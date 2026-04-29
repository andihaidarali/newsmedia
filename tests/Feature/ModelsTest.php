<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a category with slug', function () {
    $category = Category::factory()->create(['name' => 'Technology']);
    expect($category->slug)->toBeString();
    expect($category->slug)->toContain('technology');
});

it('can create a tag with slug', function () {
    $tag = Tag::factory()->create(['name' => 'Laravel Framework']);
    expect($tag->slug)->toBeString();
    expect($tag->slug)->toContain('laravel-framework');
});

it('can create a post and attach tags', function () {
    $post = Post::factory()->create();
    $tag = Tag::factory()->create();

    $post->tags()->attach($tag);

    expect($post->tags->count())->toBe(1);
    expect($post->tags->first()->id)->toBe($tag->id);
});

it('can filter posts by published scope', function () {
    Post::factory()->count(3)->published()->create();
    Post::factory()->count(2)->draft()->create();

    expect(Post::published()->count())->toBe(3);
    expect(Post::draft()->count())->toBe(2);
});

it('ensures slug is unique', function () {
    $post1 = Post::factory()->create(['title' => 'My Post Title']);
    $post2 = Post::factory()->create(['title' => 'My Post Title']);

    expect($post1->slug)->not->toBe($post2->slug);
    expect($post2->slug)->toStartWith('my-post-title-');
});
