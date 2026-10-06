<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\DemoData;
use App\Support\DemoData\IssuedCertificates;
use App\Support\DemoData\LearnerProgress;
use App\Support\DemoData\LearningPaths;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LearnerDashboard
{
    public static function data(bool $empty = false): array
    {
        $authenticated = auth()->user();
        $user = DemoData::currentUser('learner');

        if ($authenticated) {
            $user['name'] = $authenticated->name;
            $user['email'] = $authenticated->email;
            $user['district'] = $authenticated->district;
        }

        $inProgress = $empty ? [] : LearnerProgress::inProgress();
        $completed = $empty ? [] : LearnerProgress::completed();
        $continue = $empty ? null : LearnerProgress::continueLesson();
        $certificates = ($empty || ($inProgress === [] && $completed === []))
            ? []
            : array_map(function (array $row): array {
                $platform = Platforms::find($row['platform']);
                $row['platform_name'] = $platform['name'] ?? $row['platform'];

                return $row;
            }, IssuedCertificates::forLearner(LearnerProgress::LEARNER_ID));
        $platforms = array_values(array_unique(array_column(array_merge($inProgress, $completed), 'platform_name')));
        $mixLabels = [];
        $mixValues = [];

        if (count($inProgress) > 0) {
            $mixLabels[] = 'In progress';
            $mixValues[] = count($inProgress);
        }

        if (count($completed) > 0) {
            $mixLabels[] = 'Completed';
            $mixValues[] = count($completed);
        }

        $hasRecord = $inProgress !== [] || $completed !== [];
        $upcoming = [];
        $available = [];

        if (! $empty && $hasRecord) {
            foreach (LearnerProgress::sessions() as $session) {
                if (! in_array($session['status'], ['scheduled', 'live'], true)) {
                    continue;
                }

                $platform = Platforms::find($session['platform']);
                $session['platform_name'] = $platform['name'] ?? $session['platform'];
                $session['can_join'] = $session['status'] === 'live';
                $upcoming[] = $session;
            }

            $available = array_values(array_filter(
                LearnerProgress::available(),
                fn (array $item): bool => in_array($item['platform_name'], $platforms, true),
            ));
        }

        return [
            'user' => $user,
            'header' => LearnerHeader::make(
                'Welcome back, '.$user['name'],
                $platforms === []
                    ? 'One learning record across every academy you train on. Nothing here is a separate account.'
                    : 'One learning record — '.implode(', ', $platforms).' together. Nothing here is a separate account.',
            ),
            'continue' => $continue,
            'inProgress' => $inProgress,
            'completed' => $completed,
            'paths' => ($empty || ! $hasRecord) ? [] : LearningPaths::all(),
            'upcoming' => array_slice($upcoming, 0, 3),
            'available' => array_slice($available, 0, 4),
            'certificates' => array_slice($certificates, 0, 3),
            'certificatesCount' => count($certificates),
            'stats' => [
                [
                    'label' => 'In progress',
                    'value' => (string) count($inProgress),
                    'trend' => null,
                    'direction' => null,
                    'note' => 'Open enrolments',
                    'icon' => 'book',
                    'tint' => 'green',
                ],
                [
                    'label' => 'Completed',
                    'value' => (string) count($completed),
                    'trend' => null,
                    'direction' => null,
                    'note' => 'Finished on this record',
                    'icon' => 'check',
                    'tint' => 'blue',
                ],
                [
                    'label' => 'Certificates',
                    'value' => (string) count($certificates),
                    'trend' => null,
                    'direction' => null,
                    'note' => 'Issued to you',
                    'icon' => 'award',
                    'tint' => 'amber',
                ],
            ],
            'charts' => [
                'mix' => [
                    'title' => 'Learning mix',
                    'subtitle' => 'Courses on this record, by progress.',
                    'headline' => (string) (count($inProgress) + count($completed)),
                    'labels' => $mixLabels,
                    'values' => $mixValues,
                ],
                'progress' => [
                    'title' => 'Progress by course',
                    'subtitle' => 'Share of lessons finished on each open course.',
                    'labels' => array_map(
                        fn (array $enrolment): string => $enrolment['course_data']['title'] ?? $enrolment['course'],
                        $inProgress,
                    ),
                    'values' => array_map(
                        fn (array $enrolment): int => (int) ($enrolment['progress'] ?? 0),
                        $inProgress,
                    ),
                    'suffix' => '%',
                    'max' => 100,
                ],
            ],
        ];
    }
}
