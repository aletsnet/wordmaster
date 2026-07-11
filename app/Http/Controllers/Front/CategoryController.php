<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Option;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $perPage = Option::getValue('posts_per_page', 10);

        $posts = $category->posts()
            ->with('author', 'categories', 'tags')
            ->published()
            ->ofType('post')
            ->latest()
            ->paginate((int) $perPage);

        return view('front.posts.index', compact('posts', 'category'));
    }
}
