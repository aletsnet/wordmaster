@extends('admin.layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.users') }}</h2>
    <a href="{{ route('admin.users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('admin.new_user') }}</a>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr class="text-left">
                <th class="px-6 py-3">{{ __('admin.name') }}</th>
                <th class="px-6 py-3">{{ __('admin.email') }}</th>
                <th class="px-6 py-3">{{ __('admin.role') }}</th>
                <th class="px-6 py-3">{{ __('admin.registered') }}</th>
                <th class="px-6 py-3">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-t">
                <td class="px-6 py-4">{{ $user->display_name ?? $user->name }}</td>
                <td class="px-6 py-4">{{ $user->email }}</td>
                <td class="px-6 py-4"><span class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">{{ $user->role }}</span></td>
                <td class="px-6 py-4">{{ $user->created_at->format('d/m/Y') }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('admin.edit') }}</a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_delete_user') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('admin.delete') }}</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $users->links() }}</div>
</div>
@endsection
