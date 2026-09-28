<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Support\DemoData\Curriculum;
use Illuminate\Database\Seeder;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Course::query()->orderBy('id')->get() as $course) {
            $this->seedFor($course);
        }
    }

    public function seedFor(Course $course): void
    {
        foreach (Curriculum::fixtureModulesFor($course->slug) as $moduleRow) {
            $module = Module::query()->updateOrCreate(
                [
                    'course_id' => $course->id,
                    'slug' => $moduleRow['id'],
                ],
                [
                    'title' => $moduleRow['title'],
                    'sort_order' => $moduleRow['order'],
                ],
            );

            foreach ($moduleRow['lessons'] as $index => $lessonRow) {
                Lesson::query()->updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'slug' => $lessonRow['id'],
                    ],
                    [
                        'module_id' => $module->id,
                        'title' => $lessonRow['title'],
                        'type' => $lessonRow['type'],
                        'duration' => $lessonRow['duration'],
                        'body' => Curriculum::body($lessonRow['id']),
                        'is_preview' => (bool) ($lessonRow['is_preview'] ?? false),
                        'quiz_slug' => Curriculum::quizFor($lessonRow['id']),
                        'sort_order' => $index + 1,
                    ],
                );
            }
        }
    }
}
