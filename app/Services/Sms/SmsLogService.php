<?php

namespace App\Services\Sms;

use App\Models\SmsLog;

class SmsLogService
{
    public function __construct(private SenderGeClient $sender)
    {
    }



    /**
     * Check if a message was already sent to the given destination for the given event type
     * and entity id using SmsLog table.
     *
     * @param string $destination Georgian mobile (5xxxxxxxx)
     * @param string $eventType the name of the event (e.g. 'debt_due_2_days')
     * @param int $entityId the id of the entity (e.g. task occurrence id)
     * @param string $recipientType the type of the recipient (e.g. 'responsible_person')
     *
     * @return bool true if the message was already sent, false otherwise
     */
    public function alreadySent(
        string $destination,
        string $eventType,
        int $entityId,
        string $recipientType
    ): bool {
        $destination = $this->normalizeDestination($destination);
        $pending = SmsLog::statusNumber('pending') ?? 0;
        $delivered = SmsLog::statusNumber('delivered') ?? 1;

        return SmsLog::query()
            ->where('destination', $destination)
            ->where('event_type', $eventType)
            ->where('entity_id', $entityId)
            ->where('recipient_type', $recipientType)
            // Treat pending/delivered as "already sent" for idempotency.
            ->whereIn('status', [$pending, $delivered])
            ->exists();
    }

    public function sendEventNotification(
        string $destination,
        string $content,
        string $eventType,
        int $entityId,
        string $recipientType,
        int $smsno = 2
    ): array {
        $response = $this->sendEventNotificationForEntities(
            $destination,
            $content,
            $eventType,
            [$entityId],
            $recipientType,
            $smsno
        );

        return [
            'sms_log' => $response['sms_logs'][0] ?? null,
            'result' => $response['result'],
            'ok' => $response['ok'],
            'failed' => $response['failed'],
            'status' => $response['status'],
        ];
    }

    public function sendEventNotificationForEntities(
        string $destination,
        string $content,
        string $eventType,
        array $entityIds,
        string $recipientType,
        int $smsno = 2
    ): array {
        $destination = $this->normalizeDestination($destination);

        // Normalize entity IDs: ints only, drop empties, de-dupe, and reindex.
        $entityIds = array_values(array_unique(array_filter(array_map('intval', $entityIds))));
        $result = [];

        if (empty($entityIds)) {
            return [
                'sms_logs' => [],
                'result' => [],
                'ok' => false,
                'failed' => true,
                'status' => SmsLog::statusNumber('undelivered') ?? 2,
            ];
        }

        try {
            // Send sms 
            $result = $this->sender->sendSms($smsno, $destination, $content);
        } catch (\Throwable $e) {
            $result = [
                'ok' => false,
                'http_status' => null,
                'data' => [
                    'message' => $e->getMessage(),
                ],
                'raw' => null,
                'operation' => 'sendSms',
                'exception' => get_class($e),
            ];
        }

        $statusId = data_get($result, 'data.data.0.statusId');
        $messageId = data_get($result, 'data.data.0.messageId');
        $statusId = is_numeric($statusId) ? (int) $statusId : null;

        $undelivered = SmsLog::statusNumber('undelivered') ?? 2;
        $pending = SmsLog::statusNumber('pending') ?? 0;

        $ok = (bool) data_get($result, 'ok', false);
        $failed = (!$ok) || ($statusId !== null && $statusId === $undelivered);
        $finalStatus = $statusId ?? ($failed ? $undelivered : $pending);

        $smsLogs = [];
        foreach ($entityIds as $entityId) {
            // Create a per-entity log entry so each occurrence is tracked independently.
            $smsLogs[] = SmsLog::create([
                'provider' => 'sender_ge',
                'provider_message_id' => $messageId,
                'destination' => $destination,
                'content' => $content,
                'event_type' => $eventType,
                'entity_id' => $entityId,
                'recipient_type' => $recipientType,
                'smsno' => $smsno,
                'status' => $finalStatus,
                'provider_response' => $failed ? $result : null,
                'sent_at' => $ok ? now() : null,
            ]);
        }

        return [
            'sms_logs' => $smsLogs,
            'result' => $result,
            'ok' => $ok,
            'failed' => $failed,
            'status' => $finalStatus,
        ];
    }

    private function normalizeDestination(string $destination): string
    {
        $cleaned = preg_replace('/^(\+?995)/', '', $destination);

        return trim($cleaned ?? $destination);
    }
}
