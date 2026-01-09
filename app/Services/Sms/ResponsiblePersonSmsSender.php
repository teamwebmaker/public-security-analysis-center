<?php

namespace App\Services\Sms;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Log;

class ResponsiblePersonSmsSender
{
    /**
     * Send aggregated SMS per responsible person with idempotent guard.
     */
    public function sendAggregatedSmsToResponsiblePersons(
        array $byUser,
        SmsLogService $smsLogService,
        string $eventType,
        callable $messageBuilder,
        string $logMessage,
        array $logContext = [],
        array $recipientLogContext = [],
        string $recipientType = 'responsible_person',
        ?int $smsno = null
    ): void {

        $smsno = $smsno ?? SmsLog::smsnoTypeNumber('information') ?? 2;
        $occurrenceIds = [];

        foreach ($byUser as $entry) {
            // Merge to compute overall counts for logging.
            $occurrenceIds = array_merge($occurrenceIds, $entry['occurrence_ids'] ?? []);
        }

        Log::info($logMessage, array_merge([
            'event_type' => $eventType,
            'recipients' => count($byUser),
            'occurrences' => count(array_unique($occurrenceIds)),
        ], $logContext));

        foreach ($byUser as $entry) {
            $user = $entry['user'];
            $occurrenceIds = $entry['occurrence_ids'] ?? [];
            $occurrenceServiceIds = $entry['occurrence_service_ids'] ?? [];
            $destination = trim((string) $user->phone);

            // Skip if no phone.
            if ($destination === '') {
                Log::warning('Payment SMS skipped: missing phone.', [
                    'event_type' => $eventType,
                    'responsible_person_id' => $user->id,
                ]);
                continue;
            }

            // De-duplicate and remove occurrences already notified for this user.
            $occurrenceIds = array_values(array_unique($occurrenceIds));

            // Remove occurrences for services the user is not responsible for.
            $allowedServiceIds = $user->services->pluck('id')->toArray();
            $filteredOccurrenceIds = [];
            foreach ($occurrenceIds as $occurrenceId) {
                $serviceId = $occurrenceServiceIds[$occurrenceId] ?? null;
                if ($serviceId === null || !in_array($serviceId, $allowedServiceIds, true)) {
                    Log::warning('Payment SMS not sent: The responsible person is not authorized to receive SMS notifications for this occurrence.', [
                        'event_type' => $eventType,
                        'responsible_person_id' => $user->id,
                        'occurrence_id' => $occurrenceId,
                        'service_id_snapshot' => $serviceId,
                    ]);
                    continue;
                }
                $filteredOccurrenceIds[] = $occurrenceId;
            }
            $occurrenceIds = $filteredOccurrenceIds;

            // Keep only the ones not already notified.
            $occurrenceIds = array_values(array_filter(
                $occurrenceIds,
                fn($occurrenceId) => !$smsLogService->alreadySent(
                    $destination,
                    $eventType,
                    (int) $occurrenceId,
                    $recipientType
                )
            ));

            // Skip if no occurrences to send.
            if (empty($occurrenceIds)) {
                continue;
            }

            $message = $messageBuilder($occurrenceIds);

            // Send SMS and create log entries for each occurrence.
            $sendResult = $smsLogService->sendEventNotificationForEntities(
                $destination,
                $message,
                $eventType,
                $occurrenceIds,
                $recipientType,
                $smsno
            );

            $logLevel = $sendResult['failed'] ? 'warning' : 'info';
            Log::{$logLevel}('Payment SMS send result', array_merge([
                'event_type' => $eventType,
                'responsible_person_id' => $user->id,
                'phone' => $destination,
                'occurrence_ids' => $occurrenceIds,
                'status' => $sendResult['status'],
                'ok' => $sendResult['ok'],
            ], $recipientLogContext));
        }
    }
}
