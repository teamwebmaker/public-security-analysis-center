<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushController extends Controller
{
    protected string $resourceName = "push-subscriptions";

    public function index()
    {
        $subscriptions = PushSubscription::latest()->paginate(6);
        return view("admin.{$this->resourceName}.index", [
            'subscriptions' => $subscriptions,
            'resourceName' => $this->resourceName
        ]);
    }

    // Updates approved status to 1 (true)
    public function approve(PushSubscription $subscription)
    {
        $subscription->update(['approved' => true]);
        return back()->with('success', 'გამოწერა წარმატებით დამტკიცდა');
    }

    // Deletes subscription
    public function reject(PushSubscription $subscription)
    {
        $subscription->delete();
        return back()->with('success', 'გამოწერა უარყოფილია');
    }

    // Gets subscription by endpoint and deletes it
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url'
        ]);

        PushSubscription::where('subscriptions->endpoint', $request->endpoint)->delete();

        return response()->json(['message' => 'გამოწერა წარმატებით გაუქმდა']);
    }

    // Saves new subscription to database with additional info (like browser, os, ip, location)
    public function saveSubscription(Request $request)
    {
        $request->validate([
            'sub' => 'required|array',
            'sub.endpoint' => 'required|url',
            'sub.keys' => 'required|array',
            'sub.keys.p256dh' => 'required|string',
            'sub.keys.auth' => 'required|string',
        ]);

        $endpoint = $request->input('sub.endpoint');

        // Check if endpoint exists
        $exists = PushSubscription::where('subscriptions->endpoint', $endpoint)->exists();

        if ($exists) {
            return response()->json(['message' => 'Subscription already exists'], 200);
        }

        // Get browser information
        $browser = $this->getBrowser();
        $os = $this->getOS();
        $userAgent = request()->header('User-Agent');
        $ip = request()->ip();
        $location = $this->getLocation($ip);

        // Save new subscription (not approved by default)
        PushSubscription::create([
            'subscriptions' => $request->sub,
            'approved' => false, // Default to not approved
            'browser' => $browser,
            'os' => $os,
            'ip_address' => $ip,
            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null,
            'user_agent' => $userAgent
        ]);

        return response()->json(['message' => 'Subscription saved successfully. Awaiting approval.'], 200);
    }

    // Helper methods to get browser and OS info
    private function getBrowser()
    {
        $userAgent = request()->header('User-Agent');
        if (strpos($userAgent, 'Opera') || strpos($userAgent, 'OPR/'))
            return 'Opera';
        elseif (strpos($userAgent, 'Edge'))
            return 'Edge';
        elseif (strpos($userAgent, 'Chrome'))
            return 'Chrome';
        elseif (strpos($userAgent, 'Safari'))
            return 'Safari';
        elseif (strpos($userAgent, 'Firefox'))
            return 'Firefox';
        elseif (strpos($userAgent, 'MSIE') || strpos($userAgent, 'Trident/7'))
            return 'Internet Explorer';
        return 'Unknown';
    }

    private function getOS()
    {
        $userAgent = request()->header('User-Agent');
        if (strpos($userAgent, 'Windows'))
            return 'Windows';
        elseif (strpos($userAgent, 'Mac'))
            return 'Mac';
        elseif (strpos($userAgent, 'Linux'))
            return 'Linux';
        elseif (strpos($userAgent, 'Android'))
            return 'Android';
        elseif (strpos($userAgent, 'iOS'))
            return 'iOS';
        return 'Unknown';
    }


    private function getLocation($ip)
    {
        // If you have an API key, add it like ?token=YOUR_TOKEN
        $response = Http::get("https://ipinfo.io/{$ip}/json");

        if ($response->successful()) {
            $data = $response->json();

            return [
                'country' => $data['country'] ?? null,
                'city' => $data['city'] ?? null,
            ];
        }

        return [
            'country' => null,
            'city' => null,
        ];
    }

}
