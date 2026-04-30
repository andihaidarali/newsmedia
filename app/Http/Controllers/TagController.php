<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected function loadMoreUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return $url.(str_contains($url, '?') ? '&' : '?').'load_more=1';
    }

    /**
     * Display posts filtered by tag.
     */
    public function show(Request $request, Tag $tag)
    {
        $posts = Post::published()
            ->whereHas('tags', fn ($q) => $q->where('tags.id', $tag->id))
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

        return view('blog.tag', compact('tag', 'posts', 'latestPosts'));
    }
}
