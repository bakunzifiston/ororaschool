<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $items = [
            [
                'prompt' => fake()->sentence(8),
                'options' => [fake()->words(3, true), fake()->words(3, true), fake()->words(3, true)],
                'correct' => 0,
            ],
        ];

        return [
            'course_id' => Course::factory(),
            'slug' => 'q-'.fake()->unique()->bothify('??##'),
            'title' => fake()->sentence(3),
            'lesson_title' => 'Introduction',
            'question_count' => 1,
            'pass_score' => 70,
            'attempt_limit' => 3,
            'average_score' => 75,
            'items' => $items,
        ];
    }
}
