<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::published()
            ->with('author')
            ->when(
                $request->category,
                fn($q) => $q->where('category', $request->category)
            )
            ->latest('published_at')
            ->paginate(9);

        $categories = BlogPost::published()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('public.blog.index', compact('posts', 'categories'));
    }

    public function show(BlogPost $blogPost)
    {
        abort_if($blogPost->status !== 'published', 404);

        $blogPost->increment('views');
        $blogPost->load('author');

        $related = BlogPost::published()
            ->where('id', '!=', $blogPost->id)
            ->where('category', $blogPost->category)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.blog.show', compact('blogPost', 'related'));
    }
}