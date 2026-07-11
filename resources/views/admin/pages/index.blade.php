@extends('admin.layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.pages') }}</h2>
    <a href="{{ route('admin.pages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('admin.new_page') }}</a>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr class="text-left">
                <th class="px-6 py-3">{{ __('admin.title') }}</th>
                <th class="px-6 py-3">{{ __('admin.slug') }}</th>
                <th class="px-6 py-3">{{ __('admin.status') }}</th>
                <th class="px-6 py-3">{{ __('admin.date') }}</th>
                <th class="px-6 py-3">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pages as $page)
            <tr class="border-t">
                <td class="px-6 py-4">{{ $page->title }}</td>
                <td class="px-6 py-4">/{{ $page->slug }}</td>
                <td class="px-6 py-4"><span class="px-2 py-1 rounded text-sm {{ $page->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $page->status }}</span></td>
                <td class="px-6 py-4">{{ $page->created_at->format('d/m/Y') }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.pages.edit', $page) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('admin.edit') }}</a>
                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_delete_page') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('admin.delete') }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $pages->links() }}</div>
</div>
@endsection
