<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Academies;
use App\Support\DemoData\ActivityLogs;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class SuperAdminDashboard
{
    public static function data(bool $empty = false): array
    {
        $platforms = Platforms::all();
        $learners = array_sum(array_column($platforms, 'learners'));
        $staff = count(People::staff());
        $completion = (int) round(array_sum(array_column($platforms, 'completion_rate')) / max(1, count($platforms)));
        $inactive = count(array_filter($platforms, fn (array $platform) => $platform['status'] !== 'active'));

        $glance = array_map(fn (array $platform) => [
            'name' => $platform['name'],
            'status' => $platform['status'],
            'academies' => $platform['academies'],
            'users' => $platform['users'],
            'courses' => $platform['courses'],
        ], $platforms);

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'Orora School', 'route' => 'admin.dashboard'],
                    ['label' => 'Dashboard'],
                ],
                'title' => 'Dashboard',
                'subtitle' => count($platforms).' platforms, '.number_format($learners)
                    .' enrolled learners, and everything waiting on an administrator this week.',
            ],

            'stats' => [
                ['label' => 'Platforms', 'value' => (string) count($platforms), 'trend' => $inactive.' inactive', 'direction' => null, 'note' => null],
                ['label' => 'Users', 'value' => number_format($learners + $staff), 'trend' => '+42', 'direction' => 'up', 'note' => 'learners and staff'],
                ['label' => 'Overall completion', 'value' => $completion.'%', 'trend' => '+3 pts', 'direction' => 'up', 'note' => 'rolling 90 days'],
            ],

            'platforms' => [
                'columns' => [
                    ['key' => 'name', 'label' => 'Platform'],
                    ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
                    ['key' => 'academies', 'label' => 'Academies', 'align' => 'right', 'numeric' => true],
                    ['key' => 'users', 'label' => 'Users', 'align' => 'right', 'numeric' => true],
                    ['key' => 'courses', 'label' => 'Courses', 'align' => 'right', 'numeric' => true],
                ],
                'rows' => $glance,
            ],

            'activity' => $empty ? [] : array_slice(ActivityLogs::all(), 0, 6),

            'academies' => count(Academies::all()),
        ];
    }
}
