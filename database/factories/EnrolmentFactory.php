<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrolment>
 */
class EnrolmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->learner(),
            'course_id' => Course::factory(),
            'status' => 'active',
            'progress' => 0,
            'lessons_done' => 0,
            'enrolled_at' => now(),
            'completed_at' => null,
            'due_on' => now()->addWeeks(2),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'progress' => 100,
            'completed_at' => now(),
        ]);
    }
}
