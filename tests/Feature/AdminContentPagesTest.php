<?php

use App\Models\Advertorial;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('renders post management pages', function () {
    $user = User::factory()->editor()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->get(route('admin.posts.index'))->assertOk();
    $this->actingAs($user)->get(route('admin.posts.create'))->assertOk();
    $this->actingAs($user)->get(route('admin.posts.edit', $post))->assertOk();
});

it('filters posts by advertorial, month, and year on the index page', function () {
    Carbon::setTestNow('2026-04-29 10:00:00');

    $user = User::factory()->editor()->create();
    $advertorial = Advertorial::factory()->create(['name' => 'Advertorial A']);
    $otherAdvertorial = Advertorial::factory()->create(['name' => 'Advertorial B']);

    $matchingPost = Post::factory()->create([
        'title' => 'Matching Advertorial Post',
        'user_id' => $user->id,
        'advertorial_id' => $advertorial->id,
        'status' => 'published',
        'created_at' => '2026-04-10 09:00:00',
        'updated_at' => '2026-04-10 09:00:00',
        'published_at' => '2026-04-10 09:00:00',
    ]);

    Post::factory()->create([
        'title' => 'Wrong Advertorial Post',
        'user_id' => $user->id,
        'advertorial_id' => $otherAdvertorial->id,
        'status' => 'published',
        'created_at' => '2026-04-12 09:00:00',
        'updated_at' => '2026-04-12 09:00:00',
        'published_at' => '2026-04-12 09:00:00',
    ]);

    Post::factory()->create([
        'title' => 'Wrong Month Post',
        'user_id' => $user->id,
        'advertorial_id' => $advertorial->id,
        'status' => 'published',
        'created_at' => '2026-03-15 09:00:00',
        'updated_at' => '2026-03-15 09:00:00',
        'published_at' => '2026-03-15 09:00:00',
    ]);

    Post::factory()->create([
        'title' => 'Draft In Target Month',
        'user_id' => $user->id,
        'advertorial_id' => $advertorial->id,
        'status' => 'draft',
        'created_at' => '2026-04-16 09:00:00',
        'updated_at' => '2026-04-16 09:00:00',
        'published_at' => null,
    ]);

    $this->actingAs($user)
        ->get(route('admin.posts.index', [
            'advertorial_id' => $advertorial->id,
            'month' => 4,
            'year' => 2026,
        ]))
        ->assertOk()
        ->assertSee($matchingPost->title)
        ->assertDontSee('Draft In Target Month')
        ->assertDontSee('Wrong Advertorial Post')
        ->assertDontSee('Wrong Month Post');

    Carbon::setTestNow();
});

it('renders category management pages', function () {
    $user = User::factory()->editor()->create();
    $category = Category::factory()->create();

    $this->actingAs($user)->get(route('admin.categories.index'))->assertOk();
    $this->actingAs($user)->get(route('admin.categories.create'))->assertOk();
    $this->actingAs($user)->get(route('admin.categories.edit', $category))->assertOk();
});

it('renders tag management pages', function () {
    $user = User::factory()->editor()->create();
    $tag = Tag::factory()->create();

    $this->actingAs($user)->get(route('admin.tags.index'))->assertOk();
    $this->actingAs($user)->get(route('admin.tags.create'))->assertOk();
    $this->actingAs($user)->get(route('admin.tags.edit', $tag))->assertOk();
});

it('renders advertorial management pages and the detail page', function () {
    $user = User::factory()->editor()->create();
    $advertorial = Advertorial::factory()->create(['name' => 'Ramadan Campaign']);
    $post = Post::factory()->create([
        'title' => 'Sponsored Post',
        'advertorial_id' => $advertorial->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('admin.advertorials.index'))
        ->assertOk()
        ->assertSee('Ramadan Campaign')
        ->assertDontSee('Sponsored Post');

    $this->actingAs($user)->get(route('admin.advertorials.create'))->assertOk();
    $this->actingAs($user)->get(route('admin.advertorials.edit', $advertorial))->assertOk();
    $this->actingAs($user)
        ->get(route('admin.advertorials.show', $advertorial))
        ->assertOk()
        ->assertSee('Save as PDF')
        ->assertSee('Sponsored Post');
});

it('prevents reporters from accessing advertorial pages', function () {
    $reporter = User::factory()->reporter()->create();
    $advertorial = Advertorial::factory()->create();

    $this->actingAs($reporter)->get(route('admin.advertorials.index'))->assertForbidden();
    $this->actingAs($reporter)->get(route('admin.advertorials.create'))->assertForbidden();
    $this->actingAs($reporter)->get(route('admin.advertorials.show', $advertorial))->assertForbidden();
    $this->actingAs($reporter)->get(route('admin.advertorials.edit', $advertorial))->assertForbidden();
});

it('can create an advertorial from the admin page', function () {
    $user = User::factory()->editor()->create();

    $response = $this->actingAs($user)->post(route('admin.advertorials.store'), [
        'name' => 'Election Coverage',
        'partner' => 'PT Kolaborasi Media',
        'starts_at' => '2026-05-01',
        'ends_at' => '2026-06-01',
    ]);

    $response->assertRedirect(route('admin.advertorials.index'));

    $this->assertDatabaseHas('advertorials', [
        'name' => 'Election Coverage',
        'partner' => 'PT Kolaborasi Media',
    ]);
});

it('can create a post with inline category and tags', function () {
    $user = User::factory()->editor()->create();
    $existingTag = Tag::factory()->create(['name' => 'Existing Tag']);

    $response = $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'Inline Taxonomy Post',
        'body' => 'This post creates taxonomy from the post form.',
        'status' => 'draft',
        'new_category_name' => 'Inline Category',
        'tags' => [(string) $existingTag->id, '__new__:Breaking News', '__new__:Analysis'],
    ]);

    $response->assertRedirect(route('admin.posts.index'));

    $this->assertDatabaseHas('categories', ['name' => 'Inline Category']);
    $this->assertDatabaseHas('tags', ['name' => 'Breaking News']);
    $this->assertDatabaseHas('tags', ['name' => 'Analysis']);

    $post = Post::where('title', 'Inline Taxonomy Post')->firstOrFail();

    expect($post->category?->name)->toBe('Inline Category');
    expect($post->tags()->pluck('name')->all())->toContain('Existing Tag', 'Breaking News', 'Analysis');
});

