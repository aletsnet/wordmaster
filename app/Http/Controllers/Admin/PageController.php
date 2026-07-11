<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Post::with('author')
            ->ofType('page')
            ->latest()
            ->paginate(15);
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug',
            'content' => 'nullable',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|max:255',
            'template' => 'nullable|max:255',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['author_id'] = Auth::id();
        $data['type'] = 'page';

        Post::create($data);

        return redirect()->route('admin.pages.index')
            ->with('success', __('messages.page_created'));
    }

    public function edit(Post $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Post $page)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug,' . $page->id,
            'content' => 'nullable',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|max:255',
            'template' => 'nullable|max:255',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $page->update($data);

        return redirect()->route('admin.pages.index')
            ->with('success', __('messages.page_updated'));
    }

    public function destroy(Post $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')
            ->with('success', __('messages.page_deleted'));
    }
}
