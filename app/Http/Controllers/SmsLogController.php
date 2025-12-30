<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSmsLogRequest;
use App\Http\Requests\UpdateSmsLogRequest;
use App\Models\SmsLog;
use App\Presenters\TableHeaderDataPresenter;
use App\Presenters\TableRowDataPresenter;
use Illuminate\Http\Request;
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
}
