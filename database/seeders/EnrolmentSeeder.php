<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrolment;
use App\Models\User;
use App\Support\DemoData\Courses;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class EnrolmentSeeder extends Seeder
{
    public function run(): void
    {
        $placide = User::query()->where('email', 'p.bizimana@umuhinzi.rw')->first();

        if (! $placide) {
            return;
        }

        $this->seedFor($placide);
    }

    public function seedFor(User $user): void
    {
        foreach (Courses::enrolments() as $index => $row) {
            $course = Course::query()->where('slug', $row['course'])->first();

            if (! $course) {
                continue;
            }

            Enrolment::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ],
                [
                    'status' => $row['status'],
                    'progress' => $row['progress'],
                    'lessons_done' => $row['lessons_done'],
                    'enrolled_at' => now()->subDays($index),
                    'completed_at' => $row['status'] === 'completed' ? now()->subDays($index + 5) : null,
                    'due_on' => $row['due'] === 'Completed' ? null : Carbon::parse($row['due']),
                ],
            );
        }
    }
}
