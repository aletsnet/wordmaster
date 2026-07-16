<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('author', 'categories')
            ->ofType('post')
            ->latest()
            ->paginate(15);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug',
            'content' => 'nullable',
            'excerpt' => 'nullable',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|max:255',
            'template' => 'nullable|max:255',
            'categories' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['author_id'] = Auth::id();
        $data['type'] = 'post';

        $post = Post::create($data);

        if (isset($data['categories'])) {
            $post->categories()->sync($data['categories']);
        }
        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', __('messages.post_created'));
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:posts,slug,' . $post->id,
            'content' => 'nullable',
            'excerpt' => 'nullable',
            'status' => 'required|in:draft,published',
            'featured_image' => 'nullable|max:255',
            'template' => 'nullable|max:255',
            'categories' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $post->update($data);

        $post->categories()->sync($data['categories'] ?? []);
        $post->tags()->sync($data['tags'] ?? []);

        return redirect()->route('admin.posts.index')
            ->with('success', __('messages.post_updated'));
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')
            ->with('success', __('messages.post_deleted'));
    }

    public function linkPickerData()
    {
        $pages = Post::ofType('page')->latest()->get(['id', 'title', 'slug']);
        $posts = Post::ofType('post')->latest()->get(['id', 'title', 'slug']);

        return response()->json(compact('pages', 'posts'));
    }
}
