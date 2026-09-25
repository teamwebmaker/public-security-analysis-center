<?php

namespace App\Services\Sms;

use App\Services\Messages\MessageStoreService;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsFailureSystemNotifier
{
    public function __construct(private MessageStoreService $messageStoreService)
    {
    }

    /**
     * Store an actionable system message when an SMS could not be sent.
     *
     * @param array<int, string> $lines
     */
    public function report(string $subject, array $lines, array $context = []): void
    {
        $message = implode("\n", array_values(array_filter(
            array_map(fn ($line) => trim((string) $line), $lines),
            fn ($line) => $line !== ''
        )));

        if ($message === '') {
            return;
        }

        try {
            $payload = [
                'source' => 'system',
                'subject' => $subject,
                'message' => $message,
                'context' => $context,
            ];

            if (! empty($context['sms_log_id'])) {
                $payload['action_url'] = route('sms_logs.index', [
                    'filter' => ['search' => (string) $context['sms_log_id']],
                ], false);
                $payload['action_label'] = 'SMS ლოგის გახსნა';
            }

            $this->messageStoreService->createAndDispatch($payload);
        } catch (Throwable $e) {
            Log::error('Failed to create SMS failure system message', [
                'subject' => $subject,
                ...$context,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
