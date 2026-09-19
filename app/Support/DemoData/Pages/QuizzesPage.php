<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Paging;
use App\Support\DemoData\Platforms;
use App\Support\DemoData\Quizzes;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class QuizzesPage
{
    public static function index(string $platform, bool $empty = false, int $page = 1, string $course = ''): array
    {
        $current = Platforms::find($platform) ?? Platforms::find('gemura');
        $rows = $empty ? [] : Quizzes::forPlatform($platform);

        if ($course !== '') {
            $rows = array_values(array_filter($rows, fn (array $row) => $row['course_slug'] === $course));
        }

        $courses = ['' => 'Any course'] + array_column(Quizzes::forPlatform($platform), 'course', 'course_slug');
        $paged = Paging::paginate(
            $rows,
            $page,
            10,
            '/workspace/'.$platform.'/quizzes',
            array_filter(['empty' => $empty ? 1 : null, 'course' => $course !== '' ? $course : null]),
        );

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Quizzes',
                'Assessments attached to a '.$current['name'].' lesson. Passing score and attempt limit live on the quiz.',
            ),
            'filters' => ['course' => $course, 'courses' => $courses],
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'No quizzes on '.$current['name'].' yet',
            'emptyMessage' => 'Attach a quiz to a lesson. Questions and the correct-answer mark are authored on the builder.',
        ];
    }

    public static function show(string $platform, string $slug): ?array
    {
        $current = Platforms::find($platform);
        $quiz = Quizzes::findForPlatform($slug, $platform);

        if (! $quiz) {
            return null;
        }

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                $quiz['title'],
                $quiz['course'].' · '.$quiz['lesson'],
                [
                    ['label' => 'Quizzes', 'route' => 'workspace.quizzes', 'params' => ['platform' => $platform]],
                    ['label' => $quiz['title']],
                ],
            ),
            'quiz' => $quiz,
        ];
    }
}
