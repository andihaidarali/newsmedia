<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected function loadMoreUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return $url.(str_contains($url, '?') ? '&' : '?').'load_more=1';
    }

    /**
     * Display posts filtered by category.
     */
    public function show(Request $request, string $categoryPath)
    {
        $category = Category::findBySlugPath($categoryPath);

        abort_unless($category, 404);

        $category->load('descendants');

        $posts = Post::published()
            ->whereIn('category_id', $category->descendantsAndSelf()->pluck('id'))
            ->with(['author', 'category.parent', 'tags'])
            ->latest('published_at')
            ->simplePaginate(5)
            ->withQueryString();

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

        return view('blog.category', compact('category', 'posts', 'latestPosts'));
    }
}
