<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(rand(6, 12));
        $status = fake()->randomElement(['draft', 'published', 'published', 'published', 'scheduled', 'archived']);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'advertorial_id' => null,
            'type' => 'article',
            'title' => $title,
            'excerpt' => fake()->paragraph(2),
            'body' => $this->generateBody(),
            'featured_image' => null,
            'breaking_news' => false,
            'youtube_url' => null,
            'gallery_images' => null,
            'infographic_image' => null,
            'status' => $status,
            'published_at' => $status === 'published'
                ? fake()->dateTimeBetween('-6 months', 'now')
                : ($status === 'scheduled' ? fake()->dateTimeBetween('now', '+1 month') : null),
            'meta_title' => fake()->optional(0.7)->sentence(),
            'meta_description' => fake()->optional(0.7)->text(160),
        ];
    }

    /**
     * Generate realistic blog post body content.
     */
    private function generateBody(): string
    {
        $paragraphs = fake()->paragraphs(rand(4, 8));
        $body = '';

        foreach ($paragraphs as $i => $paragraph) {
            if ($i > 0 && $i % 2 === 0) {
                $body .= '<h2>'.fake()->sentence(rand(3, 6)).'</h2>'."\n\n";
            }
            $body .= '<p>'.$paragraph.'</p>'."\n\n";
        }

        return $body;
    }

    /**
     * Set the post as published.
     */
    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Set the post as a draft.
     */
    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Set the post as scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => 'scheduled',
            'published_at' => fake()->dateTimeBetween('now', '+1 month'),
        ]);
    }
}
