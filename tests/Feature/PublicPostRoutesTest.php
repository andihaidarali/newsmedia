<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

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
    expect($post->publicUrl())->toBe(route('posts.show', [
        'categoryPath' => 'news/politics/election',
        'post' => $post,
    ]));
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

    $this->get(route('categories.show', $category))
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

    $this->get(route('categories.show', $parentCategory))
        ->assertOk()
        ->assertSee('Descendant Category Post')
        ->assertSee($descendantPost->publicUrl());
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
