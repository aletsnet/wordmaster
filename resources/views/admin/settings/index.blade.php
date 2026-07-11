@extends('admin.layouts.app')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.settings_title') }}</h2>
</div>
<form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white rounded-lg shadow p-6">
    @csrf @method('PUT')
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.site_title') }}</label>
        <input type="text" name="site_title" value="{{ $settings['site_title'] }}" class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.site_description') }}</label>
        <textarea name="site_description" rows="2" class="w-full border rounded px-3 py-2">{{ $settings['site_description'] }}</textarea>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.site_logo') }}</label>
        <input type="text" name="site_logo" value="{{ $settings['site_logo'] }}" class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.posts_per_page') }}</label>
        <input type="number" name="posts_per_page" value="{{ $settings['posts_per_page'] }}" class="w-full border rounded px-3 py-2" min="1" max="100">
    </div>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">{{ __('admin.save_settings') }}</button>
</form>
@endsection
