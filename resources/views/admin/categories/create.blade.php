@extends('admin.layouts.app')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.new_category') }}</h2>
</div>
<form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.name') }}</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.slug') }}</label>
        <input type="text" name="slug" value="{{ old('slug') }}" class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.description') }}</label>
        <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.parent_category') }}</label>
        <select name="parent_id" class="w-full border rounded px-3 py-2">
            <option value="">{{ __('admin.none') }}</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">{{ __('admin.create_category') }}</button>
</form>
@endsection