it('records reporter as the automatic writer and ignores manual names from reporters', function () {
    $reporter = User::factory()->reporter()->create();

    $response = $this->actingAs($reporter)->post(route('admin.posts.store'), [
        'title' => 'Reporter Written Post',
        'body' => 'Reporter wrote this directly.',
        'status' => 'draft',
        'manual_author_names' => 'Manual Name',
    ]);

    $response->assertRedirect(route('admin.posts.index'));

    $post = Post::where('title', 'Reporter Written Post')->firstOrFail();

    expect($post->writerCredits()->pluck('name')->all())->toBe([$reporter->name]);
    expect($post->writerCredits()->where('source', 'auto')->count())->toBe(1);
});

it('always saves reporter-created posts as draft', function () {
    $reporter = User::factory()->reporter()->create();

    $response = $this->actingAs($reporter)->post(route('admin.posts.store'), [
        'title' => 'Reporter Forced Draft Post',
        'body' => 'Reporter attempted to publish this.',
        'status' => 'published',
    ]);

    $response->assertRedirect(route('admin.posts.index'));

    $post = Post::where('title', 'Reporter Forced Draft Post')->firstOrFail();

    expect($post->status)->toBe('draft');
    expect($post->published_at)->toBeNull();
});

it('shows the status input only for editors and administrators', function () {
    $editor = User::factory()->editor()->create();
    $administrator = User::factory()->administrator()->create();
    $reporter = User::factory()->reporter()->create();
    $post = Post::factory()->create(['user_id' => $editor->id]);

    $this->actingAs($editor)
        ->get(route('admin.posts.create'))
        ->assertOk()
        ->assertSee('Status');

    $this->actingAs($administrator)
        ->get(route('admin.posts.edit', $post))
        ->assertOk()
        ->assertSee('Status');

    $this->actingAs($reporter)
        ->get(route('admin.posts.create'))
        ->assertOk()
        ->assertDontSee('Status');
});

it('allows editors to add manual writer names while recording themselves automatically', function () {
    $editor = User::factory()->editor()->create(['name' => 'News Editor']);

    $response = $this->actingAs($editor)->post(route('admin.posts.store'), [
        'title' => 'Editor Written Post',
        'body' => 'Editor wrote this directly.',
        'status' => 'draft',
        'manual_author_names' => "Guest Writer\nSecond Writer",
    ]);

    $response->assertRedirect(route('admin.posts.index'));

    $post = Post::where('title', 'Editor Written Post')->firstOrFail();
    $names = $post->writerCredits()->orderBy('id')->pluck('name')->all();

    expect($names)->toContain('News Editor', 'Guest Writer', 'Second Writer');
    expect($post->writerCredits()->where('source', 'auto')->pluck('name')->all())->toBe(['News Editor']);
});

it('records an editor automatically when editing a reporter post', function () {
    $reporter = User::factory()->reporter()->create(['name' => 'Field Reporter']);
    $editor = User::factory()->editor()->create(['name' => 'Desk Editor']);
    $secondReporter = User::factory()->reporter()->create(['name' => 'Second Reporter']);

    $this->actingAs($reporter)->post(route('admin.posts.store'), [
        'title' => 'Reporter Post For Editing',
        'body' => 'Reporter wrote this.',
        'status' => 'draft',
    ])->assertRedirect(route('admin.posts.index'));

    $post = Post::where('title', 'Reporter Post For Editing')->firstOrFail();

    $this->actingAs($editor)->patch(route('admin.posts.update', $post), [
        'title' => 'Reporter Post For Editing',
        'body' => 'Editor revised this.',
        'status' => 'draft',
        'writer_user_ids' => [$secondReporter->id],
    ])->assertRedirect(route('admin.posts.index'));

    expect($post->fresh()->writerCredits()->pluck('name')->all())
        ->toContain('Field Reporter', 'Desk Editor', 'Second Reporter');
});

