<?php

use App\Models\Post;
use Database\Seeders\PostTypeSeeder;
use Illuminate\Support\Facades\Storage;

it('seeds every supported post type with its extra fields', function () {
    Storage::fake('public');

    $this->seed(PostTypeSeeder::class);

    $article = Post::query()->where('type', 'article')->first();
    $video = Post::query()->where('type', 'video')->first();
    $gallery = Post::query()->where('type', 'gallery')->first();
    $infographic = Post::query()->where('type', 'infographic')->first();

    expect($article)->not->toBeNull();
    expect($video)->not->toBeNull();
    expect($gallery)->not->toBeNull();
    expect($infographic)->not->toBeNull();

    expect($video->youtube_url)->toStartWith('https://www.youtube.com/watch');
    expect($gallery->gallery_images)->toBeArray()->toHaveCount(3);
    expect($infographic->infographic_image)->not->toBeNull();

    Storage::disk('public')->assertExists($article->featured_image);
    Storage::disk('public')->assertExists($video->featured_image);
    Storage::disk('public')->assertExists($infographic->infographic_image);

    foreach ($gallery->gallery_images as $imagePath) {
        Storage::disk('public')->assertExists($imagePath);
    }

    expect($video->tags()->count())->toBeGreaterThan(0);
    expect($gallery->writerCredits()->count())->toBeGreaterThan(0);
});
