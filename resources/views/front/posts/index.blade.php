@extends('front.layouts.app')
@section('title', isset($category) ? __('front.category_prefix') . $category->name : (isset($tag) ? __('front.tag_prefix') . $tag->name : __('front.home')))
@section('content')
@if(isset($category))
<div class="mb-8">
    <h1 class="text-3xl font-bold">{{ __('front.category_prefix') }}{{ $category->name }}</h1>
    @if($category->description)<p class="text-gray-600 mt-2">{{ $category->description }}</p>@endif
</div>
@elseif(isset($tag))
<div class="mb-8">
    <h1 class="text-3xl font-bold">{{ __('front.tag_prefix') }}{{ $tag->name }}</h1>
</div>
@endif

<div class="grid gap-6">
    @forelse($posts as $post)
    <article class="bg-white rounded-lg shadow p-6">
        @if($post->featured_image)
        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-48 object-cover rounded mb-4">
        @endif
        <h2 class="text-2xl font-bold mb-2">
            <a href="{{ route('post.show', $post->slug) }}" class="text-gray-900 hover:text-blue-600">{{ $post->title }}</a>
        </h2>
        <p class="text-gray-500 text-sm mb-3">
            {{ __('front.by') }}{{ $post->author->name }} | {{ $post->created_at->format('d/m/Y') }}
            @foreach($post->categories as $cat)
            | <a href="{{ route('category.show', $cat->slug) }}" class="text-blue-600">{{ $cat->name }}</a>
            @endforeach
        </p>
        <p class="text-gray-700">{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 200) }}</p>
        <a href="{{ route('post.show', $post->slug) }}" class="text-blue-600 hover:text-blue-800 mt-3 inline-block">{{ __('front.read_more') }}</a>
    </article>
    @empty
    <p class="text-gray-500 text-center py-8">{{ __('front.no_posts') }}</p>
    @endforelse
</div>

<div class="mt-6">
    {{ $posts->links() }}
</div>
@endsection
