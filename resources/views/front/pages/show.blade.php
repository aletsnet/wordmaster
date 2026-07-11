@extends('front.layouts.app')
@section('title', $page->title)
@section('content')
<article class="bg-white rounded-lg shadow p-8">
    @if($page->featured_image)
    <img src="{{ $page->featured_image }}" alt="{{ $page->title }}" class="w-full h-64 object-cover rounded mb-6">
    @endif

    <h1 class="text-4xl font-bold mb-6">{{ $page->title }}</h1>

    <div class="prose max-w-none text-gray-800 leading-relaxed">
        {!! $page->content !!}
    </div>
</article>
@endsection
