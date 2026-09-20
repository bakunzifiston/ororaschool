<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\LearnerProgress;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LearnerLessonPage
{
    public static function show(string $courseSlug, string $lessonId): ?array
    {
        $page = LearnerProgress::lessonPage($courseSlug, $lessonId);

        if (! $page) {
            return null;
        }

        $lesson = $page['lesson'];
        $course = $page['syllabus']['course'];

        return array_merge($page, [
            'header' => LearnerHeader::make(
                $lesson['title'],
                $course['title'].' · '.$lesson['module'],
                [
                    ['label' => 'My courses', 'route' => 'learner.courses'],
                    ['label' => $course['title'], 'route' => 'learner.courses.show', 'params' => ['course' => $courseSlug]],
                    ['label' => $lesson['title']],
                ],
            ),
        ]);
    }
}
