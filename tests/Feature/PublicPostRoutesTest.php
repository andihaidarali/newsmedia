<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('builds the public post url from the full category tree', function () {
    $user = User::factory()->create();
    $parentCategory = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);
    $childCategory = Category::factory()->child($parentCategory)->create([
        'name' => 'Politics',
        'slug' => 'politics',
    ]);
    $grandchildCategory = Category::factory()->child($childCategory)->create([
        'name' => 'Election',
        'slug' => 'election',
    ]);
    $post = Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $grandchildCategory->id,
        'slug' => 'article-slug-title',
    ]);

    expect($post->category_path)->toBe('news/politics/election');
    expect($grandchildCategory->publicUrl())->toBe(url('/news/politics/election'));
    expect($post->publicUrl())->toBe(url('/news/politics/election/article-slug-title'));
});

it('shows a published post only on the matching category path', function () {
    $user = User::factory()->create();
    $parentCategory = Category::factory()->create(['slug' => 'news']);
    $childCategory = Category::factory()->child($parentCategory)->create(['slug' => 'politics']);
    $post = Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $childCategory->id,
        'slug' => 'match-path-post',
    ]);

    $this->get('/news/politics/match-path-post')->assertOk();
    $this->get('/news/wrong-path/match-path-post')->assertNotFound();
});

it('renders public listing pages with the nested public post url', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);
    $childCategory = Category::factory()->child($category)->create([
        'name' => 'Politics',
        'slug' => 'politics',
    ]);
    $post = Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $childCategory->id,
        'title' => 'Public Listing Post',
        'slug' => 'public-listing-post',
    ]);

    $this->get(route('posts.index'))
        ->assertOk()
        ->assertSee($post->publicUrl());

    $this->get($category->publicUrl())
        ->assertOk();
});

it('includes descendant posts on the parent category page', function () {
    $user = User::factory()->create();
    $parentCategory = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);
    $childCategory = Category::factory()->child($parentCategory)->create([
        'name' => 'Politics',
        'slug' => 'politics',
    ]);
    $grandchildCategory = Category::factory()->child($childCategory)->create([
        'name' => 'Election',
        'slug' => 'election',
    ]);

    $descendantPost = Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $grandchildCategory->id,
        'title' => 'Descendant Category Post',
        'slug' => 'descendant-category-post',
    ]);

    $otherCategory = Category::factory()->create([
        'name' => 'Sports',
        'slug' => 'sports',
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $otherCategory->id,
        'title' => 'Other Category Post',
        'slug' => 'other-category-post',
    ]);

    $this->get($parentCategory->publicUrl())
        ->assertOk()
        ->assertSee('Descendant Category Post')
        ->assertSee($descendantPost->publicUrl());
});

it('shows nested category pages by full parent-child slug path', function () {
    $parentCategory = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);
    $childCategory = Category::factory()->child($parentCategory)->create([
        'name' => 'Politics',
        'slug' => 'politics',
    ]);

    $this->get('/news/politics')
        ->assertOk()
        ->assertSee('Politics');
});

it('returns load more payload for the public listing without triggering extra page navigation', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);

    Post::factory()->count(7)->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $response = $this->get(route('posts.index', [
        'page' => 2,
        'load_more' => 1,
    ]), [
        'X-Requested-With' => 'XMLHttpRequest',
        'Accept' => 'application/json',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'html',
            'next_page_url',
        ]);
});

it('uses the post type media as the public thumbnail source', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);

    Storage::disk('public')->put('posts/infographics/thumb-info.png', 'image');

    $videoPost = Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Video Thumbnail Post',
        'type' => 'video',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'featured_image' => 'posts/featured/unused-video.png',
    ]);

    $infographicPost = Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Infographic Thumbnail Post',
        'type' => 'infographic',
        'infographic_image' => 'posts/infographics/thumb-info.png',
        'featured_image' => 'posts/featured/unused-info.png',
    ]);

    $response = $this->get(route('posts.index'));

    $response->assertOk()
        ->assertSee('https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg', false)
        ->assertSee(Storage::disk('public')->url('posts/infographics/thumb-info.png'), false);
});

it('shows public content type badges in listing pages', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Video Badge Post',
        'type' => 'video',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Infographic Badge Post',
        'type' => 'infographic',
        'infographic_image' => 'posts/infographics/infographic-badge.png',
    ]);

    $this->get(route('posts.index'))
        ->assertOk()
        ->assertSee('Video')
        ->assertSee('Infografis');
});

