@extends('admin.layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.templates') }}</h2>
    <a href="{{ route('admin.templates.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        {{ __('admin.new_template') }}
    </a>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr class="text-left">
                <th class="px-6 py-3">{{ __('admin.name') }}</th>
                <th class="px-6 py-3">{{ __('admin.slug') }}</th>
                <th class="px-6 py-3">{{ __('admin.description') }}</th>
                <th class="px-6 py-3">Assets</th>
                <th class="px-6 py-3">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
            <tr class="border-t">
                <td class="px-6 py-4">{{ $template->name }}</td>
                <td class="px-6 py-4">{{ $template->slug }}</td>
                <td class="px-6 py-4">{{ Str::limit($template->description, 60) }}</td>
                <td class="px-6 py-4">
                    @if($template->assets_path)
                        <span class="inline-flex items-center gap-1 text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            assets
                        </span>
                    @else
                        <span class="text-gray-400 text-xs">—</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.templates.edit', $template) }}"
                       class="text-blue-600 hover:text-blue-900 mr-2">{{ __('admin.edit') }}</a>
                    <form action="{{ route('admin.templates.destroy', $template) }}" method="POST"
                          class="inline"
                          onsubmit="return confirm('{{ __('admin.confirm_delete_template') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">
                            {{ __('admin.delete') }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $templates->links() }}</div>
</div>
@endsection
