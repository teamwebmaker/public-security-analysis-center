<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use App\Services\SubscriptionMetaService;

class PushController extends Controller
{
    protected string $resourceName = 'push-subscriptions';

    /**
     * Displays the subscription list view with pagination.
     */
    public function index()
    {
        return view("admin.{$this->resourceName}.index", [
            'subscriptions' => PushSubscription::latest()->paginate(6),
            'resourceName' => $this->resourceName,
        ]);
    }

    /**
     * Approves a specific subscription (sets 'approved' to true).
     */
    public function approve(PushSubscription $subscription)
    {
        $subscription->update(['approved' => true]);
        return back()->with('success', 'გამოწერა წარმატებით დამტკიცდა');
    }

    /**
     * Rejects a specific subscription (deletes it).
     */
    public function reject(PushSubscription $subscription)
    {
        $subscription->delete();
        return back()->with('success', 'გამოწერა უარყოფილია');
    }

    /**
     * Unsubscribes a user by endpoint (removes subscription from DB).
     */
    public function unsubscribe(Request $request)
    {
        $endpoint = $this->validateEndpoint($request);
        $this->deleteByEndpoint($endpoint);

        return response()->json(['message' => 'გამოწერა წარმატებით გაუქმდა']);
    }

    /**
     * Checks if a given subscription exists based on the endpoint.
     */
    public function checkSubscription(Request $request)
    {
        $endpoint = $this->validateEndpoint($request);
        return response()->json(['exists' => $this->existsByEndpoint($endpoint)]);
    }

    /**
     * Saves a new push subscription with metadata (browser, OS, location).
     */
    public function saveSubscription(Request $request, SubscriptionMetaService $meta)
    {
        $userAgent = $request->userAgent();
        $ip = $request->ip();
        $browser = $meta->detectBrowser($userAgent);
        $os = $meta->detectOS($userAgent);
        $location = $meta->getLocation($ip);

        $request->validate([
            'sub' => 'required|array',
            'sub.endpoint' => 'required|url',
            'sub.keys' => 'required|array',
            'sub.keys.p256dh' => 'required|string',
            'sub.keys.auth' => 'required|string',
        ]);

        $endpoint = $request->input('sub.endpoint');

        // Prevent duplicate subscriptions
        if ($this->existsByEndpoint($endpoint)) {
            return response()->json(['message' => 'Subscription already exists'], 200);
        }

        // Save the new subscription with collected metadata
        PushSubscription::create([
            'subscriptions' => $request->sub,
            'approved' => false,
            'browser' => $browser,
            'os' => $os,
            'ip_address' => $ip,
            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null,
            'user_agent' => $userAgent,
        ]);

        return response()->json(['message' => 'Subscription saved successfully. Awaiting approval.'], 200);
    }

    // ========== 🔧 PRIVATE HELPERS ==========

    /**
     * Validates and returns a subscription endpoint from the request.
     */
    private function validateEndpoint(Request $request): string
    {
        return $request->validate([
            'endpoint' => 'required|url',
        ])['endpoint'];
    }

    /**
     * Checks if a subscription exists by endpoint inside the JSON field.
     */
    private function existsByEndpoint(string $endpoint): bool
    {
        return PushSubscription::where('subscriptions->endpoint', $endpoint)->exists();
    }

    /**
     * Deletes a subscription by endpoint from the JSON field.
     */
    private function deleteByEndpoint(string $endpoint): void
    {
        PushSubscription::where('subscriptions->endpoint', $endpoint)->delete();
    }
}
