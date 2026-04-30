<?php

use App\Models\AdBanner;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows editors and administrators to access the banner settings page', function () {
    $admin = User::factory()->administrator()->create();
    $editor = User::factory()->editor()->create();

    $this->actingAs($admin)
        ->get(route('admin.ad-banners.edit'))
        ->assertOk()
        ->assertSee('Banner-1')
        ->assertSee('Banner-12');

    $this->actingAs($editor)
        ->get(route('admin.ad-banners.edit'))
        ->assertOk();
});

it('prevents reporters from accessing banner settings', function () {
    $reporter = User::factory()->reporter()->create();

    $this->actingAs($reporter)
        ->get(route('admin.ad-banners.edit'))
        ->assertForbidden();
});

it('allows editors to update banner settings', function () {
    Storage::fake('public');

    $editor = User::factory()->editor()->create();

    $response = $this->actingAs($editor)->put(route('admin.ad-banners.update'), [
        'banners' => [
            1 => [
                'name' => 'Homepage Top Banner',
                'description' => 'Banner utama bagian atas homepage.',
                'image' => UploadedFile::fake()->create('banner-1.webp', 900, 'image/webp'),
            ],
            12 => [
                'name' => 'Footer Banner',
                'description' => 'Banner bagian footer.',
                'image' => UploadedFile::fake()->create('banner-12.webp', 700, 'image/webp'),
            ],
        ],
    ]);

    $response->assertRedirect(route('admin.ad-banners.edit'));

    $bannerOne = AdBanner::query()->where('slot_number', 1)->firstOrFail();
    $bannerTwelve = AdBanner::query()->where('slot_number', 12)->firstOrFail();

    expect($bannerOne->name)->toBe('Homepage Top Banner');
    expect($bannerOne->description)->toBe('Banner utama bagian atas homepage.');
    expect($bannerOne->image_path)->not->toBeNull();

    expect($bannerTwelve->name)->toBe('Footer Banner');
    expect($bannerTwelve->description)->toBe('Banner bagian footer.');
    expect($bannerTwelve->image_path)->not->toBeNull();
});
