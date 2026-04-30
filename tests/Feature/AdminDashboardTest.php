<?php

use App\Models\Advertorial;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;

it('shows dashboard statistics, split production tables, and yearly performance charts', function () {
    Carbon::setTestNow('2026-04-29 10:00:00');

    $admin = User::factory()->administrator()->create([
        'name' => 'Administrator',
        'email' => 'admin@example.com',
    ]);

    $editor = User::factory()->editor()->create([
        'name' => 'Editor Satu',
        'email' => 'editor@example.com',
    ]);

    $reporter = User::factory()->reporter()->create([
        'name' => 'Reporter Satu',
        'email' => 'reporter@example.com',
    ]);

    $secondReporter = User::factory()->reporter()->create([
        'name' => 'Reporter Dua',
        'email' => 'reporter2@example.com',
    ]);

    $inactiveEditor = User::factory()->editor()->inactive()->create([
        'name' => 'Editor Nonaktif',
        'email' => 'inactive-editor@example.com',
    ]);

    Advertorial::factory()->create([
        'starts_at' => '2026-04-01',
        'ends_at' => '2026-04-30',
    ]);

    Advertorial::factory()->create([
        'starts_at' => '2026-03-01',
        'ends_at' => '2026-03-31',
    ]);

    Post::factory()->create([
        'title' => 'Editor April Post',
        'user_id' => $editor->id,
        'status' => 'published',
        'created_at' => '2026-04-10 09:00:00',
        'updated_at' => '2026-04-10 09:00:00',
        'published_at' => '2026-04-10 09:00:00',
    ])->writerCredits()->create([
        'user_id' => $editor->id,
        'created_by_user_id' => $editor->id,
        'name' => $editor->name,
        'source' => 'auto',
    ]);

    Post::factory()->create([
        'title' => 'Reporter April Post',
        'user_id' => $reporter->id,
        'status' => 'draft',
        'created_at' => '2026-04-11 09:00:00',
        'updated_at' => '2026-04-11 09:00:00',
        'published_at' => null,
    ]);

    Post::factory()->create([
        'title' => 'Reporter March Post',
        'user_id' => $reporter->id,
        'status' => 'published',
        'created_at' => '2026-03-15 09:00:00',
        'updated_at' => '2026-03-15 09:00:00',
        'published_at' => '2026-03-15 09:00:00',
    ])->writerCredits()->create([
        'user_id' => $reporter->id,
        'created_by_user_id' => $reporter->id,
        'name' => $reporter->name,
        'source' => 'auto',
    ]);

    Post::factory()->create([
        'title' => 'Reporter Dua April Post',
        'user_id' => $secondReporter->id,
        'status' => 'published',
        'created_at' => '2026-04-20 09:00:00',
        'updated_at' => '2026-04-20 09:00:00',
        'published_at' => '2026-04-20 09:00:00',
    ])->writerCredits()->create([
        'user_id' => $secondReporter->id,
        'created_by_user_id' => $secondReporter->id,
        'name' => $secondReporter->name,
        'source' => 'auto',
    ]);

    Post::factory()->create([
        'title' => 'Admin Authored With Editor And Reporter Credits',
        'user_id' => $admin->id,
        'status' => 'published',
        'created_at' => '2026-04-22 09:00:00',
        'updated_at' => '2026-04-22 09:00:00',
        'published_at' => '2026-04-22 09:00:00',
    ])->writerCredits()->createMany([
        [
            'user_id' => $editor->id,
            'created_by_user_id' => $admin->id,
            'name' => $editor->name,
            'source' => 'manual',
        ],
        [
            'user_id' => $reporter->id,
            'created_by_user_id' => $admin->id,
            'name' => $reporter->name,
            'source' => 'manual',
        ],
    ]);

    Post::factory()->create([
        'title' => 'Inactive Editor Published Post',
        'user_id' => $inactiveEditor->id,
        'status' => 'published',
        'created_at' => '2026-04-21 09:00:00',
        'updated_at' => '2026-04-21 09:00:00',
        'published_at' => '2026-04-21 09:00:00',
    ])->writerCredits()->create([
        'user_id' => $inactiveEditor->id,
        'created_by_user_id' => $admin->id,
        'name' => $inactiveEditor->name,
        'source' => 'manual',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard', [
        'month' => 4,
        'year' => 2026,
    ]));

    $response->assertOk();
    $response->assertSeeInOrder(['Total Post Dipublikasikan', '3']);
    $response->assertSee('April 2026');
    $response->assertSeeInOrder(['Drafts', '1']);
    $response->assertSeeInOrder(['Advertorial Aktif', '1']);
    $response->assertSeeInOrder(['Jumlah Users', '4']);
    $response->assertSee('Grafik Total Post 12 Bulan');
    $response->assertSee('Grafik Editor 12 Bulan');
    $response->assertSee('Grafik Reporter 12 Bulan');
    $response->assertSee('Tabel Editor');
    $response->assertSee('Tabel Reporter');

    $response->assertSee('Editor Satu');
    $response->assertSee('Reporter Satu');
    $response->assertSee('Reporter Dua');
    $response->assertSee('editor@example.com');
    $response->assertSee('reporter@example.com');
    $response->assertSee('reporter2@example.com');
    $response->assertDontSee('Editor Nonaktif');
    $response->assertDontSee('inactive-editor@example.com');

    $response->assertSeeInOrder([
        'Tabel Editor',
        'Editor Satu',
        'editor@example.com',
        '2',
    ]);

    $response->assertSeeInOrder([
        'Tabel Reporter',
        'Reporter Satu',
        'reporter@example.com',
        '1',
    ]);

    $response->assertSeeInOrder([
        'Reporter Dua',
        'reporter2@example.com',
        '1',
    ]);

    Carbon::setTestNow();
});
