<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected function loadMoreUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return $url.(str_contains($url, '?') ? '&' : '?').'load_more=1';
    }

    /**
     * Display the blog listing page.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $headlinePost = Post::published()
            ->with(['author', 'category.parent', 'tags'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            }))
            ->latest('published_at')
            ->first();

        $posts = Post::published()
            ->with(['author', 'category.parent', 'tags'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            }))
            ->when($headlinePost, fn ($query) => $query->whereKeyNot($headlinePost->id))
            ->latest('published_at')
            ->simplePaginate(5)
            ->withQueryString();

        $categories = Category::query()
            ->with('descendants')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $categorySections = $categories
            ->take(3)
            ->map(function (Category $category) {
                return [
                    'category' => $category,
                    'posts' => Post::published()
                        ->whereIn('category_id', $category->descendantsAndSelf()->pluck('id'))
                        ->with(['author', 'category.parent', 'tags'])
                        ->latest('published_at')
                        ->limit(4)
                        ->get(),
                ];
            })
            ->filter(fn (array $section) => $section['posts']->isNotEmpty())
            ->values();

        $latestPosts = Post::published()
            ->with(['author', 'category.parent', 'tags'])
            ->latest('published_at')
            ->limit(5)
            ->get();

        if ($request->boolean('load_more')) {
            return response()->json([
                'html' => view('blog.partials.post-list-items', ['posts' => $posts])->render(),
                'next_page_url' => $this->loadMoreUrl($posts->nextPageUrl()),
            ]);
        }

        return view('blog.index', compact('posts', 'categories', 'headlinePost', 'categorySections', 'latestPosts', 'search'));
    }

    /**
     * Display a single blog post.
     */
    public function show(string $categoryPath, Post $post)
    {
        // Only allow viewing of published posts on the public site
        if ($post->status !== 'published' || $post->published_at > now()) {
            abort(404);
        }

        $post->load(['author', 'category.parent', 'tags', 'writerCredits']);

        if ($post->category_path !== trim($categoryPath, '/')) {
            abort(404);
        }

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->where(function ($query) use ($post) {
                $query->where('category_id', $post->category_id)
                    ->orWhereHas('tags', fn ($q) => $q->whereIn('tags.id', $post->tags->pluck('id')));
            })
            ->with(['author', 'category.parent', 'tags'])
            ->limit(4)
            ->latest('published_at')
            ->get();

        $latestPosts = Post::published()
            ->whereKeyNot($post->id)
            ->with(['author', 'category.parent', 'tags'])
            ->latest('published_at')
            ->limit(5)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts', 'latestPosts'));
    }
}
