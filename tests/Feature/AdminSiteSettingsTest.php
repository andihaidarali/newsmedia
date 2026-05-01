<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows administrators to view the site settings page', function () {
    $admin = User::factory()->administrator()->create();

    $this->actingAs($admin)
        ->get(route('admin.site-settings.edit'))
        ->assertOk()
        ->assertSee('Site Settings');
});

it('prevents non administrators from accessing the site settings page', function () {
    $editor = User::factory()->editor()->create();

    $this->actingAs($editor)
        ->get(route('admin.site-settings.edit'))
        ->assertForbidden();
});

it('prevents reporters from accessing or updating site settings', function () {
    Storage::fake('public');

    $reporter = User::factory()->reporter()->create();

    $this->actingAs($reporter)
        ->get(route('admin.site-settings.edit'))
        ->assertForbidden();

    $this->actingAs($reporter)
        ->put(route('admin.site-settings.update'), [
            'site_title' => 'Blocked Update',
        ])
        ->assertForbidden();
});

it('allows administrators to update site settings', function () {
    Storage::fake('public');

    $admin = User::factory()->administrator()->create();

    $response = $this->actingAs($admin)->put(route('admin.site-settings.update'), [
        'site_title' => 'News Portal',
        'subtitle' => 'Daily updates',
        'site_description' => 'Portal berita harian.',
        'site_logo' => UploadedFile::fake()->image('logo.png'),
        'site_favicon' => UploadedFile::fake()->image('favicon.png'),
        'default_featured_image' => UploadedFile::fake()->image('default-featured.png'),
        'social_links' => [
            ['platform' => 'facebook', 'url' => 'https://facebook.com/newsportal'],
            ['platform' => 'youtube', 'url' => 'https://youtube.com/@newsportal'],
        ],
    ]);

    $response->assertRedirect(route('admin.site-settings.edit'));

    $setting = SiteSetting::current();

    expect($setting->site_title)->toBe('News Portal');
    expect($setting->subtitle)->toBe('Daily updates');
    expect($setting->site_description)->toBe('Portal berita harian.');
    expect($setting->site_logo)->not->toBeNull();
    expect($setting->site_favicon)->not->toBeNull();
    expect($setting->default_featured_image)->not->toBeNull();
    expect($setting->social_links)->toHaveCount(2);
    expect($setting->social_links[0]['platform'])->toBe('facebook');
    expect($setting->social_links[1]['url'])->toBe('https://youtube.com/@newsportal');
});

it('applies site settings to the admin layout', function () {
    $admin = User::factory()->administrator()->create();

    SiteSetting::query()->create([
        'site_title' => 'News Portal Admin',
        'subtitle' => 'Editorial Desk',
        'site_description' => 'Dashboard berita',
        'site_favicon' => 'site-settings/favicon/test.png',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('News Portal Admin')
        ->assertSee('Editorial Desk')
        ->assertSee('site-settings/favicon/test.png');
});

it('renders configured social media links in the public layout', function () {
    $category = Category::factory()->create([
        'name' => 'News',
        'slug' => 'news',
    ]);
    $user = User::factory()->create();

    SiteSetting::query()->create([
        'site_title' => 'News Portal',
        'social_links' => [
            ['platform' => 'facebook', 'url' => 'https://facebook.com/newsportal'],
            ['platform' => 'instagram', 'url' => 'https://instagram.com/newsportal'],
        ],
    ]);

    Post::factory()->published()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Public Social Link Post',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('https://facebook.com/newsportal')
        ->assertSee('https://instagram.com/newsportal')
        ->assertSee('Facebook')
        ->assertSee('Instagram');
});
