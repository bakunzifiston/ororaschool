<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\ActivityLogs;
use App\Support\DemoData\Paging;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class ActivityLogsPage
{
    public static function index(bool $empty = false, int $page = 1, string $user = '', string $platform = '', string $action = ''): array
    {
        $rows = $empty ? [] : self::filtered($user, $platform, $action);
        $query = array_filter([
            'empty' => $empty ? 1 : null,
            'user' => $user !== '' ? $user : null,
            'platform' => $platform !== '' ? $platform : null,
            'action' => $action !== '' ? $action : null,
        ]);
        $paged = Paging::paginate($rows, $page, 10, '/admin/activity', $query);

        $actors = array_values(array_unique(array_column(ActivityLogs::all(), 'user')));
        sort($actors);

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Activity logs'],
                ],
                'title' => 'Activity logs',
                'subtitle' => 'Who did what, on which academy, and when. Nothing here is deleted — deactivating an academy still leaves its trail.',
            ],
            'filters' => [
                'user' => $user,
                'platform' => $platform,
                'action' => $action,
                'users' => ['' => 'Any person'] + array_combine($actors, $actors),
                'platforms' => ['' => 'Any academy'] + array_column(Platforms::all(), 'name', 'slug'),
                'actions' => [
                    '' => 'Any action',
                    'course.published' => 'Course published',
                    'course.created' => 'Course created',
                    'course.submitted' => 'Course submitted',
                    'course.approved' => 'Course approved',
                    'course.archived' => 'Course archived',
                    'role.assigned' => 'Role assigned',
                    'role.created' => 'Role created',
                    'platform.created' => 'Academy created',
                    'platform.deactivated' => 'Academy deactivated',
                    'enrollment.created' => 'Learners enrolled',
                    'certificate.issued' => 'Certificate issued',
                    'live_session.scheduled' => 'Live session scheduled',
                    'user.invited' => 'User invited',
                    'user.archived' => 'User archived',
                    'settings.updated' => 'Settings updated',
                ],
            ],
            'columns' => [
                ['key' => 'user', 'label' => 'User', 'type' => 'person'],
                ['key' => 'platform', 'label' => 'Academy'],
                ['key' => 'action_label', 'label' => 'Action'],
                ['key' => 'target', 'label' => 'Description'],
                ['key' => 'date', 'label' => 'Date', 'align' => 'right'],
            ],
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => $user !== '' || $platform !== '' || $action !== ''
                ? 'No log entries match these filters'
                : 'The trail is empty',
            'emptyMessage' => $user !== '' || $platform !== '' || $action !== ''
                ? 'Widen the filters. Publishing a course, assigning a role or creating an academy all write a row here.'
                : 'The first published course, assigned role or created academy will open the trail.',
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function filtered(string $user, string $platform, string $action): array
    {
        $rows = ActivityLogs::all();

        if ($user !== '') {
            $rows = array_values(array_filter($rows, fn (array $row) => $row['user'] === $user));
        }

        if ($platform !== '') {
            $rows = array_values(array_filter($rows, fn (array $row) => $row['platform_slug'] === $platform));
        }

        if ($action !== '') {
            $rows = array_values(array_filter($rows, fn (array $row) => $row['action'] === $action));
        }

        return $rows;
    }
}
