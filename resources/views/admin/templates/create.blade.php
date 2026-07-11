@extends('admin.layouts.app')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.new_template') }}</h2>
</div>
<form action="{{ route('admin.templates.store') }}" method="POST"
      enctype="multipart/form-data"
      class="bg-white rounded-lg shadow p-6">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.name') }}</label>
        <input type="text" name="name" value="{{ old('name') }}"
               class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.slug') }}</label>
        <input type="text" name="slug" value="{{ old('slug') }}"
               class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.description') }}</label>
        <textarea name="description" rows="2" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
    </div>

    {{-- Assets --}}
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">
            {{ __('admin.template_assets') }}
            <span class="text-gray-400 font-normal text-sm ml-1">(.zip, .tar, .tar.gz, .tgz, .tar.bz2, .rar — máx. 50 MB)</span>
        </label>
        <input type="file" name="assets"
               accept=".zip,.rar,.tar,.gz,.bz2,.tgz"
               class="w-full border rounded px-3 py-2 bg-gray-50">
        @error('assets')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
        <p class="text-gray-500 text-sm mt-1">
            Los archivos extraídos estarán disponibles en el template como <code class="bg-gray-100 px-1 rounded">{{ '{{ $assets_url }}' }}</code>
            (ej: <code class="bg-gray-100 px-1 rounded">{{ '{{ $assets_url }}' }}/css/style.css</code>).
        </p>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.template_content') }}</label>
        <textarea name="content" rows="20"
                  class="w-full border rounded px-3 py-2 font-mono text-sm">{{ old('content') }}</textarea>
        @error('content')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
        {{ __('admin.create_template') }}
    </button>
</form>
@endsection
