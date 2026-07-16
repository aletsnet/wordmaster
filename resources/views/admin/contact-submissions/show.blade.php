@extends('admin.layouts.app')
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.contact-submissions.index') }}" class="text-blue-600 hover:text-blue-900">&larr; {{ __('admin.back_to_submissions') }}</a>
</div>

<div class="bg-white rounded-lg shadow p-8 max-w-2xl">
    <div class="flex justify-between items-start mb-6">
        <h2 class="text-2xl font-bold">{{ __('admin.contact_submission') }}</h2>
        @if(is_null($contact_submission->read_at))
        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded text-sm">{{ __('admin.unread') }}</span>
        @else
        <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm">{{ __('admin.read') }} {{ $contact_submission->read_at->format('d/m/Y H:i') }}</span>
        @endif
    </div>

    <dl class="space-y-4">
        <div>
            <dt class="text-sm text-gray-500">{{ __('admin.contact_name') }}</dt>
            <dd class="text-gray-900">{{ $contact_submission->name }}</dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">{{ __('admin.contact_email_label') }}</dt>
            <dd class="text-gray-900"><a href="mailto:{{ $contact_submission->email }}" class="text-blue-600 hover:text-blue-900">{{ $contact_submission->email }}</a></dd>
        </div>
        @if($contact_submission->phone)
        <div>
            <dt class="text-sm text-gray-500">{{ __('admin.contact_phone') }}</dt>
            <dd class="text-gray-900">{{ $contact_submission->phone }}</dd>
        </div>
        @endif
        <div>
            <dt class="text-sm text-gray-500">{{ __('front.contact_message_label') }}</dt>
            <dd class="text-gray-900 whitespace-pre-wrap">{{ $contact_submission->message }}</dd>
        </div>
        <div>
            <dt class="text-sm text-gray-500">{{ __('admin.date') }}</dt>
            <dd class="text-gray-900">{{ $contact_submission->created_at->format('d/m/Y H:i:s') }}</dd>
        </div>
    </dl>

    <div class="mt-8 flex gap-3">
        <form action="{{ route('admin.contact-submissions.destroy', $contact_submission) }}" method="POST" onsubmit="return confirm('{{ __('admin.confirm_delete_submission') }}')">
            @csrf @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">{{ __('admin.delete') }}</button>
        </form>
    </div>
</div>
@endsection
