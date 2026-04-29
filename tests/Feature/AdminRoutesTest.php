<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests from admin routes', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('login'));
});

it('allows authenticated users to access admin routes', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertStatus(200);
});

it('can view posts index', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('admin.posts.index'));

    $response->assertStatus(200);
});

it('can store a category', function () {
    $user = User::factory()->editor()->create();

    $response = $this->actingAs($user)->post(route('admin.categories.store'), [
        'name' => 'New Category',
        'sort_order' => 1,
    ]);

    $response->assertRedirect(route('admin.categories.index'));
    $this->assertDatabaseHas('categories', [
        'name' => 'New Category',
    ]);
});
