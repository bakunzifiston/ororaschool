<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Academies;
use App\Support\DemoData\ActivityLogs;
use App\Support\DemoData\Courses;
use App\Support\DemoData\IssuedCertificates;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class SuperAdminDashboard
{
    public static function data(bool $empty = false): array
    {
        $platforms = Platforms::visible();
        $courses = Courses::all();
        $learners = array_sum(array_column($platforms, 'learners'));
        $staff = count(People::staff());
        $users = $learners + $staff;
        $activePlatforms = count(array_filter($platforms, fn (array $platform) => $platform['status'] === 'active'));
        $inactivePlatforms = count($platforms) - $activePlatforms;
        $academies = count(Academies::all());
        $completion = (int) round(array_sum(array_column($platforms, 'completion_rate')) / max(1, count($platforms)));
        $pendingCourses = array_values(array_filter($courses, fn (array $course) => $course['status'] === 'pending_review'));
        $pendingUsers = array_values(array_filter(
            People::directory(),
            fn (array $person) => in_array($person['status'], ['pending_review', 'draft'], true),
        ));
        $revokedCertificates = array_values(array_filter(
            IssuedCertificates::all(),
            fn (array $certificate) => $certificate['status'] === 'revoked',
        ));
        $inactiveRows = array_values(array_filter($platforms, fn (array $platform) => $platform['status'] !== 'active'));

        $glance = array_map(fn (array $platform) => [
            'slug' => $platform['slug'],
            'name' => $platform['name'],
            'discipline' => $platform['discipline'],
            'icon' => self::platformIcon($platform['slug']),
            'status' => $platform['status'],
            'academies' => $platform['academies'],
            'users' => $platform['users'],
            'courses' => $platform['courses'],
            'view' => route('workspace.dashboard', ['platform' => $platform['slug']]),
            'edit' => route('admin.platforms.edit', $platform['slug']),
        ], $platforms);

        return [
            'header' => [
                'breadcrumb' => [],
                'title' => 'Dashboard',
                'subtitle' => 'Real-time overview of the FarmSchool learning ecosystem.',
                'updated' => 'Last updated '.now()->toFormattedDateString(),
            ],

            'stats' => [
                [
                    'label' => 'Total Academies',
                    'value' => (string) count($platforms),
                    'trend' => null,
                    'direction' => $inactivePlatforms > 0 ? 'warn' : null,
                    'note' => $activePlatforms.' active · '.$inactivePlatforms.' inactive',
                    'icon' => 'cap',
                    'href' => route('admin.platforms'),
                    'tint' => 'green',
                ],
                [
                    'label' => 'Total Users',
                    'value' => number_format($users),
                    'trend' => '+42',
                    'direction' => 'up',
                    'note' => 'this period',
                    'icon' => 'users',
                    'href' => route('admin.users'),
                    'tint' => 'blue',
                ],
                [
                    'label' => 'Active Learners',
                    'value' => number_format($learners),
                    'trend' => null,
                    'direction' => null,
                    'note' => 'Enrolled across the estate',
                    'icon' => 'user',
                    'href' => route('admin.users'),
                    'tint' => 'violet',
                ],
                [
                    'label' => 'Completion Rate',
                    'value' => $completion.'%',
                    'trend' => '+3 pts',
                    'direction' => 'up',
                    'note' => 'over 90 days',
                    'icon' => 'bars',
                    'href' => route('admin.platforms'),
                    'tint' => 'amber',
                ],
            ],

            'platforms' => [
                'columns' => [
                    ['key' => 'name', 'label' => 'Academy'],
                    ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
                    ['key' => 'academies', 'label' => 'Academies', 'align' => 'right', 'numeric' => true],
                    ['key' => 'users', 'label' => 'Users', 'align' => 'right', 'numeric' => true],
                    ['key' => 'courses', 'label' => 'Courses', 'align' => 'right', 'numeric' => true],
                ],
                'rows' => $glance,
            ],

            'charts' => [
                'catalogue' => array_merge([
                    'title' => 'Catalogue status',
                    'subtitle' => 'Every course on the estate, by publishing state.',
                    'headline' => (string) count($courses),
                    'caption' => 'Total courses',
                ], self::countedSeries($courses, 'status', [
                    'published' => 'Published',
                    'approved' => 'Approved',
                    'pending_review' => 'Pending review',
                    'draft' => 'Draft',
                    'archived' => 'Archived',
                ])),
                'users' => [
                    'title' => 'Users by academy',
                    'subtitle' => 'Including inactive academies that still hold records.',
                    'labels' => array_column($platforms, 'name'),
                    'values' => array_column($platforms, 'users'),
                ],
            ],

            'attention' => self::attentionItems(
                $pendingCourses,
                $inactiveRows,
                $pendingUsers,
                $revokedCertificates,
            ),

            'activity' => $empty ? [] : array_map(fn (array $entry) => array_merge($entry, [
                'icon' => self::activityIcon($entry['action'] ?? ''),
            ]), array_slice(ActivityLogs::all(), 0, 6)),

            'actions' => [
                ['label' => 'Add academy', 'route' => 'admin.platforms.create', 'icon' => 'plus', 'variant' => 'primary'],
                ['label' => 'Create user', 'route' => 'admin.users.create', 'icon' => 'user', 'variant' => 'secondary'],
                ['label' => 'Manage roles', 'route' => 'admin.roles', 'icon' => 'shield', 'variant' => 'secondary'],
                ['label' => 'Manage permissions', 'route' => 'admin.permissions', 'icon' => 'key', 'variant' => 'secondary'],
                ['label' => 'Review activity', 'route' => 'admin.activity', 'icon' => 'history', 'variant' => 'secondary'],
            ],

            'academies' => $academies,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $pendingCourses
     * @param  list<array<string, mixed>>  $inactivePlatforms
     * @param  list<array<string, mixed>>  $pendingUsers
     * @param  list<array<string, mixed>>  $revokedCertificates
     * @return list<array{title: string, explanation: string, href: string}>
     */
    private static function attentionItems(
        array $pendingCourses,
        array $inactivePlatforms,
        array $pendingUsers,
        array $revokedCertificates,
    ): array {
        $items = [];

        if (count($pendingCourses) > 0) {
            $items[] = [
                'title' => count($pendingCourses).' '.str('course')->plural(count($pendingCourses)).' awaiting review',
                'explanation' => 'Submitted catalogues stay off the learner list until a reviewer approves them.',
                'href' => route('admin.content'),
            ];
        }

        if (count($inactivePlatforms) > 0) {
            $names = implode(', ', array_column($inactivePlatforms, 'name'));
            $items[] = [
                'title' => count($inactivePlatforms).' inactive '.str('academy')->plural(count($inactivePlatforms)),
                'explanation' => $names.' are off the workspace switcher. Historical certificates still resolve.',
                'href' => route('admin.platforms'),
            ];
        }

        if (count($pendingUsers) > 0) {
            $items[] = [
                'title' => count($pendingUsers).' '.str('user')->plural(count($pendingUsers)).' waiting to be activated',
                'explanation' => 'Draft and pending-review accounts cannot sign in until an administrator confirms them.',
                'href' => route('admin.users'),
            ];
        }

        if (count($revokedCertificates) > 0) {
            $items[] = [
                'title' => count($revokedCertificates).' revoked '.str('certificate')->plural(count($revokedCertificates)),
                'explanation' => 'Revoked numbers still resolve on the public lookup with a revoked status.',
                'href' => route('admin.activity'),
            ];
        }

        return $items;
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

    private static function platformIcon(string $slug): string
    {
        return match ($slug) {
            'ororafarm' => 'sprout',
            'gemura' => 'droplet',
            'buchapro' => 'tag',
            'feedgrid' => 'layers',
            default => 'building',
        };
    }

    private static function activityIcon(string $action): string
    {
        return match ($action) {
            'course.submitted' => 'file',
            'course.approved' => 'check',
            'course.published' => 'book',
            'course.archived' => 'archive',
            'course.created' => 'plus',
            'enrollment.created' => 'users',
            'certificate.issued' => 'award',
            'role.assigned', 'role.created' => 'shield',
            'platform.created', 'platform.deactivated' => 'layers',
            'user.invited', 'user.archived' => 'user',
            'live_session.scheduled' => 'video',
            'settings.updated' => 'cog',
            default => 'history',
        };
    }
}
