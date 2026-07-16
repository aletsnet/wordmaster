<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
    public function index()
    {
        $submissions = ContactSubmission::latest()->paginate(20);
        return view('admin.contact-submissions.index', compact('submissions'));
    }

    public function show(ContactSubmission $contact_submission)
    {
        $contact_submission->markAsRead();
        return view('admin.contact-submissions.show', compact('contact_submission'));
    }

    public function destroy(ContactSubmission $contact_submission)
    {
        $contact_submission->delete();
        return redirect()->route('admin.contact-submissions.index')
            ->with('success', __('messages.contact_deleted'));
    }
}
