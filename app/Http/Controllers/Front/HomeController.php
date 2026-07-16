<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Post;
use App\Services\TemplateRenderer;

class HomeController extends Controller
{
    public function index()
    {
        $homeSlug = Option::getValue('home_page');

        if ($homeSlug) {
            $post = Post::with('author', 'categories', 'tags')
                ->where('slug', $homeSlug)
                ->whereIn('type', ['post', 'page'])
                ->published()
                ->first();

            if ($post) {
                if ($post->template) {
                    $renderer = new TemplateRenderer();
                    return response($renderer->render($post->template, [
                        'title'   => $post->title,
                        'content' => $post->content,
                        'page'    => $post,
                        'post'    => $post,
                    ]));
                }

                if ($post->type === 'page') {
                    return view('front.pages.show', compact('post') + ['page' => $post]);
                }

                return view('front.posts.show', compact('post'));
            }
        }

        $perPage = Option::getValue('posts_per_page', 10);
        $posts = Post::with('author', 'categories', 'tags')
            ->published()
            ->ofType('post')
            ->latest()
            ->paginate((int) $perPage);
        return view('front.posts.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::with('author', 'categories', 'tags')
            ->where('slug', $slug)
            ->whereIn('type', ['post', 'page'])
            ->published()
            ->firstOrFail();

        if ($post->template) {
            $renderer = new TemplateRenderer();
            return response($renderer->render($post->template, [
                'title'   => $post->title,
                'content' => $post->content,
                'page'    => $post,
                'post'    => $post,
            ]));
        }

        if ($slug === 'contacto') {
            return view('front.contact.show', compact('post') + ['page' => $post]);
        }

        if ($post->type === 'page') {
            return view('front.pages.show', compact('post') + ['page' => $post]);
        }

        return view('front.posts.show', compact('post'));
    }

    public function page($slug)
    {
        $page = Post::where('slug', $slug)
            ->where('type', 'page')
            ->published()
            ->firstOrFail();

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
