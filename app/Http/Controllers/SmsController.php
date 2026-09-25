<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Services\Sms\SenderGeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsController extends Controller
{
    // With Temp Logs
    public function send(Request $request, SenderGeClient $sender)
    {
        Log::debug('SMS send request started', [
            'request_data' => $request->all(),
        ]);

        $validated = $request->validate([
            'smsno' => ['required', 'integer', 'in:1,2'],
            'destination' => ['required', 'regex:/^(\+?995)?5\d{8}$/'],
            'content' => ['required', 'string', 'max:1000'],
            'sms_log_id' => ['nullable', 'integer', 'exists:sms_logs,id'],
        ]);

        Log::debug('Request validated', $validated);

        $undeliveredStatus = SmsLog::statusNumber('undelivered');

        Log::debug('Undelivered status resolved', [
            'undelivered_status' => $undeliveredStatus,
        ]);

        $smsLog = null;

        if (!empty($validated['sms_log_id'])) {
            Log::debug('sms_log_id provided', [
                'sms_log_id' => $validated['sms_log_id'],
            ]);

            $smsLog = SmsLog::find($validated['sms_log_id']);

            Log::debug('SmsLog loaded', [
                'sms_log' => $smsLog,
            ]);

            if (
                $undeliveredStatus !== null &&
                (int) $smsLog->status !== $undeliveredStatus
            ) {
            Log::info('Resend blocked due to status mismatch', [
                'current_status' => $smsLog->status,
                'undelivered_status' => $undeliveredStatus,
            ]);

                return back()->with('error', 'მესიჯის თავიდან გაგზავნა შეუძლებელია.');
            }
        }

        Log::debug('Sending SMS to provider', [
            'smsno' => (int) $validated['smsno'],
            'destination' => $validated['destination'],
            'content_length' => strlen($validated['content']),
        ]);
        Log::debug('Before sendSms');

        $result = $sender->sendSms(
            (int) $validated['smsno'],
            $validated['destination'],
            $validated['content']
        );

        Log::debug('After sendSms');
        Log::debug('Provider response received', [
            'response' => $result,
        ]);

        $statusId = data_get($result, 'data.data.0.statusId');
        $messageId = data_get($result, 'data.data.0.messageId');

        Log::debug('Parsed provider response', [
            'statusId_raw' => $statusId,
            'messageId' => $messageId,
        ]);

        $statusId = is_numeric($statusId) ? (int) $statusId : null;
        $ok = (bool) data_get($result, 'ok', false);

        $failed = (!$ok) || (
            $undeliveredStatus !== null &&
            $statusId === $undeliveredStatus
        );

        Log::debug('Computed send result', [
            'ok' => $ok,
            'statusId' => $statusId,
            'failed' => $failed,
        ]);

        if ($smsLog) {
            $update = [];

            if ($statusId !== null) {
                $update['status'] = $statusId;
            }

            if (!empty($messageId) && $messageId !== $smsLog->provider_message_id) {
                $update['provider_message_id'] = $messageId;
            }

            if ($ok) {
                $update['sent_at'] = now();
            }

            if ($failed) {
                $update['provider_response'] = $result;
            }

            Log::debug('Prepared SmsLog update payload', [
                'update' => $update,
            ]);

            if ($update) {
                $smsLog->update($update);

                Log::debug('SmsLog updated', [
                    'sms_log_id' => $smsLog->id,
                ]);
            }
        }

        if ($failed) {
            Log::warning('SMS sending failed', [
                'response' => $result,
            ]);

            return back()->with('error', 'მესიჯი ვერ გაიგზავნა');
        }

        Log::debug('SMS sent successfully');

        return back()->with('success', 'SMS გაგზავნა წარმატებით');
    }


    public function balance(Request $request, SenderGeClient $sender)
    {
        $cacheKey = 'senderge.sms-balance.current';
        $lastKnownBalanceKey = 'senderge.sms-balance.last-known';
        $cacheMinutes = max(1, (int) config('services.senderge.balance_cache_minutes', 15));
        $forceRefresh = $request->boolean('refresh');
        $cachedBalance = Cache::get($cacheKey);

        if (! $forceRefresh && $this->isValidBalanceSnapshot($cachedBalance)) {
            return response()->json([
                'ok' => true,
                ...$cachedBalance,
                'cached' => true,
            ]);
        }

        try {
            $result = $sender->getBalance();
            $balance = data_get($result, 'data.data.0.balance');
            $overdraft = data_get($result, 'data.data.0.overdraft');

            if (! ($result['ok'] ?? false) || ! is_numeric($balance)) {
                throw new \RuntimeException('Sender.Ge returned an invalid balance response.');
            }

            $snapshot = [
                'balance' => (float) $balance,
                'overdraft' => is_numeric($overdraft) ? (float) $overdraft : null,
                'fetched_at' => now()->toIso8601String(),
            ];

            Cache::put($cacheKey, $snapshot, now()->addMinutes($cacheMinutes));
            Cache::forever($lastKnownBalanceKey, $snapshot);

            return response()->json([
                'ok' => true,
                ...$snapshot,
                'cached' => false,
            ]);
        } catch (Throwable $e) {
            Log::warning('Unable to fetch Sender.Ge SMS balance.', [
                'error' => $e->getMessage(),
            ]);

            $lastKnownBalance = Cache::get($lastKnownBalanceKey);

            if ($this->isValidBalanceSnapshot($lastKnownBalance)) {
                return response()->json([
                    'ok' => true,
                    ...$lastKnownBalance,
                    'cached' => true,
                    'stale' => true,
                ]);
            }

            return response()->json([
                'ok' => false,
                'message' => 'SMS ბალანსის მიღება ვერ მოხერხდა.',
            ], 503);
        }
    }

    private function isValidBalanceSnapshot(mixed $snapshot): bool
    {
        return is_array($snapshot) && is_numeric($snapshot['balance'] ?? null);
    }

    public function report(Request $request, SenderGeClient $sender)
    {
        $validated = $request->validate([
            'messageId' => ['required', 'string'],
        ]);

        return response()->json($sender->getDeliveryReport($validated['messageId']));
    }
}
