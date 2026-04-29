<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(rand(1, 2), true);

        return [
            'name' => ucwords($name),
            'description' => fake()->sentence(),
            'parent_id' => null,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * Indicate this category is a child of another.
     */
    public function child(Category $parent): static
    {
        return $this->state(fn () => [
            'parent_id' => $parent->id,
        ]);
    }
}
