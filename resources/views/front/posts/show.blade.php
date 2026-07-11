@extends('front.layouts.app')
@section('title', $post->title)
@section('content')
<article class="bg-white rounded-lg shadow p-8">
    @if($post->featured_image)
    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-64 object-cover rounded mb-6">
    @endif

    <h1 class="text-4xl font-bold mb-4">{{ $post->title }}</h1>

    <p class="text-gray-500 text-sm mb-6">
        {{ __('front.by') }}{{ $post->author->name }} | {{ $post->created_at->format('d/m/Y') }}
        @foreach($post->categories as $cat)
        | <a href="{{ route('category.show', $cat->slug) }}" class="text-blue-600">{{ $cat->name }}</a>
        @endforeach
    </p>

    @if($post->tags->isNotEmpty())
    <div class="mb-6">
        @foreach($post->tags as $tag)
        <a href="{{ route('tag.show', $tag->slug) }}" class="bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm mr-2">{{ $tag->name }}</a>
        @endforeach
    </div>
    @endif

    <div class="prose max-w-none text-gray-800 leading-relaxed">
        {!! $post->content !!}
    </div>
</article>

<div class="mt-6">
    <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">{{ __('front.back_to_posts') }}</a>
</div>
@endsection
