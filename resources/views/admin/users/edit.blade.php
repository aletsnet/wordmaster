@extends('admin.layouts.app')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.edit_user') }}</h2>
</div>
<form action="{{ route('admin.users.update', $user) }}" method="POST" class="bg-white rounded-lg shadow p-6">
    @csrf @method('PUT')
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.username') }}</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.display_name') }}</label>
        <input type="text" name="display_name" value="{{ old('display_name', $user->display_name) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.email') }}</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.new_password') }}</label>
        <input type="password" name="password" class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.confirm_password') }}</label>
        <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.role') }}</label>
        <select name="role" class="w-full border rounded px-3 py-2">
            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>{{ __('admin.role_admin') }}</option>
            <option value="editor" {{ $user->role == 'editor' ? 'selected' : '' }}>{{ __('admin.role_editor') }}</option>
            <option value="author" {{ $user->role == 'author' ? 'selected' : '' }}>{{ __('admin.role_author') }}</option>
            <option value="subscriber" {{ $user->role == 'subscriber' ? 'selected' : '' }}>{{ __('admin.role_subscriber') }}</option>
        </select>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">{{ __('admin.update_user') }}</button>
</form>
@endsection
