<?php

namespace App\Services\Sms;

use App\Models\Order;
use App\Models\SmsLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderSmsNotifier
{
    public function __construct(
        private SmsLogService $smsLogService,
        private SmsFailureSystemNotifier $failureNotifier
    ) {
    }

    public function notifyCreated(Order $order, ?Collection $participants = null): void
    {
        $order->loadMissing('branch');
        $participants ??= $order->userParticipants()->with('user.role')->get();

        $missingRecipients = [];

        foreach ($participants as $participant) {
            $participant->loadMissing('user.role');
            $user = $participant->user;

            if (! $user) {
                $missingRecipients[] = 'მონაწილებელი მომხმარებელი ვერ მოიძებნა';
                continue;
            }

            if (trim((string) $user->phone) === '') {
                $missingRecipients[] = "ტელეფონი არ არის მითითებული: {$user->full_name}";
                continue;
            }

            try {
                if ($this->smsLogService->alreadySent(
                    $user->phone,
                    'order_created',
                    $order->id,
                    $user->getRoleName()
                )) {
                    continue;
                }

                $this->smsLogService->sendEventNotification(
                    $user->phone,
                    $this->message($order),
                    'order_created',
                    $order->id,
                    $user->getRoleName(),
                    SmsLog::smsnoTypeNumber('information') ?? 2
                );
            } catch (Throwable $e) {
                Log::error('Order SMS notification failed', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($missingRecipients !== []) {
            $this->failureNotifier->report(
                'ბრძანების SMS ვერ გაიგზავნა',
                array_merge([
                    'ბრძანების მონაწილეებისთვის SMS სრულად ვერ გაიგზავნა.',
                    "ბრძანება: #{$order->id} ({$order->title})",
                ], array_values(array_unique($missingRecipients))),
                ['order_id' => $order->id, 'event_type' => 'order_created']
            );
        }
    }

    private function message(Order $order): string
    {
        return "📄 ახალი ბრძანება\n"
            . "ბრძანება: {$order->title}\n"
            . "ფილიალი: {$order->branch->name}\n"
            . 'გთხოვთ, შეხვიდეთ სისტემაში და დაადასტუროთ დოკუმენტი.';
    }
}
