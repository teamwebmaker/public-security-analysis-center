<?php

namespace App\Http\Controllers;

use App\Mail\EmailNotification;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{

    public function sendNotification(StoreMessageRequest $request)
    {
        $resource = $request->validated();
        $data = [
            'subject' => $request->filled('subject') ? $request->subject : 'without subject',
            'email' => $request->email,
            'description' => $request->message
        ];
        Message::create($data);
        $title = $data['subject'];
        $body = $data['description'];

        Mail::to('davitgogidze@gmail.com')->send(new EmailNotification($title, $body));

        return redirect()->back()->with('success', 'თვენი შეტყობინება წარმატებით გაიგზავნა');
    }
}
