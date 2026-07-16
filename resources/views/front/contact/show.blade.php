@extends('front.layouts.app')
@section('title', $page->title)
@section('content')

@if($page->featured_image)
<img src="{{ $page->featured_image }}" alt="{{ $page->title }}">
@endif

<h1>{{ $page->title }}</h1>

<div>
    {!! $page->content !!}
</div>

@if (session('success'))
<div>{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('contact.send') }}">
    @csrf
    <div>
        <label>{{ __('front.contact_name') }}</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
        @error('name') <div>{{ $message }}</div> @enderror
    </div>
    <div>
        <label>{{ __('front.contact_email_label') }}</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <div>{{ $message }}</div> @enderror
    </div>
    <div>
        <label>{{ __('front.contact_phone') }}</label>
        <input type="text" name="phone" value="{{ old('phone') }}">
    </div>
    <div>
        <label>{{ __('front.contact_message_label') }}</label>
        <textarea name="message" rows="6" required>{{ old('message') }}</textarea>
        @error('message') <div>{{ $message }}</div> @enderror
    </div>
    <button type="submit">{{ __('front.contact_send') }}</button>
</form>

@endsection