it('renders gallery posts with slider images and without showing the featured image on the detail page', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);

    Storage::disk('public')->put('posts/featured/gallery-featured.png', 'featured');
    Storage::disk('public')->put('posts/gallery/gallery-slide-1.png', 'slide-1');
    Storage::disk('public')->put('posts/gallery/gallery-slide-2.png', 'slide-2');

    $post = Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'type' => 'gallery',
        'title' => 'Gallery Slider Post',
        'slug' => 'gallery-slider-post',
        'featured_image' => 'posts/featured/gallery-featured.png',
        'gallery_images' => [
            'posts/gallery/gallery-slide-1.png',
            'posts/gallery/gallery-slide-2.png',
        ],
    ]);

    $featuredImageUrl = Storage::disk('public')->url('posts/featured/gallery-featured.png');

    $this->get($post->publicUrl())
        ->assertOk()
        ->assertSee(Storage::disk('public')->url('posts/gallery/gallery-slide-1.png'), false)
        ->assertSee(Storage::disk('public')->url('posts/gallery/gallery-slide-2.png'), false)
        ->assertSee('<meta property="og:image" content="'.$featuredImageUrl.'">', false)
        ->assertDontSee('<img src="'.$featuredImageUrl.'"', false);
});

it('uses the default featured image only in meta tags when a public post has no rendered image', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $category = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);

    Storage::disk('public')->put('site-settings/default/default-featured.png', 'default-image');

    SiteSetting::query()->create([
        'default_featured_image' => 'site-settings/default/default-featured.png',
    ]);

    $post = Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Post Without Featured Image',
        'slug' => 'post-without-featured-image',
        'type' => 'article',
        'featured_image' => null,
    ]);

    $defaultImageUrl = Storage::disk('public')->url('site-settings/default/default-featured.png');

    $this->get($post->publicUrl())
        ->assertOk()
        ->assertSee('<meta property="og:image" content="'.$defaultImageUrl.'">', false)
        ->assertSee('<meta name="twitter:image" content="'.$defaultImageUrl.'">', false)
        ->assertDontSee('<img src="'.$defaultImageUrl.'"', false);
});

it('hides the latest-news thumbnail slot for posts without a featured image', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Post Without List Thumbnail',
        'slug' => 'post-without-list-thumbnail',
        'featured_image' => null,
        'type' => 'article',
    ]);

    $this->get($category->publicUrl())
        ->assertOk()
        ->assertSee('Post Without List Thumbnail')
        ->assertDontSee('h-[98px] w-[98px] shrink-0 overflow-hidden rounded-md', false);
});

it('hides compact thumbnail slots when posts do not have renderable media', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Headline Without Media',
        'slug' => 'headline-without-media',
        'featured_image' => null,
        'type' => 'article',
        'published_at' => now()->subMinute(),
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Compact Without Media',
        'slug' => 'compact-without-media',
        'featured_image' => null,
        'type' => 'article',
        'published_at' => now()->subMinutes(2),
    ]);

    $this->get(route('posts.index'))
        ->assertOk()
        ->assertSee('Compact Without Media')
        ->assertDontSee('h-20 w-20 shrink-0 overflow-hidden rounded-lg', false);
});

it('renders dedicated homepage sections for video, gallery, and infographic content', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Homepage Video Post',
        'type' => 'video',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Homepage Gallery Post',
        'type' => 'gallery',
        'featured_image' => 'posts/featured/gallery-home.png',
        'gallery_images' => ['posts/gallery/home-gallery-1.png'],
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Homepage Infographic Post',
        'type' => 'infographic',
        'featured_image' => 'posts/featured/infographic-home.png',
        'infographic_image' => 'posts/infographics/infographic-home.png',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Video Pilihan')
        ->assertSee('Galeri Foto')
        ->assertSee('Infografis')
        ->assertSee('Homepage Video Post')
        ->assertSee('Homepage Gallery Post')
        ->assertSee('Homepage Infographic Post');
});

it('shows visual media overlays for video and gallery cards on the homepage', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Overlay Video Post',
        'type' => 'video',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Overlay Gallery Post',
        'type' => 'gallery',
        'featured_image' => 'posts/featured/overlay-gallery.png',
        'gallery_images' => ['posts/gallery/overlay-gallery-1.png'],
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('aria-label="Play video"', false)
        ->assertSee('aria-label="Open gallery"', false);
});
