<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'OS-TST-'.fake()->unique()->numerify('####'),
            'course_id' => Course::factory(),
            'learner_name' => fake()->name(),
            'learner_id' => null,
            'issued_at' => now()->subMonth(),
            'status' => 'valid',
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Certificate $certificate): void {
            if ($certificate->platform_id || $certificate->course_id === null) {
                return;
            }

            $course = Course::query()->find($certificate->course_id);

            if ($course) {
                $certificate->platform_id = $course->platform_id;
            }
        });
    }

    public function revoked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'revoked',
        ]);
    }
}
