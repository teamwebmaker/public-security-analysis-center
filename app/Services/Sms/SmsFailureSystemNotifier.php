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
            $this->messageStoreService->createAndDispatch([
                'source' => 'system',
                'subject' => $subject,
                'message' => $message,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to create SMS failure system message', [
                'subject' => $subject,
                ...$context,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
