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
        $platform = Platforms::find($slug) ?? Platforms::find('gemura');
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

        return [
            'platform' => $platform,
            'header' => WorkspaceHeader::make(
                $platform,
                'Dashboard',
                $platform['tagline'],
            ),
            'stats' => [
                ['label' => 'Courses', 'value' => (string) count($courses), 'trend' => count($pipeline).' in the pipeline', 'direction' => null, 'note' => count($published).' published'],
                ['label' => 'Learners', 'value' => number_format($platform['learners']), 'trend' => '+'.(int) round($platform['learners'] * 0.04), 'direction' => 'up', 'note' => count($learners).' on this roster'],
                ['label' => 'Completion rate', 'value' => $platform['completion_rate'].'%', 'trend' => $platform['completion_rate'] >= 65 ? '+4 pts' : '−2 pts', 'direction' => $platform['completion_rate'] >= 65 ? 'up' : 'down', 'note' => 'rolling 90 days'],
            ],
            'secondaryStats' => [
                ['label' => 'Instructors', 'value' => (string) count($instructors), 'trend' => null, 'direction' => null, 'note' => $platform['region'], 'href' => null],
                ['label' => 'Certificates issued', 'value' => (string) $certificates, 'trend' => $certificates > 0 ? '+'.min(3, $certificates) : null, 'direction' => $certificates > 0 ? 'up' : null, 'note' => 'this quarter', 'href' => null],
            ],
            'recent' => array_slice($courses, 0, 3),
            'activity' => array_slice($activity, 0, 6),
            'sessions' => LiveSessions::upcoming($platform['slug'], 4),
        ];
    }
}
