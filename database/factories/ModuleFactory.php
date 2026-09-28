<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'slug' => 'm-'.fake()->unique()->bothify('??##'),
            'title' => fake()->sentence(3),
            'sort_order' => 1,
        ];
    }
}
