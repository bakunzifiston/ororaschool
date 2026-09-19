<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Courses;
use App\Support\DemoData\IssuedCertificates;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;
use App\Support\DemoData\Quizzes;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class WorkspaceAnalyticsPage
{
    public static function data(string $platform, bool $empty = false): array
    {
        $current = Platforms::find($platform) ?? Platforms::find('gemura');
        $courses = $empty ? [] : Courses::forPlatform($platform);
        $enrollments = array_sum(array_column($courses, 'enrolled'));
        $labels = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'];

        $series = match ($platform) {
            'gemura' => [
                'enrol' => [210, 244, 268, 301, 280, 218],
                'completion' => [68, 70, 71, 72, 73, 74],
                'quiz' => [12, 18, 24, 31, 22, 9],
            ],
            'buchapro' => [
                'enrol' => [80, 96, 110, 124, 118, 94],
                'completion' => [44, 46, 47, 49, 50, 51],
                'quiz' => [8, 14, 22, 28, 19, 11],
            ],
            'feedgrid' => [
                'enrol' => [120, 140, 155, 170, 162, 130],
                'completion' => [55, 57, 58, 60, 61, 62],
                'quiz' => [10, 16, 21, 27, 18, 8],
            ],
            default => [
                'enrol' => [180, 200, 220, 240, 230, 190],
                'completion' => [60, 62, 64, 65, 67, 68],
                'quiz' => [9, 15, 20, 26, 17, 10],
            ],
        };

        $top = $courses;
        usort($top, fn ($a, $b) => ($b['enrolled'] ?? 0) <=> ($a['enrolled'] ?? 0));

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Analytics',
                $current['name'].' only — '.$current['discipline'].'. Estate-wide numbers live with Super Admin.',
            ),
            'empty' => $empty,
            'stats' => [
                ['label' => 'Enrolments', 'value' => $empty ? '0' : number_format($enrollments), 'trend' => $empty ? null : '+'.(int) round($enrollments * 0.08), 'direction' => $empty ? null : 'up', 'note' => 'this half'],
                ['label' => 'Completion', 'value' => $empty ? '—' : $current['completion_rate'].'%', 'trend' => $empty ? null : ($current['completion_rate'] >= 65 ? '+4 pts' : '−2 pts'), 'direction' => $empty ? null : ($current['completion_rate'] >= 65 ? 'up' : 'down'), 'note' => 'rolling 90 days'],
                ['label' => 'Certificates', 'value' => $empty ? '0' : (string) IssuedCertificates::countFor($platform), 'trend' => null, 'direction' => null, 'note' => 'issued here'],
                ['label' => 'Learners on roster', 'value' => $empty ? '0' : (string) count(People::learnersOn($platform)), 'trend' => null, 'direction' => null, 'note' => 'fixture directory'],
            ],
            'charts' => $empty ? [] : [
                [
                    'title' => 'Enrolments over time',
                    'subtitle' => 'New seats taken on '.$current['name'].' that month',
                    'labels' => $labels,
                    'values' => $series['enrol'],
                ],
                [
                    'title' => 'Completion rate',
                    'subtitle' => 'Share of active enrolments that finished, percent',
                    'labels' => $labels,
                    'values' => $series['completion'],
                    'suffix' => '%',
                ],
                [
                    'title' => 'Quiz score distribution',
                    'subtitle' => 'Share of attempts in each band, last 90 days',
                    'labels' => ['0–49', '50–59', '60–69', '70–79', '80–89', '90–100'],
                    'values' => $series['quiz'],
                    'suffix' => '%',
                ],
            ],
            'topCourses' => [
                'columns' => [
                    ['key' => 'title', 'label' => 'Course'],
                    ['key' => 'instructor', 'label' => 'Instructor', 'type' => 'person'],
                    ['key' => 'enrolled', 'label' => 'Enrolled', 'align' => 'right', 'numeric' => true],
                    ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
                ],
                'rows' => $empty ? [] : array_slice($top, 0, 6),
            ],
            'quizCount' => $empty ? 0 : count(Quizzes::forPlatform($platform)),
        ];
    }
}
