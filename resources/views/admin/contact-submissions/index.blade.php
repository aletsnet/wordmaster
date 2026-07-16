@extends('admin.layouts.app')
@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold">{{ __('admin.contact_submissions') }}</h2>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="text-left border-b bg-gray-50">
                <th class="px-6 py-3">{{ __('admin.contact_name') }}</th>
                <th class="px-6 py-3">{{ __('admin.contact_email_label') }}</th>
                <th class="px-6 py-3">{{ __('admin.contact_phone') }}</th>
                <th class="px-6 py-3">{{ __('front.contact_message_label') }}</th>
                <th class="px-6 py-3">{{ __('admin.date') }}</th>
                <th class="px-6 py-3">{{ __('admin.status') }}</th>
                <th class="px-6 py-3">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $submission)
            <tr class="border-b hover:bg-gray-50 {{ is_null($submission->read_at) ? 'font-semibold bg-blue-50' : '' }}">
                <td class="px-6 py-4">{{ $submission->name }}</td>
                <td class="px-6 py-4"><a href="mailto:{{ $submission->email }}" class="text-blue-600 hover:text-blue-900">{{ $submission->email }}</a></td>
                <td class="px-6 py-4">{{ $submission->phone ?: '—' }}</td>
                <td class="px-6 py-4 max-w-xs truncate">{{ Str::limit($submission->message, 80) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $submission->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-6 py-4">
                    @if(is_null($submission->read_at))
                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">{{ __('admin.unread') }}</span>
                    @else
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">{{ __('admin.read') }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <a href="{{ route('admin.contact-submissions.show', $submission) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('admin.view') }}</a>
                    <form action="{{ route('admin.contact-submissions.destroy', $submission) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_delete_submission') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('admin.delete') }}</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">{{ __('admin.no_submissions') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $submissions->links() }}
</div>
@endsection
