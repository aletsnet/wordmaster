<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('posts')->latest()->paginate(15);
        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|unique:tags,slug',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        Tag::create($data);

        return redirect()->route('admin.tags.index')
            ->with('success', __('messages.tag_created'));
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|unique:tags,slug,' . $tag->id,
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $tag->update($data);

        return redirect()->route('admin.tags.index')
            ->with('success', __('messages.tag_updated'));
    }

    public function destroy(Tag $tag)
    {
        $tag->posts()->detach();
        $tag->delete();
        return redirect()->route('admin.tags.index')
            ->with('success', __('messages.tag_deleted'));
    }
}
