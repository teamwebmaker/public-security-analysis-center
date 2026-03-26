<?php

namespace App\Services\Sms;

use App\Models\AdminNumber;
use App\Models\SmsLog;
use App\Models\TaskOccurrence;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminSmsNotifier
{
    public function __construct(private SmsLogService $smsLogService)
    {
    }

    /**
     * Notify all admin phone numbers when a worker starts a task occurrence.
     */
    public function notifyTaskStarted(TaskOccurrence $occurrence, User $worker): void
    {
        $eventType = 'task_started';
        $entityId = (int) $occurrence->id;
        $message = $this->buildTaskStartedMessage($occurrence, $worker);

        $this->notifyAdminsForSingleEntity(
            $eventType,
            $entityId,
            $message
        );
    }

    /**
     * Notify all admin phone numbers when a worker finishes a task occurrence.
     */
    public function notifyTaskFinished(TaskOccurrence $occurrence, User $worker): void
    {
        $eventType = 'task_finished';
        $entityId = (int) $occurrence->id;
        $message = $this->buildTaskFinishedMessage($occurrence, $worker);

        $this->notifyAdminsForSingleEntity(
            $eventType,
            $entityId,
            $message
        );
    }

    /**
     * Notify all admin phone numbers when a responsible person receives an overdue-payment SMS.
     *
     * @param array<int, int|string> $occurrenceIds
     */
    public function notifyResponsiblePersonOverdue(User $responsiblePerson, array $occurrenceIds): void
    {
        $eventType = 'debt_overdue_service_suspended';
        $occurrenceIds = array_values(array_unique(array_filter(array_map('intval', $occurrenceIds))));

        if (empty($occurrenceIds)) {
            return;
        }

        $message = $this->buildResponsiblePersonOverdueMessage($responsiblePerson, $occurrenceIds);
        $recipientType = 'admin';

        foreach ($this->adminDestinations() as $destination) {
            try {
                $unsentIds = array_values(array_filter(
                    $occurrenceIds,
                    fn($occurrenceId) => !$this->smsLogService->alreadySent(
                        $destination,
                        $eventType,
                        (int) $occurrenceId,
                        $recipientType
                    )
                ));

                if (empty($unsentIds)) {
                    continue;
                }

                $result = $this->smsLogService->sendEventNotificationForEntities(
                    $destination,
                    $message,
                    $eventType,
                    $unsentIds,
                    $recipientType,
                    SmsLog::smsnoTypeNumber('information') ?? 2
                );

                if (($result['failed'] ?? false) === true) {
                    Log::warning('Admin overdue SMS notification failed', [
                        'destination' => $destination,
                        'responsible_person_id' => $responsiblePerson->id,
                        'occurrence_ids' => $unsentIds,
                    ]);
                }
            } catch (Throwable $e) {
                Log::error('Admin overdue SMS notification unexpected failure', [
                    'destination' => $destination,
                    'responsible_person_id' => $responsiblePerson->id,
                    'occurrence_ids' => $occurrenceIds,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function notifyAdminsForSingleEntity(string $eventType, int $entityId, string $message): void
    {
        if ($entityId <= 0) {
            return;
        }

        $recipientType = 'admin';
        $smsno = SmsLog::smsnoTypeNumber('information') ?? 2;

        foreach ($this->adminDestinations() as $destination) {
            try {
                $alreadySent = $this->smsLogService->alreadySent(
                    $destination,
                    $eventType,
                    $entityId,
                    $recipientType
                );

                if ($alreadySent) {
                    continue;
                }

                $result = $this->smsLogService->sendEventNotification(
                    $destination,
                    $message,
                    $eventType,
                    $entityId,
                    $recipientType,
                    $smsno
                );

                if (($result['failed'] ?? false) === true) {
                    Log::warning('Admin SMS notification failed', [
                        'destination' => $destination,
                        'event_type' => $eventType,
                        'entity_id' => $entityId,
                    ]);
                }
            } catch (Throwable $e) {
                Log::error('Admin SMS notification unexpected failure', [
                    'destination' => $destination,
                    'event_type' => $eventType,
                    'entity_id' => $entityId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * @return array<int, string>
     */
    protected function adminDestinations(): array
    {
        return AdminNumber::query()
            ->select('phone')
            ->whereNotNull('phone')
            ->pluck('phone')
            ->map(fn($phone) => trim((string) $phone))
            ->filter(fn($phone) => $phone !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function buildTaskStartedMessage(TaskOccurrence $occurrence, User $worker): string
    {
        $startedAt = optional($occurrence->start_date)->format('d.m.Y H:i') ?? now()->format('d.m.Y H:i');
        $workerName = trim((string) ($worker->full_name ?? 'უცნობი'));
        $branch = trim((string) ($occurrence->branch_name_snapshot ?? '—'));
        $service = trim((string) ($occurrence->service_name_snapshot ?? '—'));

        return "👷 მუშამ დაიწყო სამუშაო\n"
            . "მუშა: {$workerName}\n"
            . "სამუშაო: #{$occurrence->id}\n"
            . "ფილიალი: {$branch}\n"
            . "სერვისი: {$service}\n"
            . "დრო: {$startedAt}";
    }

    private function buildTaskFinishedMessage(TaskOccurrence $occurrence, User $worker): string
    {
        $finishedAt = optional($occurrence->end_date)->format('d.m.Y H:i') ?? now()->format('d.m.Y H:i');
        $workerName = trim((string) ($worker->full_name ?? 'უცნობი'));
        $branch = trim((string) ($occurrence->branch_name_snapshot ?? '—'));
        $service = trim((string) ($occurrence->service_name_snapshot ?? '—'));

        return "✅ მუშამ დაასრულა სამუშაო\n"
            . "მუშა: {$workerName}\n"
            . "სამუშაო: #{$occurrence->id}\n"
            . "ფილიალი: {$branch}\n"
            . "სერვისი: {$service}\n"
            . "დრო: {$finishedAt}";
    }

    /**
     * @param array<int, int> $occurrenceIds
     */
    private function buildResponsiblePersonOverdueMessage(User $responsiblePerson, array $occurrenceIds): string
    {
        $name = trim((string) ($responsiblePerson->full_name ?? 'უცნობი'));
        $phone = trim((string) ($responsiblePerson->phone ?? '—'));
        $list = implode(', ', array_map(fn($id) => "#{$id}", $occurrenceIds));

        return "⚠️ ვადაგადაცილებულად მოინიშნა\n"
            . "სამუშაოები: {$list}"
            . "პასუხისმგებელი პირი: {$name}\n"
            . "ნომერი: {$phone}\n";

    }
}

