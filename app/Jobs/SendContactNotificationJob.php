<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\PushSubscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class SendContactNotificationJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected Contact $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Handle the queued job.
     * Send a push notification to all approved subscriptions.
     */
    public function handle(): void
    {
        // VAPID keys for authentication
        $auth = [
            'VAPID' => [
                'subject' => env('VAPID_SUBJECT'),
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];

        $webPush = new WebPush($auth);

        // Get all approved subscriptions
        $subscriptions = PushSubscription::approved()->get();

        // Prepare notification payload
        $data = json_encode([
            'title' => 'ახალი შეტყობინება',
            'body' => 'ახალი შეტყობინება ' . ($this->contact->full_name ?? 'უცნობი') . '-სგან, თემა: ' . ($this->contact->subject ?? 'არ არის მითითებული'),
            'url' => route('contacts.index', $this->contact),
        ]);

        // Queue notifications
        foreach ($subscriptions as $subscription) {
            $subData = is_string($subscription->subscriptions)
                ? json_decode($subscription->subscriptions, true)
                : $subscription->subscriptions;

            if (!is_array($subData)) {
                Log::error('Invalid subscription format', [
                    'subscription' => $subscription->id
                ]);
                continue;
            }

            try {
                $webPush->queueNotification(
                    Subscription::create($subData),
                    $data
                );
            } catch (\Exception $e) {
                Log::error('Failed to queue notification', [
                    'subscription' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Send all queued notifications and handle results
        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                Log::info('Push sent successfully to: ' . $report->getEndpoint());
            } else {
                Log::error('Push failed for: ' . $report->getEndpoint() . ' Reason: ' . $report->getReason());

                // Clean up expired subscriptions
                if ($report->isSubscriptionExpired()) {
                    PushSubscription::where('subscriptions->endpoint', $report->getEndpoint())->delete();
                }
            }
        }
    }
}
