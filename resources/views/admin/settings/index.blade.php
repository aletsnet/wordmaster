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
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.contact_email') }}</label>
        <input type="email" name="contact_email" value="{{ $settings['contact_email'] }}" class="w-full border rounded px-3 py-2" placeholder="informacion@convoydemexico.com">
    </div>

    <hr class="my-6">

    <h3 class="text-lg font-bold mb-4">{{ __('admin.contact_social') }}</h3>

    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.contact_phone1') }}</label>
        <input type="text" name="contact_phone1" value="{{ $settings['contact_phone1'] }}" class="w-full border rounded px-3 py-2" placeholder="+52 775 753 8585">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.contact_phone2') }}</label>
        <input type="text" name="contact_phone2" value="{{ $settings['contact_phone2'] }}" class="w-full border rounded px-3 py-2" placeholder="+52 775 108 9864">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.contact_whatsapp') }}</label>
        <input type="text" name="contact_whatsapp" value="{{ $settings['contact_whatsapp'] }}" class="w-full border rounded px-3 py-2" placeholder="527751089864">
        <p class="text-xs text-gray-500 mt-1">{{ __('admin.contact_whatsapp_hint') }}</p>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.contact_facebook') }}</label>
        <input type="text" name="contact_facebook" value="{{ $settings['contact_facebook'] }}" class="w-full border rounded px-3 py-2" placeholder="Convoy de México">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.contact_instagram') }}</label>
        <input type="text" name="contact_instagram" value="{{ $settings['contact_instagram'] }}" class="w-full border rounded px-3 py-2" placeholder="convoy_mexico">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.contact_tiktok') }}</label>
        <input type="text" name="contact_tiktok" value="{{ $settings['contact_tiktok'] }}" class="w-full border rounded px-3 py-2" placeholder="convoy_de_mexico">
    </div>

    <hr class="my-6">
    <h3 class="text-lg font-bold mb-4">{{ __('admin.custom_css') }}</h3>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.custom_css_label') }}</label>
        <textarea name="custom_css" rows="8" class="w-full border rounded px-3 py-2 font-mono text-sm">{{ $settings['custom_css'] }}</textarea>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">{{ __('admin.save_settings') }}</button>
</form>
@endsection
