<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Jobs\SendContactNotificationJob;
use App\Http\Requests\StoreContactRequest;
use App\Models\Service;

class ContactsController extends Controller
{
    protected string $resourceName = 'contacts';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // if admin is coming from browser notification take id from url
        $selectedContactId = request()->query()
            ? (int) array_keys(request()->query())[0]
            : null;


        return view('admin.contacts.index', [
            'contacts' => Contact::orderBy('created_at', 'DESC')->paginate(6),
            'selectedContactId' => $selectedContactId,
            'resourceName' => $this->resourceName
        ]);
    }


    // public function store(StoreContactRequest $request)
    // {
    //     $validated = $request->validated();

    //     $contact = Contact::create([
    //         'subject' => $request->filled('subject') ? $request->subject : 'without subject',
    //         ...$validated,
    //     ]);

    //     // Dispatch the job ( send the notification )
    //     dispatch(new SendContactNotificationJob($contact));

    //     return redirect()->back()->with('success', 'Your message has been sent.');
    // }


    public function store(StoreContactRequest $request)
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

        // Build extra info
        $extraInfoParts = [];

        if (!empty($validated['company_name'])) {
            $extraInfoParts[] = "🏢 კომპანია: {$validated['company_name']}";
        }

        if (!empty($services)) {
            $extraInfoParts[] = "🛠 სერვისები: " . implode(', ', $services);
        }

        $extraInfo = implode("\n", $extraInfoParts);

        // Final message
        $finalMessage = $validated['message'] ?? '';

        if ($finalMessage && $extraInfo) {
            $finalMessage =
                "📩 მესიჯი:\n{$finalMessage}\n\n" .
                "───\n" .
                "{$extraInfo}";
        } elseif (!$finalMessage) {
            $finalMessage = $extraInfo;
        }

        $contact = Contact::create([
            ...$validated,
            'subject' => $subject,
            'message' => $finalMessage,
        ]);

        dispatch(new SendContactNotificationJob($contact));

        $req_message = $locale == 'en' ? 'Your message has been sent.' : 'შეტყობინება წარმატებით გაგზავნა.';
        return redirect()->back()->with('success', $req_message);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'შეტყობინება წარმატებით წაიშალა.');
    }
}