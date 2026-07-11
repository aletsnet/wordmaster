@extends('admin.layouts.app')
@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.edit_menu') }} {{ $menu->name }}</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">{{ __('admin.menu_properties') }}</h3>
        <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">{{ __('admin.name') }}</label>
                <input type="text" name="name" value="{{ $menu->name }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">{{ __('admin.slug') }}</label>
                <input type="text" name="slug" value="{{ $menu->slug }}" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">{{ __('admin.location') }}</label>
                <input type="text" name="location" value="{{ $menu->location }}" class="w-full border rounded px-3 py-2">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('admin.update_menu') }}</button>
        </form>

        <h3 class="text-lg font-bold mt-8 mb-4">{{ __('admin.add_item') }}</h3>
        <form action="{{ route('admin.menus.items.add', $menu) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">{{ __('admin.title') }}</label>
                <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">{{ __('admin.type') }}</label>
                <select name="type" class="w-full border rounded px-3 py-2" id="itemType">
                    <option value="custom">{{ __('admin.custom') }}</option>
                    <option value="page">{{ __('admin.pages') }}</option>
                    <option value="category">{{ __('admin.categories') }}</option>
                </select>
            </div>
            <div class="mb-4" id="customUrl">
                <label class="block text-gray-700 mb-2">{{ __('admin.url') }}</label>
                <input type="text" name="url" class="w-full border rounded px-3 py-2" placeholder="/ejemplo">
            </div>
            <div class="mb-4 hidden" id="pageSelect">
                <label class="block text-gray-700 mb-2">{{ __('admin.select_page') }}</label>
                <select name="target_id" class="w-full border rounded px-3 py-2">
                    @foreach($pages as $page)
                    <option value="{{ $page->id }}">{{ $page->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4 hidden" id="categorySelect">
                <label class="block text-gray-700 mb-2">{{ __('admin.select_category') }}</label>
                <select name="target_id" class="w-full border rounded px-3 py-2">
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">{{ __('admin.parent_item') }}</label>
                <select name="parent_id" class="w-full border rounded px-3 py-2">
                    <option value="">{{ __('admin.no_parent') }}</option>
                    @foreach($menu->items as $item)
                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">{{ __('admin.add_item') }}</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">{{ __('admin.menu_items') }}</h3>
        @if($menu->items->isEmpty())
        <p class="text-gray-500">{{ __('admin.no_items') }}</p>
        @else
        <ul class="space-y-2">
            @foreach($menu->items as $item)
            <li class="border rounded p-3">
                <div class="flex justify-between items-center">
                    <div>
                        <strong>{{ $item->title }}</strong>
                        <p class="text-sm text-gray-500">{{ $item->url }} ({{ $item->type }})</p>
                    </div>
                    <form action="{{ route('admin.menus.items.destroy', [$menu, $item]) }}" method="POST" onsubmit="return confirm('{{ __('admin.confirm_delete_item') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">{{ __('admin.delete') }}</button>
                    </form>
                </div>
                @if($item->children->isNotEmpty())
                <ul class="ml-4 mt-2 space-y-1">
                    @foreach($item->children as $child)
                    <li class="flex justify-between items-center text-sm border-t pt-1">
                        <span>{{ $child->title }}</span>
                        <form action="{{ route('admin.menus.items.destroy', [$menu, $child]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">{{ __('admin.delete') }}</button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                @endif
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>

<script>
document.getElementById('itemType').addEventListener('change', function() {
    document.getElementById('customUrl').classList.toggle('hidden', this.value !== 'custom');
    document.getElementById('pageSelect').classList.toggle('hidden', this.value !== 'page');
    document.getElementById('categorySelect').classList.toggle('hidden', this.value !== 'category');
});
</script>
@endsection
