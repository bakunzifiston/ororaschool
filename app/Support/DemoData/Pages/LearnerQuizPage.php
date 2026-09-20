<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\LearnerProgress;
use App\Support\DemoData\Quizzes;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Quiz-taking is a single page (every question at once), then a mock result.
 */
class LearnerQuizPage
{
    public static function show(string $slug, bool $submitted = false): ?array
    {
        $quiz = Quizzes::find($slug);

        if (! $quiz) {
            return null;
        }

        $enrolment = LearnerProgress::findEnrolment($quiz['course_slug']);

        $result = null;

        if ($submitted) {
            $score = 83;
            $result = [
                'score' => $score,
                'pass' => $score >= (int) $quiz['pass'],
                'passed' => $score >= (int) $quiz['pass'],
                'label' => $score >= (int) $quiz['pass'] ? 'Passed' : 'Not yet',
            ];
        }

        return [
            'header' => LearnerHeader::make(
                $quiz['title'],
                $quiz['course'].' · '.$quiz['lesson'],
                [
                    ['label' => 'My courses', 'route' => 'learner.courses'],
                    ['label' => $quiz['course'], 'route' => 'learner.courses.show', 'params' => ['course' => $quiz['course_slug']]],
                    ['label' => $quiz['title']],
                ],
            ),
            'quiz' => $quiz,
            'enrolment' => $enrolment,
            'result' => $result,
            'layout' => 'single-page',
        ];
    }
}
