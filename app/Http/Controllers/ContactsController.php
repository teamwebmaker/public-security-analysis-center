<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Jobs\SendContactNotificationJob;
use App\Http\Requests\StoreContactRequest;

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


    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();

        $contact = Contact::create([
            'subject' => $request->filled('subject') ? $request->subject : 'without subject',
            ...$validated,
        ]);

        // Dispatch the job ( send the notification )
        dispatch(new SendContactNotificationJob($contact));

        return redirect()->back()->with('success', 'Your message has been sent.');
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