@extends('admin.layouts.app')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">{{ __('admin.categories') }}</h2>
    <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('admin.new_category') }}</a>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr class="text-left">
                <th class="px-6 py-3">{{ __('admin.name') }}</th>
                <th class="px-6 py-3">{{ __('admin.slug') }}</th>
                <th class="px-6 py-3">{{ __('admin.parent') }}</th>
                <th class="px-6 py-3">{{ __('admin.posts') }}</th>
                <th class="px-6 py-3">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr class="border-t">
                <td class="px-6 py-4">{{ $category->name }}</td>
                <td class="px-6 py-4">{{ $category->slug }}</td>
                <td class="px-6 py-4">{{ $category->parent->name ?? '—' }}</td>
                <td class="px-6 py-4">{{ $category->posts_count }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('admin.edit') }}</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('admin.confirm_delete_category') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('admin.delete') }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $categories->links() }}</div>
</div>
@endsection
