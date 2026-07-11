<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Tag;

class TagController extends Controller
{
    public function show($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $perPage = Option::getValue('posts_per_page', 10);

        $posts = $tag->posts()
            ->with('author', 'categories', 'tags')
            ->published()
            ->ofType('post')
            ->latest()
            ->paginate((int) $perPage);

        return view('front.posts.index', compact('posts', 'tag'));
    }
}
