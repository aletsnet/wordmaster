@extends('admin.layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.menus') }}</h2>
    <a href="{{ route('admin.menus.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('admin.new_menu') }}</a>
</div>
<div class="grid gap-4">
    @foreach($menus as $menu)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold">{{ $menu->name }}</h3>
                <p class="text-sm text-gray-500">{{ __('admin.slug_label') }} {{ $menu->slug }} | {{ __('admin.location_label') }} {{ $menu->location ?? '—' }} | {{ __('admin.items_count') }} {{ $menu->items->count() }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.menus.edit', $menu) }}" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">{{ __('admin.edit') }}</a>
                <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('{{ __('admin.confirm_delete_menu') }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">{{ __('admin.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
