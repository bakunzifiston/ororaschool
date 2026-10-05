<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\ActivityLogs;
use App\Support\DemoData\Courses;
use App\Support\DemoData\IssuedCertificates;
use App\Support\DemoData\LiveSessions;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class WorkspaceDashboard
{
    public static function for(string $slug): array
    {
        $platform = Platforms::require($slug);
        $courses = Courses::forPlatform($platform['slug']);
        $published = array_values(array_filter($courses, fn ($c) => $c['status'] === 'published'));
        $pipeline = array_values(array_filter($courses, fn ($c) => in_array($c['status'], ['draft', 'pending_review', 'approved'], true)));
        $learners = People::learnersOn($platform['slug']);
        $instructors = People::instructorsOn($platform['slug']);
        $certificates = IssuedCertificates::countFor($platform['slug']);
        $activity = array_values(array_filter(
            ActivityLogs::all(),
            fn (array $row) => $row['platform_slug'] === $platform['slug'],
        ));
        $ranked = $courses;
        usort($ranked, fn (array $left, array $right): int => ($right['enrolled'] ?? 0) <=> ($left['enrolled'] ?? 0));
        $ranked = array_slice($ranked, 0, 6);

        return [
            'platform' => $platform,
            'header' => array_merge(WorkspaceHeader::make(
                $platform,
                'Dashboard',
                $platform['tagline'],
            ), [
                'updated' => 'Last updated '.now()->toFormattedDateString(),
            ]),
            'stats' => [
                [
                    'label' => 'Courses',
                    'value' => (string) count($courses),
                    'trend' => count($pipeline).' in the pipeline',
                    'direction' => null,
                    'note' => count($published).' published',
                    'icon' => 'book',
                    'tone' => 'accent',
                    'tint' => 'green',
                ],
                [
                    'label' => 'Learners',
                    'value' => number_format($platform['learners']),
                    'trend' => '+'.(int) round($platform['learners'] * 0.04),
                    'direction' => 'up',
                    'note' => count($learners).' on this roster',
                    'icon' => 'users',
                    'tone' => 'active',
                    'tint' => 'blue',
                ],
                [
                    'label' => 'Completion rate',
                    'value' => $platform['completion_rate'].'%',
                    'trend' => $platform['completion_rate'] >= 65 ? '+4 pts' : '−2 pts',
                    'direction' => $platform['completion_rate'] >= 65 ? 'up' : 'down',
                    'note' => 'rolling 90 days',
                    'icon' => 'bars',
                    'tone' => $platform['completion_rate'] >= 65 ? 'ok' : 'pending',
                    'tint' => 'amber',
                ],
            ],
            'secondaryStats' => [
                [
                    'label' => 'Instructors',
                    'value' => (string) count($instructors),
                    'trend' => null,
                    'direction' => null,
                    'note' => $platform['region'],
                    'icon' => 'teacher',
                    'tone' => 'approved',
                    'tint' => 'violet',
                    'href' => route('workspace.instructors', ['platform' => $platform['slug']]),
                ],
                [
                    'label' => 'Certificates issued',
                    'value' => (string) $certificates,
                    'trend' => $certificates > 0 ? '+'.min(3, $certificates) : null,
                    'direction' => $certificates > 0 ? 'up' : null,
                    'note' => 'this quarter',
                    'icon' => 'award',
                    'tone' => 'completed',
                    'tint' => 'green',
                    'href' => route('workspace.certificates', ['platform' => $platform['slug']]),
                ],
            ],
            'recent' => array_slice($courses, 0, 3),
            'activity' => array_slice($activity, 0, 6),
            'sessions' => LiveSessions::upcoming($platform['slug'], 4),
            'charts' => [
                'status' => array_merge([
                    'title' => 'Course status',
                    'subtitle' => 'Catalogue on this academy, by publishing state.',
                    'headline' => (string) count($courses),
                ], self::countedSeries($courses, 'status', [
                    'published' => 'Published',
                    'approved' => 'Approved',
                    'pending_review' => 'Pending review',
                    'draft' => 'Draft',
                    'archived' => 'Archived',
                ])),
                'enrolment' => [
                    'title' => 'Enrolment by course',
                    'subtitle' => 'Seats already taken on this academy.',
                    'labels' => array_map(
                        fn (array $course): string => $course['title'],
                        $ranked,
                    ),
                    'values' => array_map(
                        fn (array $course): int => (int) ($course['enrolled'] ?? 0),
                        $ranked,
                    ),
                ],
            ],
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  array<string, string>  $labels
     * @return array{labels: list<string>, values: list<int>}
     */
    private static function countedSeries(array $rows, string $attribute, array $labels): array
    {
        $series = ['labels' => [], 'values' => []];

        foreach ($labels as $key => $label) {
            $count = count(array_filter(
                $rows,
                fn (array $row): bool => ($row[$attribute] ?? '') === $key,
            ));

            if ($count === 0) {
                continue;
            }

            $series['labels'][] = $label;
            $series['values'][] = $count;
        }

        return $series;
    }
}
