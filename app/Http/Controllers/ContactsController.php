<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

class ContactsController extends Controller
{
    protected string $resourceName = 'contacts';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.contacts.index', [
            'contacts' => Contact::orderBy('created_at', 'DESC')->paginate(10),
            'resourceName' => $this->resourceName
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        // Validate the request
        $validated = $request->validated();
        // Prepare data for database
        $data = [
            'subject' => $request->filled('subject') ? $request->subject : 'without subject',
            ...$validated
        ];

        // Store in DB
        Contact::create($data);

        // Send email notification
        // Mail::to('davitgogidze@gmail.com')->send(new EmailNotification($data['subject'], $data['description']));

        // Redirect back with success message
        return redirect()->back()->with('success', 'თქვენი შეტყობინება წარმატებით გაიგზავნა');
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
