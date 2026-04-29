<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Advertorial;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of all posts (admin).
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Post::class);

        $filters = [
            'advertorial_id' => $request->integer('advertorial_id') ?: null,
            'month' => $request->integer('month') ?: null,
            'year' => $request->integer('year') ?: null,
        ];

        $posts = Post::with(['advertorial', 'author', 'category', 'tags', 'writerCredits'])
            ->when($filters['advertorial_id'], fn ($query, $advertorialId) => $query->where('advertorial_id', $advertorialId))
            ->when($filters['month'], fn ($query, $month) => $query->whereMonth('published_at', $month))
            ->when($filters['year'], fn ($query, $year) => $query->whereYear('published_at', $year))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $advertorials = Advertorial::orderBy('name')->get(['id', 'name', 'partner']);
        $years = Post::query()
            ->selectRaw('strftime("%Y", published_at) as year')
            ->whereNotNull('published_at')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter()
            ->values();

        return view('admin.posts.index', compact('posts', 'advertorials', 'filters', 'years'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        Gate::authorize('create', Post::class);

        $categories = Category::orderBy('sort_order')->get();
        $advertorials = Advertorial::orderByDesc('starts_at')
            ->orderBy('name')
            ->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.create', compact('advertorials', 'categories', 'tags'));
    }

    /**
     * Store a newly created post.
     */
    public function store(StorePostRequest $request)
    {
        Gate::authorize('create', Post::class);

        $data = $request->validated();

        if (filled($data['new_category_name'] ?? null)) {
            Gate::authorize('create', Category::class);

            $category = Category::create([
                'name' => $data['new_category_name'],
                'slug' => $data['new_category_slug'] ?? null,
                'description' => $data['new_category_description'] ?? null,
            ]);

            $data['category_id'] = $category->id;
        }

        if (filled($data['new_advertorial_name'] ?? null)) {
            $advertorial = Advertorial::create([
                'name' => $data['new_advertorial_name'],
                'partner' => $data['new_advertorial_partner'],
                'starts_at' => $data['new_advertorial_starts_at'],
                'ends_at' => $data['new_advertorial_ends_at'],
            ]);

            $data['advertorial_id'] = $advertorial->id;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')
                ->store('posts/featured', 'public');
        }

        if ($request->hasFile('gallery_images')) {
            $data['gallery_images'] = collect($request->file('gallery_images'))
                ->map(fn ($image) => $image->store('posts/gallery', 'public'))
                ->all();
        }

        if ($request->hasFile('infographic_image')) {
            $data['infographic_image'] = $request->file('infographic_image')
                ->store('posts/infographics', 'public');
        }

        // Set the author
        $data['user_id'] = auth()->id();

        if (auth()->user()->isReporter()) {
            $data['status'] = 'draft';
            $data['published_at'] = null;
        }

        $data = $this->normalizePublishState($data);

        // Remove tags from data before creating (handled separately)
        $submittedTags = collect($data['tags'] ?? []);
        $tags = $submittedTags
            ->filter(fn (string $tag) => ctype_digit($tag))
            ->map(fn (string $tag) => (int) $tag)
            ->values()
            ->all();

        $select2Tags = $submittedTags
            ->filter(fn (string $tag) => str_starts_with($tag, '__new__:'))
            ->map(fn (string $tag) => trim(str_replace('__new__:', '', $tag)))
            ->filter();

        $newTags = collect(preg_split('/[\r\n,]+/', $data['new_tags'] ?? ''))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->merge($select2Tags)
            ->unique()
            ->values();

        if ($newTags->isNotEmpty()) {
            Gate::authorize('create', Tag::class);

            $tags = array_merge(
                $tags,
                $newTags->map(fn (string $tag) => Tag::firstOrCreate(['name' => $tag])->id)->all(),
            );
        }

        unset(
            $data['new_category_description'],
            $data['new_category_name'],
            $data['new_category_slug'],
            $data['new_advertorial_ends_at'],
            $data['new_advertorial_name'],
            $data['new_advertorial_partner'],
            $data['new_advertorial_starts_at'],
            $data['new_tags'],
            $data['manual_author_names'],
            $data['tags'],
        );

        if (($data['type'] ?? 'article') !== 'video') {
            $data['youtube_url'] = null;
        }

        if (($data['type'] ?? 'article') !== 'gallery') {
            $data['gallery_images'] = null;
        }

        if (($data['type'] ?? 'article') !== 'infographic') {
            $data['infographic_image'] = null;
        }

        $post = Post::create($data);
        $this->recordWriterCredits($post, auth()->user(), $request->validated('manual_author_names'));

        // Sync tags
        if (! empty($tags)) {
            $post->tags()->sync(array_unique($tags));
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Show the form for editing a post.
     */
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        $categories = Category::orderBy('sort_order')->get();
        $advertorials = Advertorial::orderByDesc('starts_at')
            ->orderBy('name')
            ->get();
        $tags = Tag::orderBy('name')->get();
        $writerUsers = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
        $post->load(['tags', 'writerCredits']);

        return view('admin.posts.edit', compact('post', 'advertorials', 'categories', 'tags', 'writerUsers'));
    }

    /**
     * Update the specified post.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $data = $request->validated();

        if (filled($data['new_category_name'] ?? null)) {
            Gate::authorize('create', Category::class);

            $category = Category::create([
                'name' => $data['new_category_name'],
                'slug' => $data['new_category_slug'] ?? null,
                'description' => $data['new_category_description'] ?? null,
            ]);

            $data['category_id'] = $category->id;
        }

        if (filled($data['new_advertorial_name'] ?? null)) {
            $advertorial = Advertorial::create([
                'name' => $data['new_advertorial_name'],
                'partner' => $data['new_advertorial_partner'],
                'starts_at' => $data['new_advertorial_starts_at'],
                'ends_at' => $data['new_advertorial_ends_at'],
            ]);

            $data['advertorial_id'] = $advertorial->id;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')
                ->store('posts/featured', 'public');
        }

        if ($request->hasFile('gallery_images')) {
            $data['gallery_images'] = collect($request->file('gallery_images'))
                ->map(fn ($image) => $image->store('posts/gallery', 'public'))
                ->all();
        }

        if ($request->hasFile('infographic_image')) {
            $data['infographic_image'] = $request->file('infographic_image')
                ->store('posts/infographics', 'public');
        }

        if (auth()->user()->isReporter()) {
            $data['status'] = 'draft';
            $data['published_at'] = null;
        }

        $data = $this->normalizePublishState($data, $post);

        // Remove tags and writer users from data before updating (handled separately)
        $submittedTags = collect($data['tags'] ?? []);
        $tags = $submittedTags
            ->filter(fn (string $tag) => ctype_digit($tag))
            ->map(fn (string $tag) => (int) $tag)
            ->values()
            ->all();

        $select2Tags = $submittedTags
            ->filter(fn (string $tag) => str_starts_with($tag, '__new__:'))
            ->map(fn (string $tag) => trim(str_replace('__new__:', '', $tag)))
            ->filter()
            ->unique()
            ->values();

        if ($select2Tags->isNotEmpty()) {
            Gate::authorize('create', Tag::class);

            $tags = array_merge(
                $tags,
                $select2Tags->map(fn (string $tag) => Tag::firstOrCreate(['name' => $tag])->id)->all(),
            );
        }

        $writerUserIds = $data['writer_user_ids'] ?? [];
        unset(
            $data['new_category_description'],
            $data['new_category_name'],
            $data['new_category_slug'],
            $data['new_advertorial_ends_at'],
            $data['new_advertorial_name'],
            $data['new_advertorial_partner'],
            $data['new_advertorial_starts_at'],
            $data['writer_user_ids'],
            $data['tags'],
        );

        if (($data['type'] ?? $post->type) !== 'video') {
            $data['youtube_url'] = null;
        }

        if (($data['type'] ?? $post->type) !== 'gallery') {
            $data['gallery_images'] = null;
        }

        if (($data['type'] ?? $post->type) !== 'infographic') {
            $data['infographic_image'] = null;
        }

        $post->update($data);
        $this->recordWriterCredits($post, auth()->user(), null);
        $this->recordWriterCreditsFromUsers($post, auth()->user(), $writerUserIds);

        // Sync tags if provided
        if ($submittedTags->isNotEmpty()) {
            $post->tags()->sync(array_unique($tags));
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        // Delete featured image from storage
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }

    private function recordWriterCredits(Post $post, User $user, ?string $manualAuthorNames = null): void
    {
        $post->writerCredits()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'created_by_user_id' => $user->id,
                'name' => $user->name,
                'source' => 'auto',
            ],
        );

        if (! $user->isEditor()) {
            return;
        }

        $this->parseManualAuthorNames($manualAuthorNames)
            ->each(function (string $name) use ($post, $user) {
                $exists = $post->writerCredits()
                    ->whereRaw('lower(name) = ?', [strtolower($name)])
                    ->exists();

                if ($exists) {
                    return;
                }

                $post->writerCredits()->create([
                    'created_by_user_id' => $user->id,
                    'name' => $name,
                    'source' => 'manual',
                ]);
            });
    }

    private function parseManualAuthorNames(?string $names): Collection
    {
        return collect(preg_split('/[\r\n,]+/', $names ?? ''))
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique()
            ->values();
    }

    private function recordWriterCreditsFromUsers(Post $post, User $user, array $writerUserIds): void
    {
        if (! $user->isEditor()) {
            return;
        }

        User::query()
            ->whereIn('id', $writerUserIds)
            ->get(['id', 'name'])
            ->each(function (User $writerUser) use ($post, $user) {
                $post->writerCredits()->firstOrCreate(
                    ['user_id' => $writerUser->id],
                    [
                        'created_by_user_id' => $user->id,
                        'name' => $writerUser->name,
                        'source' => 'manual',
                    ],
                );
            });
    }

    private function normalizePublishState(array $data, ?Post $post = null): array
    {
        $status = $data['status'] ?? $post?->status ?? 'draft';
        $publishedAt = $data['published_at'] ?? $post?->published_at;

        if (blank($publishedAt)) {
            $publishedAt = null;
        } elseif (! $publishedAt instanceof CarbonInterface) {
            $publishedAt = Carbon::parse($publishedAt);
        }

        if ($status === 'scheduled') {
            $data['published_at'] = $publishedAt;

            return $data;
        }

        if ($status === 'published') {
            if ($publishedAt === null || $publishedAt->isFuture()) {
                $data['published_at'] = now();

                return $data;
            }

            $data['published_at'] = $publishedAt;

            return $data;
        }

        $data['published_at'] = null;

        return $data;
    }
}
