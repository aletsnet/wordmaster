<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactSubmission;
use App\Models\Medium;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'posts' => Post::count(),
            'pages' => Post::ofType('page')->count(),
            'categories' => Category::count(),
            'tags' => Tag::count(),
            'media' => Medium::count(),
            'users' => User::count(),
            'unreadMessages' => ContactSubmission::unread()->count(),
            'recentPosts' => Post::with('author')->latest()->take(5)->get(),
        ];
        return view('admin.dashboard', $stats);
    }
}
