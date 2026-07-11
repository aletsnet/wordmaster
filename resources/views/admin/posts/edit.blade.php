@extends('admin.layouts.app')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.edit_post') }}</h2>
</div>
<form action="{{ route('admin.posts.update', $post) }}" method="POST" class="bg-white rounded-lg shadow p-6">
    @csrf @method('PUT')
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.title') }}</label>
        <input type="text" name="title" value="{{ old('title', $post->title) }}" class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.slug') }}</label>
        <input type="text" name="slug" value="{{ old('slug', $post->slug) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.content') }}</label>
        <div class="flex gap-2 mb-2">
            <button type="button" onclick="openMediaPicker(function(media) { insertImageTag(media.url, media.alt); })" class="px-3 py-1.5 bg-gray-100 border rounded text-sm hover:bg-gray-200">{{ __('admin.insert_image') }}</button>
        </div>
        <textarea name="content" rows="15" class="w-full border rounded px-3 py-2 font-mono text-sm">{{ old('content', $post->content) }}</textarea>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.excerpt') }}</label>
        <textarea name="excerpt" rows="3" class="w-full border rounded px-3 py-2">{{ old('excerpt', $post->excerpt) }}</textarea>
    </div>
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-gray-700 mb-2">{{ __('admin.status') }}</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="draft" {{ $post->status == 'draft' ? 'selected' : '' }}>{{ __('admin.draft') }}</option>
                <option value="published" {{ $post->status == 'published' ? 'selected' : '' }}>{{ __('admin.published') }}</option>
            </select>
        </div>
        <div>
            <label class="block text-gray-700 mb-2">{{ __('admin.template') }}</label>
            <input type="text" name="template" value="{{ old('template', $post->template) }}" class="w-full border rounded px-3 py-2">
        </div>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.featured_image') }}</label>
        <div class="flex gap-2">
            <input type="text" name="featured_image" id="featured_image" value="{{ old('featured_image', $post->featured_image) }}" class="flex-1 border rounded px-3 py-2">
            <button type="button" onclick="openMediaPicker(function(media) { document.getElementById('featured_image').value = media.url; })" class="px-4 py-2 bg-gray-100 border rounded hover:bg-gray-200 whitespace-nowrap">{{ __('admin.browse_media') }}</button>
        </div>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.categories') }}</label>
        <div class="grid grid-cols-3 gap-2">
            @foreach($categories as $category)
            <label class="flex items-center">
                <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="mr-2" {{ in_array($category->id, $post->categories->pluck('id')->toArray()) ? 'checked' : '' }}> {{ $category->name }}
            </label>
            @endforeach
        </div>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.tags') }}</label>
        <div class="grid grid-cols-3 gap-2">
            @foreach($tags as $tag)
            <label class="flex items-center">
                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="mr-2" {{ in_array($tag->id, $post->tags->pluck('id')->toArray()) ? 'checked' : '' }}> {{ $tag->name }}
            </label>
            @endforeach
        </div>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">{{ __('admin.update_post') }}</button>
</form>

@include('admin.media.picker-modal')
<script>
function insertIntoContent(html) {
    const textarea = document.querySelector('textarea[name="content"]');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    textarea.value = textarea.value.substring(0, start) + html + textarea.value.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + html.length;
    textarea.focus();
}
function insertImageTag(url, alt) {
    insertIntoContent('<img src="' + url + '" alt="' + alt + '">');
}
</script>
@endsection
