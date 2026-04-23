<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Service;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::query()->where('status', 'published')->latest('published_at')->paginate(10);

        return view('front.blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::query()->where('slug', $slug)->where('status', 'published')->firstOrFail();
        $relatedPosts = BlogPost::query()
            ->where('status', 'published')
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();
        $relatedServices = Service::query()
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->take(4)
            ->get();

        return view('front.blog.show', compact('post', 'relatedPosts', 'relatedServices'));
    }
}
