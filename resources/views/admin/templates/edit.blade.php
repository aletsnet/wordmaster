@extends('admin.layouts.app')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.edit_template') }}</h2>
</div>
<form action="{{ route('admin.templates.update', $template) }}" method="POST"
      enctype="multipart/form-data"
      class="bg-white rounded-lg shadow p-6">
    @csrf @method('PUT')
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.name') }}</label>
        <input type="text" name="name" value="{{ old('name', $template->name) }}"
               class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.slug') }}</label>
        <input type="text" name="slug" value="{{ old('slug', $template->slug) }}"
               class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.description') }}</label>
        <textarea name="description" rows="2" class="w-full border rounded px-3 py-2">{{ old('description', $template->description) }}</textarea>
    </div>

    {{-- Assets --}}
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">
            {{ __('admin.template_assets') }}
            <span class="text-gray-400 font-normal text-sm ml-1">(.zip, .tar, .tar.gz, .tgz, .tar.bz2, .rar — máx. 50 MB)</span>
        </label>

        @if($template->assets_path)
        <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded flex items-center justify-between">
            <div class="text-sm text-green-800">
                <span class="font-medium">Assets actuales:</span>
                <code class="ml-1 bg-green-100 px-1 rounded">{{ $template->assets_path }}</code>
                &nbsp;—&nbsp;
                <a href="{{ Storage::url($template->assets_path) }}"
                   target="_blank"
                   class="underline hover:text-green-600">ver directorio</a>
            </div>
            <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                <input type="checkbox" name="remove_assets" value="1"
                       class="rounded border-gray-300">
                Eliminar assets
            </label>
        </div>
        @endif

        <input type="file" name="assets"
               accept=".zip,.rar,.tar,.gz,.bz2,.tgz"
               class="w-full border rounded px-3 py-2 bg-gray-50">
        @error('assets')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
        <p class="text-gray-500 text-sm mt-1">
            @if($template->assets_path)
                Subir un nuevo archivo reemplazará los assets existentes.
            @endif
            Disponibles en el template como <code class="bg-gray-100 px-1 rounded">@{{ $assets_url }}/ruta/al/archivo</code>.
        </p>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.template_content') }}</label>
        <textarea name="content" rows="20"
                  class="w-full border rounded px-3 py-2 font-mono text-sm">{!! htmlspecialchars(old('content', $template->content)) !!}</textarea>
        @error('content')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
        {{ __('admin.update_template') }}
    </button>
</form>
@endsection
