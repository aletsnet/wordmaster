@extends('admin.layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.posts') }}</h2>
    <a href="{{ route('admin.posts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('admin.new_post') }}</a>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr class="text-left">
                <th class="px-6 py-3">{{ __('admin.title') }}</th>
                <th class="px-6 py-3">{{ __('admin.author') }}</th>
                <th class="px-6 py-3">{{ __('admin.categories') }}</th>
                <th class="px-6 py-3">{{ __('admin.status') }}</th>
                <th class="px-6 py-3">{{ __('admin.date') }}</th>
                <th class="px-6 py-3">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
            <tr class="border-t">
                <td class="px-6 py-4">{{ $post->title }}</td>
                <td class="px-6 py-4">{{ $post->author->name }}</td>
                <td class="px-6 py-4">@foreach($post->categories as $cat)<span class="bg-gray-200 px-2 py-1 rounded text-sm mr-1">{{ $cat->name }}</span>@endforeach</td>
                <td class="px-6 py-4"><span class="px-2 py-1 rounded text-sm {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $post->status }}</span></td>
                <td class="px-6 py-4">{{ $post->created_at->format('d/m/Y') }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.posts.edit', $post) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('admin.edit') }}</a>
                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_delete_post') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('admin.delete') }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $posts->links() }}</div>
</div>
@endsection
