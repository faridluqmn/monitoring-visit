<?php

namespace Database\Factories;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visit>
 */
class VisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'visitor_name' => fake()->name(),
            'latitude' => fake()->latitude(-6.35, -6.10),
            'longitude' => fake()->longitude(106.70, 106.95),
            'photo_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80',
            'created_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ];
    }
}