it('requires a future publish date only for scheduled posts and allows past dates when published', function () {
    $editor = User::factory()->editor()->create();
    $post = Post::factory()->draft()->create([
        'user_id' => $editor->id,
        'title' => 'Status Transition Post',
    ]);

    $this->actingAs($editor)
        ->patch(route('admin.posts.update', $post), [
            'title' => 'Status Transition Post',
            'body' => 'Scheduled body',
            'type' => 'article',
            'status' => 'scheduled',
            'published_at' => now()->subHour()->format('Y-m-d\\TH:i'),
        ])
        ->assertSessionHasErrors('published_at');

    $this->actingAs($editor)
        ->patch(route('admin.posts.update', $post), [
            'title' => 'Status Transition Post',
            'body' => 'Published body',
            'type' => 'article',
            'status' => 'published',
            'published_at' => now()->subHour()->format('Y-m-d\\TH:i'),
        ])
        ->assertRedirect(route('admin.posts.index'));

    expect($post->fresh()->status)->toBe('published');
    expect($post->fresh()->published_at?->isPast())->toBeTrue();
});

it('forces published posts edited from scheduled to use a non-future publish date', function () {
    $editor = User::factory()->editor()->create();
    $post = Post::factory()->scheduled()->create([
        'user_id' => $editor->id,
        'title' => 'Scheduled To Published Post',
        'published_at' => now()->addDay(),
    ]);

    $this->actingAs($editor)
        ->patch(route('admin.posts.update', $post), [
            'title' => 'Scheduled To Published Post',
            'body' => 'Published body',
            'type' => 'article',
            'status' => 'published',
            'published_at' => now()->addDay()->format('Y-m-d\\TH:i'),
        ])
        ->assertRedirect(route('admin.posts.index'));

    expect($post->fresh()->status)->toBe('published');
    expect($post->fresh()->published_at?->isFuture())->toBeFalse();
});

it('can create a post with inline advertorial', function () {
    $user = User::factory()->editor()->create();

    $response = $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'Inline Advertorial Post',
        'body' => 'This post creates an advertorial from the post form.',
        'status' => 'draft',
        'new_advertorial_name' => 'Ramadan Campaign',
        'new_advertorial_partner' => 'PT Media Partner',
        'new_advertorial_starts_at' => '2026-05-01',
        'new_advertorial_ends_at' => '2026-05-31',
    ]);

    $response->assertRedirect(route('admin.posts.index'));

    $this->assertDatabaseHas('advertorials', [
        'name' => 'Ramadan Campaign',
        'partner' => 'PT Media Partner',
    ]);

    $post = Post::where('title', 'Inline Advertorial Post')->firstOrFail();

    expect($post->advertorial?->name)->toBe('Ramadan Campaign');
});

it('can create a post with an existing advertorial', function () {
    $user = User::factory()->editor()->create();
    $advertorial = Advertorial::factory()->create();

    $response = $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'Existing Advertorial Post',
        'body' => 'This post uses an existing advertorial.',
        'status' => 'draft',
        'advertorial_id' => $advertorial->id,
    ]);

    $response->assertRedirect(route('admin.posts.index'));

    $post = Post::where('title', 'Existing Advertorial Post')->firstOrFail();

    expect($post->advertorial_id)->toBe($advertorial->id);
});

it('prevents reporters from creating categories inside the post form', function () {
    $user = User::factory()->reporter()->create();

    $response = $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'Reporter Category Post',
        'body' => 'Reporter should not create categories here.',
        'status' => 'draft',
        'new_category_name' => 'Reporter Category',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('categories', ['name' => 'Reporter Category']);
});

it('can create each supported post type', function () {
    Storage::fake('public');

    $user = User::factory()->editor()->create();

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'Video Post',
        'body' => '<p>Video body</p>',
        'type' => 'video',
        'status' => 'draft',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    ])->assertRedirect(route('admin.posts.index'));

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'Gallery Post',
        'body' => '<p>Gallery body</p>',
        'type' => 'gallery',
        'status' => 'draft',
        'gallery_images' => [
            UploadedFile::fake()->image('one.jpg'),
            UploadedFile::fake()->image('two.jpg'),
        ],
    ])->assertRedirect(route('admin.posts.index'));

    $this->actingAs($user)->post(route('admin.posts.store'), [
        'title' => 'Infographic Post',
        'body' => '<p>Infographic body</p>',
        'type' => 'infographic',
        'status' => 'draft',
        'infographic_image' => UploadedFile::fake()->image('info.png'),
    ])->assertRedirect(route('admin.posts.index'));

    expect(Post::where('title', 'Video Post')->firstOrFail()->youtube_url)
        ->toBe('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    expect(Post::where('title', 'Gallery Post')->firstOrFail()->gallery_images)->toHaveCount(2);
    expect(Post::where('title', 'Infographic Post')->firstOrFail()->infographic_image)->not->toBeNull();
});
