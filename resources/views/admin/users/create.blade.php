@extends('admin.layouts.app')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.new_user') }}</h2>
</div>
<form action="{{ route('admin.users.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.username') }}</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.display_name') }}</label>
        <input type="text" name="display_name" value="{{ old('display_name') }}" class="w-full border rounded px-3 py-2">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.email') }}</label>
        <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.password') }}</label>
        <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.confirm_password') }}</label>
        <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">{{ __('admin.role') }}</label>
        <select name="role" class="w-full border rounded px-3 py-2">
            <option value="admin">{{ __('admin.role_admin') }}</option>
            <option value="editor">{{ __('admin.role_editor') }}</option>
            <option value="author">{{ __('admin.role_author') }}</option>
            <option value="subscriber">{{ __('admin.role_subscriber') }}</option>
        </select>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">{{ __('admin.create_user') }}</button>
</form>
@endsection
