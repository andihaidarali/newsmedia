<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

it('can run the database seeder more than once without duplicating default users', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(User::query()->where('email', 'admin@example.com')->count())->toBe(1);
    expect(User::query()->where('email', 'editor@example.com')->count())->toBe(1);
    expect(User::query()->where('email', 'reporter@example.com')->count())->toBe(1);
});
