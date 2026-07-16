<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Models\ContactSubmission;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|max:50',
            'message' => 'required|max:5000',
        ]);

        ContactSubmission::create($data);

        $to = Option::getValue('contact_email', config('mail.from.address'));

        Mail::to($to)->send(new ContactMail($data));

        return redirect('/contacto')
            ->with('success', __('front.contact_sent'));
    }
}
