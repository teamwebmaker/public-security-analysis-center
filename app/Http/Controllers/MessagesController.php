<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Jobs\SendMessageNotificationJob;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Service;

class MessagesController extends Controller
{
    protected string $resourceName = 'messages';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // if admin is coming from browser notification take id from url
        $selectedMessageId = request()->query()
            ? (int) array_keys(request()->query())[0]
            : null;


        return view('admin.messages.index', [
            'messages' => Message::orderBy('created_at', 'DESC')->paginate(6),
            'selectedMessageId' => $selectedMessageId,
            'resourceName' => $this->resourceName
        ]);
    }


    // public function store(StoreMessageRequest $request)
    // {
    //     $validated = $request->validated();

    //     $message = Message::create([
    //         'subject' => $request->filled('subject') ? $request->subject : 'without subject',
    //         ...$validated,
    //     ]);

    //     // Dispatch the job ( send the notification )
    //     dispatch(new SendMessageNotificationJob($message));

    //     return redirect()->back()->with('success', 'Your message has been sent.');
    // }


    public function store(StoreMessageRequest $request)
    {
        $validated = $request->validated();
        $locale = app()->getLocale(); // 'ka' or 'en'

        // Services lookup
        $services = [];
        if (!empty($validated['service_ids'])) {
            $rawServices = Service::whereIn('id', $validated['service_ids'])->get();

            $services = $rawServices->map(function ($service) use ($locale) {
                // Prefer localized title, fallback to English
                return $service->title->ka ?? $service->title->en ?? '';
            })->filter()->toArray();
        }

        // Subject fallback
        $subject = $request->filled('subject') ? $request->subject : 'თემის გარეშე';

        // Final message formatting
        $finalMessage = $validated['message'] ?? '';

        $formattedParts = [];

        if ($finalMessage) {
            $formattedParts[] = "📩 მესიჯი: {$finalMessage}";
        }

        if (!empty($validated['company_name'])) {
            $formattedParts[] = "🏢 კომპანია: {$validated['company_name']}";
        }

        if (!empty($services)) {
            $formattedParts[] = "🛠 სერვისები: " . implode(', ', $services);
        }

        // Join each part with a newline
        $finalMessage = implode("\n", $formattedParts);

        // Save message
        $message = Message::create([
            ...$validated,
            'subject' => $subject,
            'message' => $finalMessage,
        ]);

        // Dispatch notification
        dispatch(new SendMessageNotificationJob($message));

        $req_message = $locale == 'en' ? 'Your message has been sent.' : 'შეტყობინება გაიგზავნა წარმატებით.';
        return redirect()->back()->with('success', $req_message);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        $message->delete();
        return redirect()->route('messages.index')->with('success', 'შეტყობინება წარმატებით წაიშალა.');
    }

    public function markRead(Message $message)
    {
        if (!$message->read_at) {
            $message->forceFill(['read_at' => now()])->save();
        }

        return redirect()->route('messages.index')->with('success', 'შეტყობინება მონიშნულია წაკითხულად.');
    }
}
