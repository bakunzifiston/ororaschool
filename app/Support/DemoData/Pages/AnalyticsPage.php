<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Courses;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Series are month-by-month counts, not live queries. Charts render from these
 * arrays the same way a table renders from rows.
 */
class AnalyticsPage
{
    public static function data(): array
    {
        $platforms = Platforms::all();
        $courses = Courses::all();
        $enrollments = array_sum(array_column($courses, 'enrolled'));
        $labels = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'];

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'Orora School', 'route' => 'admin.dashboard'],
                    ['label' => 'Analytics'],
                ],
                'title' => 'Analytics',
                'subtitle' => 'Six months across every platform. Open a workspace for the same numbers scoped to one tenant.',
            ],
            'stats' => [
                ['label' => 'Users now', 'value' => '11,842', 'trend' => '+364', 'direction' => 'up', 'note' => 'since April'],
                ['label' => 'Enrolments', 'value' => number_format($enrollments), 'trend' => '+1,204', 'direction' => 'up', 'note' => 'this half'],
                ['label' => 'Completion', 'value' => '64%', 'trend' => '+5 pts', 'direction' => 'up', 'note' => 'rolling 90 days'],
                ['label' => 'Certificates', 'value' => '1,847', 'trend' => '−62', 'direction' => 'down', 'note' => 'vs August'],
            ],
            'charts' => [
                [
                    'title' => 'Users over time',
                    'subtitle' => 'Staff and learners with an account, month end',
                    'labels' => $labels,
                    'values' => [8420, 9014, 9688, 10340, 11020, 11842],
                ],
                [
                    'title' => 'Enrolments over time',
                    'subtitle' => 'New course seats taken that month',
                    'labels' => $labels,
                    'values' => [410, 488, 512, 640, 701, 486],
                ],
                [
                    'title' => 'Completion rate trend',
                    'subtitle' => 'Share of active enrolments that finished, percent',
                    'labels' => $labels,
                    'values' => [52, 55, 57, 59, 61, 64],
                    'suffix' => '%',
                ],
                [
                    'title' => 'Certificates issued',
                    'subtitle' => 'Numbers minted that month',
                    'labels' => $labels,
                    'values' => [210, 244, 268, 301, 280, 218],
                ],
            ],
            'comparison' => [
                'columns' => [
                    ['key' => 'name', 'label' => 'Platform'],
                    ['key' => 'learners', 'label' => 'Learners', 'align' => 'right', 'numeric' => true],
                    ['key' => 'courses', 'label' => 'Courses', 'align' => 'right', 'numeric' => true],
                    ['key' => 'completion_rate', 'label' => 'Completion', 'align' => 'right', 'numeric' => true],
                    ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
                ],
                'rows' => array_map(fn (array $p) => [
                    'name' => $p['name'],
                    'learners' => $p['learners'],
                    'courses' => $p['courses'],
                    'completion_rate' => $p['completion_rate'].'%',
                    'status' => $p['status'],
                ], $platforms),
            ],
        ];
    }
}
