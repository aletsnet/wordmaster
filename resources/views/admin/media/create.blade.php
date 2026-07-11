@extends('admin.layouts.app')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.upload_file') }}</h2>
</div>
<form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.file_field') }}</label>
        <input type="file" name="file" class="w-full border rounded px-3 py-2" accept="image/*" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.alt_text') }}</label>
        <input type="text" name="alt_text" value="{{ old('alt_text') }}" class="w-full border rounded px-3 py-2">
    </div>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">{{ __('admin.upload') }}</button>
</form>
@endsection
