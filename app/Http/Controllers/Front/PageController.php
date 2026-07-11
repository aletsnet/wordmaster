<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\TemplateRenderer;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Post::with('author')
            ->where('slug', $slug)
            ->where('type', 'page')
            ->firstOrFail();

        if ($page->status !== 'published') {
            abort(404);
        }

        if ($page->template) {
            $renderer = new TemplateRenderer();
            return response($renderer->render($page->template, [
                'title'   => $page->title,
                'content' => $page->content,
                'page'    => $page,
            ]));
        }

        return view('front.pages.show', compact('page'));
    }
}
