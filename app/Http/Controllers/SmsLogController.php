<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSmsLogRequest;
use App\Http\Requests\UpdateSmsLogRequest;
use App\Models\SmsLog;
use App\Presenters\TableHeaderDataPresenter;
use App\Presenters\TableRowDataPresenter;
use App\Services\Sms\SenderGeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SmsLogController extends CrudController
{
    protected string $modelClass = SmsLog::class;
    protected string $contextField = "sms_log";
    protected string $contextFieldPlural = "sms_logs";
    protected string $resourceName = "sms_logs";

    protected int $perPage = 10;
    private const FILTERS = [
        'status' => [
            'label' => 'სტატუსი',
            'options' => [
                0 => 'მოლოდინში',
                1 => 'გაგზავნილი',
                2 => 'ვერ გაიგზავნა',
            ],
        ],
        'smsno' => [
            'label' => 'ტიპი',
            'options' => [
                1 => 'რეკლამა',
                2 => 'ინფორმაცია',
            ],
        ],
        'provider' => [
            'label' => 'პროვაიდერი',
            'options' => [
                'sender_ge' => 'sender_ge',
            ],
        ],
    ];

    private const SORTABLE_MAP = [
        'გაგზავნის დრო' => 'sent_at',
        'ჩანაწერის დრო' => 'created_at',
    ];
    /**
     * Display a listing of the resource with search and filters.
     */

    public function index(Request $request)
    {
        $smsLogs = $this->buildIndexQuery()
            ->paginate($this->perPage)
            ->appends($request->query());

        $smsLogHeaders = TableHeaderDataPresenter::smsLogHeaders();

        $undeliveredStatusNumber = SmsLog::statusNumber('undelivered');
        $smsLogRows = $smsLogs->map(
            fn($smsLog) =>
            TableRowDataPresenter::smsLogRow(
                $smsLog,
                fn($smsLog) => $this->renderResendButton($smsLog, $undeliveredStatusNumber)
            )
        );

        return view("admin.{$this->resourceName}.index", [
            $this->contextFieldPlural => $smsLogs,
            'resourceName' => $this->resourceName,
            'smsLogHeaders' => $smsLogHeaders,
            'smsLogRows' => $smsLogRows,
            'filters' => self::FILTERS,
            'sortableMap' => self::SORTABLE_MAP,
            'totalSmsLogs' => SmsLog::query()->count(),
        ]);
    }

    /**
     * Refresh statuses for latest SMS logs by querying provider delivery reports.
     */
    public function syncStatuses(Request $request, SenderGeClient $sender)
    {
        $validated = $request->validate([
            'limit' => ['required', 'integer', Rule::in([10, 20, 50])],
        ]);

        $limit = (int) $validated['limit'];
        $logs = SmsLog::query()
            ->where('provider', 'sender_ge')
            ->whereNotNull('provider_message_id')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        $checked = 0;
        $updated = 0;
        $unchanged = 0;
        $skipped = 0;
        $errors = [];
        $statusCache = [];

        foreach ($logs as $smsLog) {
            $checked++;
            $messageId = trim((string) $smsLog->provider_message_id);

            if ($messageId === '') {
                $skipped++;
                continue;
            }

            if (!array_key_exists($messageId, $statusCache)) {
                try {
                    $report = $sender->getDeliveryReport($messageId);
                } catch (Throwable $e) {
                    $statusCache[$messageId] = null;
                    $errors[] = [
                        'sms_log_id' => $smsLog->id,
                        'message' => 'Delivery report request failed.',
                    ];

                    Log::warning('SMS delivery report request threw exception', [
                        'sms_log_id' => $smsLog->id,
                        'provider_message_id' => $messageId,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }

                $httpStatus = (int) data_get($report, 'http_status', 0);
                if ($httpStatus === 401) {
                    $statusCache[$messageId] = null;
                    $errors[] = [
                        'sms_log_id' => $smsLog->id,
                        'message_id' => $messageId,
                        'http_status' => 401,
                        'message' => 'SMS პროვაიდერის ავტორიზაცია ვერ მოხერხდა (401).',
                    ];
                    Log::warning('SMS delivery report unauthorized (401)', [
                        'sms_log_id' => $smsLog->id,
                        'provider_message_id' => $messageId,
                    ]);
                    continue;
                }

                if ($httpStatus === 403) {
                    $statusCache[$messageId] = null;
                    $errors[] = [
                        'sms_log_id' => $smsLog->id,
                        'message_id' => $messageId,
                        'http_status' => 403,
                        'message' => 'SMS პროვაიდერმა წვდომა შეზღუდა (403).',
                    ];
                    Log::warning('SMS delivery report forbidden (403)', [
                        'sms_log_id' => $smsLog->id,
                        'provider_message_id' => $messageId,
                    ]);
                    continue;
                }

                if ($httpStatus === 503) {
                    $statusCache[$messageId] = null;
                    $errors[] = [
                        'sms_log_id' => $smsLog->id,
                        'message_id' => $messageId,
                        'http_status' => 503,
                        'message' => 'Provider temporarily unavailable (503).',
                    ];
                    Log::warning('SMS delivery report unavailable (503)', [
                        'sms_log_id' => $smsLog->id,
                        'provider_message_id' => $messageId,
                    ]);
                    continue;
                }

                if (!(bool) data_get($report, 'ok', false)) {
                    $statusCache[$messageId] = null;
                    $errors[] = [
                        'sms_log_id' => $smsLog->id,
                        'message_id' => $messageId,
                        'http_status' => $httpStatus,
                        'message' => 'Provider response is not OK.',
                    ];
                    Log::warning('SMS delivery report not OK', [
                        'sms_log_id' => $smsLog->id,
                        'provider_message_id' => $messageId,
                        'http_status' => $httpStatus,
                        'raw' => data_get($report, 'raw'),
                    ]);
                    continue;
                }

                $statusCache[$messageId] = $this->extractStatusId($report);
            }

            $newStatus = $statusCache[$messageId];
            if ($newStatus === null) {
                $skipped++;
                continue;
            }

            if ((int) $smsLog->status === $newStatus) {
                $unchanged++;
                continue;
            }

            $smsLog->update([
                'status' => $newStatus,
            ]);
            $updated++;
        }

        return response()->json([
            'message' => 'სტატუსების გადამოწმება დასრულდა.',
            'summary' => [
                'requested' => $limit,
                'checked' => $checked,
                'updated' => $updated,
                'unchanged' => $unchanged,
                'skipped' => $skipped,
                'errors' => count($errors),
            ],
            'errors' => $errors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSmsLogRequest $request)
    {
        $data = $this->normalizePayload($request->validated());
        $this->modelClass::create($data);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "SMS ლოგი დაემატა წარმატებით");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSmsLogRequest $request, SmsLog $sms_log)
    {
        $data = $request->validated();
        $sms_log->update($data);

        return redirect()
            ->back()
            ->with("success", "SMS ლოგი განახლდა წარმატებით");
    }

    /**
     * Build the base query for index listing.
     */
    protected function buildIndexQuery()
    {
        return QueryBuilder::for(SmsLog::class)
            ->allowedSorts(['created_at', 'sent_at', 'status', 'smsno'])
            ->defaultSort('-created_at')
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $value = is_array($value) ? $value[0] : $value;
                    $like = '%' . $value . '%';

                    $query->where(function ($q) use ($like) {
                        $q->where('provider', 'LIKE', $like)
                            ->orWhere('destination', 'LIKE', $like)
                            ->orWhere('provider_message_id', 'LIKE', $like)
                            ->orWhere('content', 'LIKE', $like);
                    });
                }),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('smsno'),
                AllowedFilter::exact('provider'),
            ]);
    }

    /**
     * Builds the "resend" button HTML.
     * Keeps controller logic cleaner and avoids repeated work inside the map.
     */
    private function renderResendButton($smsLog, ?int $undeliveredStatusNumber): string
    {
        // $smsLog->status is a number in DB
        $canResend = $undeliveredStatusNumber !== null
            && ($smsLog->status === $undeliveredStatusNumber);

        return view('admin.sms_logs.partials.actions', [
            'smsLog' => $smsLog,
            'canResend' => $canResend,
        ])->render();
    }
    /**
     * Normalize JSON payload fields from form inputs.
     */
    private function normalizePayload(array $data): array
    {
        if (!empty($data['provider_response'])) {
            $data['provider_response'] = json_decode($data['provider_response'], true);
        }

        return $data;
    }

    private function extractStatusId(array $report): ?int
    {
        $statusId = data_get($report, 'data.data.0.statusId');
        if ($statusId === null) {
            $statusId = data_get($report, 'data.0.statusId');
        }
        if ($statusId === null) {
            $statusId = data_get($report, 'data.statusId');
        }

        if (!is_numeric($statusId)) {
            return null;
        }

        $statusId = (int) $statusId;

        return in_array($statusId, [0, 1, 2], true) ? $statusId : null;
    }
}
