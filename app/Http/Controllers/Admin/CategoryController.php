<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->withCount('posts')->latest()->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|unique:categories,slug',
            'description' => 'nullable',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', __('messages.category_created'));
    }

    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|unique:categories,slug,' . $category->id,
            'description' => 'nullable',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', __('messages.category_updated'));
    }

    public function destroy(Category $category)
    {
        $category->posts()->detach();
        $category->children()->update(['parent_id' => null]);
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', __('messages.category_deleted'));
    }
}
