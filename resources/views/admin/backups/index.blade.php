@extends('admin.layouts.app')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold">{{ __('admin.backups') }}</h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
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
        <h3 class="text-gray-500 text-sm">{{ __('admin.templates') }}</h3>
        <p class="text-3xl font-bold">{{ $templates }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-500 text-sm">{{ __('admin.menus') }}</h3>
        <p class="text-3xl font-bold">{{ $menus }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">{{ __('admin.export_backup') }}</h3>
        <p class="text-gray-600 mb-4">{{ __('admin.export_backup_description') }}</p>
        <a href="{{ route('admin.backups.export') }}"
           class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            {{ __('admin.download_export') }}
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">{{ __('admin.import_backup') }}</h3>
        <p class="text-gray-600 mb-4">{{ __('admin.import_backup_description') }}</p>
        <form action="{{ route('admin.backups.import') }}" method="POST" enctype="multipart/form-data"
              onsubmit="return confirm('{{ __('admin.import_confirm') }}')">
            @csrf
            <div class="mb-4">
                <input type="file" name="backup_file" accept=".zip" required
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                @error('backup_file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                {{ __('admin.import_backup') }}
            </button>
        </form>
    </div>
</div>
@endsection
