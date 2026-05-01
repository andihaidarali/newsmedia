<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Create Users ─────────────────────────────
        $adminSeed = User::factory()->administrator()->make([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
        ]);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'role' => Role::ADMINISTRATOR,
                'is_active' => true,
                'email_verified_at' => $adminSeed->email_verified_at,
                'password' => $adminSeed->password,
                'remember_token' => $adminSeed->remember_token,
            ],
        );

        $editorSeed = User::factory()->editor()->make([
            'name' => 'Editor',
            'email' => 'editor@example.com',
        ]);

        $editor = User::query()->updateOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor',
                'role' => Role::EDITOR,
                'is_active' => true,
                'email_verified_at' => $editorSeed->email_verified_at,
                'password' => $editorSeed->password,
                'remember_token' => $editorSeed->remember_token,
            ],
        );

        $reporterSeed = User::factory()->reporter()->make([
            'name' => 'Reporter',
            'email' => 'reporter@example.com',
        ]);

        $reporter = User::query()->updateOrCreate(
            ['email' => 'reporter@example.com'],
            [
                'name' => 'Reporter',
                'role' => Role::REPORTER,
                'is_active' => true,
                'email_verified_at' => $reporterSeed->email_verified_at,
                'password' => $reporterSeed->password,
                'remember_token' => $reporterSeed->remember_token,
            ],
        );

        // ── Create Categories ────────────────────────
        $parentCategories = collect([
            'Technology', 'Business', 'Lifestyle', 'Health',
            'Education', 'Entertainment', 'Sports', 'Science',
        ])->map(fn ($name, $index) => Category::query()->firstOrCreate(
            ['name' => $name, 'parent_id' => null],
            ['sort_order' => $index],
        ));

        // Create some sub-categories
        $subCategories = [
            'Technology' => ['Web Development', 'Mobile Apps', 'AI & Machine Learning'],
            'Business' => ['Startups', 'Marketing'],
            'Lifestyle' => ['Travel', 'Food & Cooking'],
        ];

        foreach ($subCategories as $parentName => $children) {
            $parent = $parentCategories->firstWhere('name', $parentName);
            foreach ($children as $childName) {
                Category::query()->firstOrCreate(
                    ['name' => $childName, 'parent_id' => $parent?->id],
                    ['sort_order' => 0],
                );
            }
        }

        $allCategories = Category::all();

        // ── Create Tags ──────────────────────────────
        $tags = collect([
            'Laravel', 'PHP', 'JavaScript', 'Vue.js', 'React',
            'Tutorial', 'Tips & Tricks', 'News', 'Opinion', 'Review',
            'Guide', 'Beginner', 'Advanced', 'API', 'Database',
            'Frontend', 'Backend', 'DevOps', 'Mobile', 'AI',
        ])->map(fn ($name) => Tag::query()->firstOrCreate(['name' => $name]));

        // ── Create Posts ─────────────────────────────
        if (! Post::query()->exists()) {
            Post::factory()
                ->count(40)
                ->sequence(fn () => [
                    'user_id' => $admin->id,
                    'category_id' => $allCategories->random()->id,
                ])
                ->create()
                ->each(function (Post $post) use ($tags) {
                    $post->tags()->attach(
                        $tags->random(rand(1, 4))->pluck('id')->toArray()
                    );
                });
        }

        $this->call(PostTypeSeeder::class);

        $this->command->info('✅ Seeded: 1 admin, '
            .$allCategories->count().' categories, '
            .$tags->count().' tags, '
            .Post::count().' posts including typed content');
    }
}
