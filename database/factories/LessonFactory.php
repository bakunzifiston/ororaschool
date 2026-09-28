<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id' => Module::factory(),
            'slug' => 'l-'.fake()->unique()->bothify('??##'),
            'title' => fake()->sentence(4),
            'type' => 'video',
            'duration' => 8,
            'body' => fake()->sentence(18),
            'is_preview' => false,
            'quiz_slug' => null,
            'sort_order' => 1,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Lesson $lesson): void {
            if ($lesson->course_id || $lesson->module_id === null) {
                return;
            }

            $module = Module::query()->find($lesson->module_id);

            if ($module) {
                $lesson->course_id = $module->course_id;
            }
        });
    }

    public function preview(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_preview' => true,
        ]);
    }
}
