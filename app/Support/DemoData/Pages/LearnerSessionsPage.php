<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\LearnerProgress;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LearnerSessionsPage
{
    public static function index(bool $empty = false): array
    {
        $rows = $empty ? [] : LearnerProgress::sessions();
        $upcoming = array_values(array_filter($rows, fn (array $row) => in_array($row['status'], ['scheduled', 'live'], true)));
        $past = array_values(array_filter($rows, fn (array $row) => in_array($row['status'], ['completed', 'cancelled'], true)));

        $label = function (array $row): array {
            $platform = Platforms::find($row['platform']);
            $row['platform_name'] = $platform['name'] ?? $row['platform'];
            $row['can_join'] = ($row['status'] ?? '') === 'live';

            $parts = array_map('trim', explode(',', (string) $row['starts'], 2));
            $row['date'] = $parts[0] ?? $row['starts'];
            $row['time'] = $parts[1] ?? '';

            return $row;
        };

        return [
            'header' => LearnerHeader::make(
                'Live sessions',
                'Clinics on courses you are enrolled in, whichever academy they sit on.',
                [
                    ['label' => 'Live sessions'],
                ],
            ),
            'upcoming' => array_map($label, $upcoming),
            'past' => array_map($label, $past),
            'emptyTitle' => 'No clinics on your courses',
            'emptyMessage' => 'When a live session is scheduled against a course you are on, it appears here with a join link.',
        ];
    }
}
