@extends('admin.layouts.app')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold">{{ __('admin.dashboard') }}</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm">{{ __('admin.posts') }}</h3>
        <p class="text-3xl font-bold">{{ $posts }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm">{{ __('admin.pages') }}</h3>
        <p class="text-3xl font-bold">{{ $pages }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm">{{ __('admin.categories') }}</h3>
        <p class="text-3xl font-bold">{{ $categories }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm">{{ __('admin.tags') }}</h3>
        <p class="text-3xl font-bold">{{ $tags }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm">{{ __('admin.media') }}</h3>
        <p class="text-3xl font-bold">{{ $media }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm">{{ __('admin.users') }}</h3>
        <p class="text-3xl font-bold">{{ $users }}</p>
    </div>
</div>
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-bold mb-4">{{ __('admin.recent_posts') }}</h3>
    <table class="w-full">
        <thead>
            <tr class="text-left border-b">
                <th class="pb-2">{{ __('admin.title') }}</th>
                <th class="pb-2">{{ __('admin.author') }}</th>
                <th class="pb-2">{{ __('admin.status') }}</th>
                <th class="pb-2">{{ __('admin.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentPosts as $post)
            <tr class="border-b">
                <td class="py-2">{{ $post->title }}</td>
                <td class="py-2">{{ $post->author->name }}</td>
                <td class="py-2">{{ $post->status }}</td>
                <td class="py-2">{{ $post->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
