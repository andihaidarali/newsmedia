<?php

use App\Enums\Role;
use App\Models\User;

it('allows administrators to manage users', function () {
    $admin = User::factory()->administrator()->create();

    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertSee($admin->email);
});

it('prevents non administrators from managing users', function () {
    $editor = User::factory()->editor()->create();

    $response = $this->actingAs($editor)->get(route('admin.users.index'));

    $response->assertForbidden();
});

it('can create a user with a role', function () {
    $admin = User::factory()->administrator()->create();

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'News Editor',
        'email' => 'editor-news@example.com',
        'role' => Role::EDITOR->value,
        'is_active' => '0',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', [
        'email' => 'editor-news@example.com',
        'role' => Role::EDITOR->value,
        'is_active' => 0,
    ]);
});

it('keeps administrators active when updated', function () {
    $admin = User::factory()->administrator()->create();

    $response = $this->actingAs($admin)->patch(route('admin.users.update', $admin), [
        'name' => $admin->name,
        'email' => $admin->email,
        'role' => Role::ADMINISTRATOR->value,
        'is_active' => '0',
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
        'is_active' => 1,
    ]);
});

it('prevents administrators from deleting themselves', function () {
    $admin = User::factory()->administrator()->create();

    $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

    $response->assertForbidden();
    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
    ]);
});
