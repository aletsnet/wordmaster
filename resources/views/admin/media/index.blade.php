@extends('admin.layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.media') }}</h2>
    <a href="{{ route('admin.media.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('admin.upload_file') }}</a>
</div>
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
    @foreach($media as $item)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <img src="{{ asset('storage/' . $item->path) }}" alt="{{ $item->alt_text }}" class="w-full h-32 object-cover">
        <div class="p-2">
            <p class="text-xs truncate">{{ $item->original_name }}</p>
            <p class="text-xs text-gray-500">{{ number_format($item->size / 1024, 1) }} KB</p>
            <form action="{{ route('admin.media.destroy', $item) }}" method="POST" class="mt-1" onsubmit="return confirm('{{ __('admin.confirm_delete_media') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-red-600 hover:text-red-900">{{ __('admin.delete') }}</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-4">{{ $media->links() }}</div>
@endsection
