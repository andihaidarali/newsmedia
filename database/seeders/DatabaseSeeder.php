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
        $admin = User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'role' => Role::ADMINISTRATOR,
        ]);

        $editor = User::factory()->create([
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'role' => Role::EDITOR,
        ]);

        $reporter = User::factory()->create([
            'name' => 'Reporter',
            'email' => 'reporter@example.com',
            'role' => Role::REPORTER,
        ]);

        // ── Create Categories ────────────────────────
        $parentCategories = collect([
            'Technology', 'Business', 'Lifestyle', 'Health',
            'Education', 'Entertainment', 'Sports', 'Science',
        ])->map(fn ($name, $index) => Category::factory()->create([
            'name' => $name,
            'sort_order' => $index,
        ]));

        // Create some sub-categories
        $subCategories = [
            'Technology' => ['Web Development', 'Mobile Apps', 'AI & Machine Learning'],
            'Business' => ['Startups', 'Marketing'],
            'Lifestyle' => ['Travel', 'Food & Cooking'],
        ];

        foreach ($subCategories as $parentName => $children) {
            $parent = $parentCategories->firstWhere('name', $parentName);
            foreach ($children as $childName) {
                Category::factory()->child($parent)->create([
                    'name' => $childName,
                ]);
            }
        }

        $allCategories = Category::all();

        // ── Create Tags ──────────────────────────────
        $tags = collect([
            'Laravel', 'PHP', 'JavaScript', 'Vue.js', 'React',
            'Tutorial', 'Tips & Tricks', 'News', 'Opinion', 'Review',
            'Guide', 'Beginner', 'Advanced', 'API', 'Database',
            'Frontend', 'Backend', 'DevOps', 'Mobile', 'AI',
        ])->map(fn ($name) => Tag::factory()->create(['name' => $name]));

        // ── Create Posts ─────────────────────────────
        Post::factory()
            ->count(40)
            ->sequence(fn () => [
                'user_id' => $admin->id,
                'category_id' => $allCategories->random()->id,
            ])
            ->create()
            ->each(function (Post $post) use ($tags) {
                // Attach 1-4 random tags to each post
                $post->tags()->attach(
                    $tags->random(rand(1, 4))->pluck('id')->toArray()
                );
            });

        $this->command->info('✅ Seeded: 1 admin, '
            .$allCategories->count().' categories, '
            .$tags->count().' tags, 40 posts');
    }
}
