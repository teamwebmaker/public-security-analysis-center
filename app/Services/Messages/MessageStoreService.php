<?php

namespace App\Services\Messages;

use App\Jobs\SendMessageNotificationJob;
use App\Models\Message;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MessageStoreService
{
    private const SYSTEM_MESSAGE_DEDUP_HOURS = 23;

    /**
     * Creates a message and dispatches a notification job.
     *
     * @param array $validated
     * @return Message
     */
    public function createAndDispatch(array $validated): Message
    {
        $validated = $this->normalizePayload($validated);


        // Services lookup
        $services = [];
        if (!empty($validated['service_ids'])) {
            $rawServices = Service::whereIn('id', $validated['service_ids'])->get();

            $services = $rawServices->map(function ($service) {
                // Prefer localized title, fallback to English
                return $service->title->ka ?? $service->title->en ?? '';
            })->filter()->toArray();
        }

        // Final message formatting
        $finalMessage = $validated['message'] ?? '';

        $formattedParts = [];
        if ($validated['source'] === 'user') {

            if ($finalMessage) {
                $formattedParts[] = "📩 მესიჯი: {$finalMessage}";
            }

            if (!empty($validated['company_name'])) {
                $formattedParts[] = "🏢 კომპანია: {$validated['company_name']}";
            }

            if (!empty($services)) {
                $formattedParts[] = "🛠 სერვისები: " . implode(', ', $services);
            }
        } else {
            $formattedParts[] = "🤖 სისტემური შეტყობინება: {$finalMessage}";
        }

        // Join each part with a newline
        $finalMessage = implode("\n", $formattedParts);

        // Keep message non-empty for DB-level NOT NULL constraints.
        if ($finalMessage === '') {
            $finalMessage = $validated['source'] === 'system'
                ? '🤖 სისტემური შეტყობინება: —'
                : '📩 მესიჯი: —';
        }

        // Save only actual DB attributes.
        $payload = [
            'full_name' => $validated['full_name'],
            'subject' => $validated['subject'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $finalMessage,
            'source' => $validated['source'],
        ];

        // Compatibility with environments where messages.source migration is missing.
        if (!$this->messagesTableHasColumn('source')) {
            unset($payload['source']);
        }

        if ($validated['source'] === 'system') {
            $existing = $this->findRecentSystemDuplicate(
                $payload['subject'] ?? '',
                $payload['message'] ?? ''
            );

            if ($existing) {
                return $existing;
            }
        }

        $message = Message::create($payload);

        // Dispatch notification
        try {
            dispatch(new SendMessageNotificationJob($message));
        } catch (Throwable $e) {
            Log::error('Message notification dispatch failed', [
                'message_id' => $message->id,
                'source' => $payload['source'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
        }

        return $message;
    }

    private function normalizePayload(array $validated): array
    {
        $validated['source'] = $validated['source'] ?? 'user';
        $validated['source'] = in_array($validated['source'], ['user', 'system'], true)
            ? $validated['source']
            : 'user';

        if ($validated['source'] === 'system') {
            $validated['full_name'] = trim((string) ($validated['full_name'] ?? 'system'));
            $validated['subject'] = trim((string) ($validated['subject'] ?? 'თემის გარეშე'));
            $validated['phone'] = trim((string) ($validated['phone'] ?? 'undefined'));
            $validated['email'] = trim((string) ($validated['email'] ?? 'undefined'));
            $validated['message'] = (string) ($validated['message'] ?? '');
        } else {
            $validated['full_name'] = trim((string) ($validated['full_name'] ?? ''));
            $validated['subject'] = trim((string) ($validated['subject'] ?? ''));
            $validated['email'] = trim((string) ($validated['email'] ?? ''));
            $validated['phone'] = isset($validated['phone']) && $validated['phone'] !== ''
                ? trim((string) $validated['phone'])
                : null;
            $validated['message'] = (string) ($validated['message'] ?? '');
        }

        if ($validated['full_name'] === '') {
            $validated['full_name'] = $validated['source'] === 'system' ? 'system' : 'unknown';
        }
        if ($validated['subject'] === '') {
            $validated['subject'] = $validated['source'] === 'system' ? 'თემის გარეშე' : 'No subject';
        }
        if ($validated['email'] === '') {
            $validated['email'] = $validated['source'] === 'system' ? 'undefined' : 'unknown@example.invalid';
        }

        return $validated;
    }

    private function findRecentSystemDuplicate(string $subject, string $message): ?Message
    {
        if (!$this->messagesTableHasColumn('source')) {
            return null;
        }

        return Message::query()
            ->where('source', 'system')
            ->where('subject', $subject)
            ->where('message', $message)
            ->where('created_at', '>=', now()->subHours(self::SYSTEM_MESSAGE_DEDUP_HOURS))
            ->latest('id')
            ->first();
    }

    private function messagesTableHasColumn(string $column): bool
    {
        static $cache = [];
        if (array_key_exists($column, $cache)) {
            return $cache[$column];
        }

        try {
            $cache[$column] = Schema::hasColumn('messages', $column);
        } catch (Throwable $e) {
            $cache[$column] = false;
            Log::warning('Unable to inspect messages table schema', [
                'column' => $column,
                'error' => $e->getMessage(),
            ]);
        }

        return $cache[$column];
    }
}
