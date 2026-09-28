<?php

namespace Database\Factories;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Platform>
 */
class PlatformFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('##'),
            'name' => $name,
            'discipline' => 'Farm management',
            'tagline' => fake()->sentence(8),
            'description' => fake()->sentence(16),
            'steward' => fake()->name(),
            'region' => 'Nyagatare, Eastern Province',
            'learner_count' => fake()->numberBetween(10, 500),
            'instructor_count' => fake()->numberBetween(1, 8),
            'status' => 'active',
            'sort_order' => 10,
            'joined_at' => now()->subYear(),
            'completion_rate' => fake()->numberBetween(40, 80),
            'cover' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
