<?php

namespace Database\Factories;

use App\Models\Advertorial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Advertorial>
 */
class AdvertorialFactory extends Factory
{
    protected $model = Advertorial::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-1 month', '+1 month');

        return [
            'name' => fake()->company().' Campaign',
            'partner' => fake()->company(),
            'starts_at' => $startsAt,
            'ends_at' => fake()->dateTimeBetween($startsAt, '+6 months'),
        ];
    }
}
