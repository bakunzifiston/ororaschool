<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'platform_id' => Platform::factory(),
            'academy_id' => null,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('##'),
            'title' => rtrim($title, '.'),
            'summary' => fake()->sentence(18),
            'description' => fake()->sentence(18),
            'instructor' => fake()->name(),
            'status' => 'published',
            'difficulty' => 'Foundation',
            'language' => 'English',
            'category' => 'Field books',
            'modules' => 3,
            'lessons' => 8,
            'duration' => 120,
            'enrolled' => 0,
            'paid' => false,
            'certificate_eligible' => true,
            'enrollment_required' => true,
            'cover' => null,
            'content_updated_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'certificate_eligible' => false,
        ]);
    }
}
