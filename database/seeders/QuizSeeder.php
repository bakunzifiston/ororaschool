<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Quiz;
use App\Support\DemoData\Quizzes;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Quizzes::fixtureRecords() as $row) {
            $course = Course::query()->where('slug', $row['course_slug'])->first();

            if (! $course) {
                continue;
            }

            Quiz::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'course_id' => $course->id,
                    'title' => $row['title'],
                    'lesson_title' => $row['lesson'],
                    'question_count' => $row['questions'],
                    'pass_score' => $row['pass'],
                    'attempt_limit' => $row['attempts'],
                    'average_score' => $row['avg'],
                    'items' => $row['items'],
                ],
            );
        }
    }
}
