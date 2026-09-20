<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\LearnerProgress;
use App\Support\DemoData\LiveSessions;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LearnerCoursesPage
{
    public static function index(string $status = '', bool $empty = false): array
    {
        $groups = $empty ? [] : LearnerProgress::groupedByPlatform($status);

        return [
            'header' => LearnerHeader::make(
                'My courses',
                'Every course you are on, grouped by platform. Same record — not three logins.',
                [
                    ['label' => 'My courses'],
                ],
            ),
            'filters' => [
                'status' => $status,
                'statuses' => [
                    '' => 'All courses',
                    'active' => 'In progress',
                    'completed' => 'Completed',
                ],
            ],
            'groups' => $groups,
            'available' => $empty ? [] : LearnerProgress::available(),
            'emptyTitle' => $status !== '' ? 'No courses match this filter' : 'No courses on your record yet',
            'emptyMessage' => 'When a field coordinator enrols you, or you start an open course, it lands here with the others.',
        ];
    }

    public static function show(string $slug): ?array
    {
        $syllabus = LearnerProgress::syllabus($slug);

        if (! $syllabus) {
            return null;
        }

        $course = $syllabus['course'];
        $cta = $syllabus['enrolled']
            ? 'continue'
            : ($syllabus['requires_enrolment'] ? 'enroll' : 'start');

        return [
            'header' => LearnerHeader::make(
                $course['title'],
                ($syllabus['platform']['name'] ?? '').' · '.$course['instructor'],
                [
                    ['label' => 'My courses', 'route' => 'learner.courses'],
                    ['label' => $course['title']],
                ],
            ),
            'syllabus' => $syllabus,
            'cta' => $cta,
            'session' => self::upcomingSession($slug),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function upcomingSession(string $slug): ?array
    {
        foreach (LiveSessions::all() as $session) {
            if ($session['course_slug'] === $slug && in_array($session['status'], ['scheduled', 'live'], true)) {
                return $session;
            }
        }

        return null;
    }
}
