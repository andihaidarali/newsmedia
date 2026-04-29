<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display the blog listing page.
     */
    public function index(Request $request)
    {
        $posts = Post::published()
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(12);

        $categories = Category::withCount(['posts' => fn ($q) => $q->published()])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return view('blog.index', compact('posts', 'categories'));
    }

    /**
     * Display a single blog post.
     */
    public function show(Post $post)
    {
        // Only allow viewing of published posts on the public site
        if ($post->status !== 'published' || $post->published_at > now()) {
            abort(404);
        }

        $post->load(['author', 'category', 'tags']);

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->where(function ($query) use ($post) {
                $query->where('category_id', $post->category_id)
                    ->orWhereHas('tags', fn ($q) => $q->whereIn('tags.id', $post->tags->pluck('id')));
            })
            ->with(['author', 'category'])
            ->limit(4)
            ->latest('published_at')
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }
}
