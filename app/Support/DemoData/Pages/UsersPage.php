<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Paging;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;
use App\Support\DemoData\Roles;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class UsersPage
{
    public static function index(bool $empty = false, int $page = 1, string $q = '', string $status = '', string $platform = ''): array
    {
        $rows = $empty ? [] : self::filtered($q, $status, $platform);
        $query = array_filter([
            'empty' => $empty ? 1 : null,
            'q' => $q !== '' ? $q : null,
            'status' => $status !== '' ? $status : null,
            'platform' => $platform !== '' ? $platform : null,
        ]);
        $paged = Paging::paginate($rows, $page, 10, '/admin/users', $query);

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'Orora School', 'route' => 'admin.dashboard'],
                    ['label' => 'Users'],
                ],
                'title' => 'Users',
                'subtitle' => 'Staff and learners across every platform. A person can hold a different role on each one.',
            ],
            'filters' => [
                'q' => $q,
                'status' => $status,
                'platform' => $platform,
                'statuses' => [
                    '' => 'Any status',
                    'active' => 'Active',
                    'pending_review' => 'Pending review',
                    'draft' => 'Draft',
                    'archived' => 'Archived',
                    'completed' => 'Completed',
                ],
                'platforms' => ['' => 'Any platform'] + array_column(Platforms::all(), 'name', 'slug'),
            ],
            'columns' => [
                ['key' => 'name', 'label' => 'Name', 'type' => 'person'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'district', 'label' => 'District'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
                ['key' => 'last_seen', 'label' => 'Last seen', 'align' => 'right'],
            ],
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => $q !== '' || $status !== '' || $platform !== ''
                ? 'No one matches these filters'
                : 'No users on the estate yet',
            'emptyMessage' => $q !== '' || $status !== '' || $platform !== ''
                ? 'Clear the search or pick a different platform — the directory itself has not gone anywhere.'
                : 'Invite a platform owner or a field coordinator and they will land here with their first assignment.',
        ];
    }

    public static function form(?int $id = null): array
    {
        $user = $id ? People::find($id) : null;
        $isEdit = (bool) $user;

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'Orora School', 'route' => 'admin.dashboard'],
                    ['label' => 'Users', 'route' => 'admin.users'],
                    ['label' => $isEdit ? $user['name'] : 'New user'],
                ],
                'title' => $isEdit ? 'Edit '.$user['name'] : 'Invite a user',
                'subtitle' => $isEdit
                    ? 'Account details only. Platform assignments live on the person’s page.'
                    : 'They receive an invite at this address. Assign a platform afterwards.',
            ],
            'user' => $user ?? [
                'id' => null,
                'name' => '',
                'email' => '',
                'district' => '',
                'status' => 'draft',
            ],
            'isEdit' => $isEdit,
            'districts' => People::districts(),
            'statuses' => ['draft' => 'Draft', 'active' => 'Active', 'pending_review' => 'Pending review', 'archived' => 'Archived'],
        ];
    }

    public static function show(int $id): ?array
    {
        $user = People::find($id);

        if (! $user) {
            return null;
        }

        $assignments = [];
        foreach ($user['roles'] as $slug => $roleKey) {
            $platform = Platforms::find($slug);
            $assignments[] = [
                'platform_slug' => $slug,
                'platform' => $platform['name'] ?? $slug,
                'discipline' => $platform['discipline'] ?? '',
                'role' => $roleKey,
                'role_label' => Roles::find($roleKey)['label'],
            ];
        }

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'Orora School', 'route' => 'admin.dashboard'],
                    ['label' => 'Users', 'route' => 'admin.users'],
                    ['label' => $user['name']],
                ],
                'title' => $user['name'],
                'subtitle' => $user['email'].' · '.$user['district'],
            ],
            'user' => $user,
            'assignments' => $assignments,
            'platforms' => array_column(Platforms::all(), 'name', 'slug'),
            'roles' => array_column(Roles::all(), 'label', 'key'),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function filtered(string $q, string $status, string $platform): array
    {
        $rows = People::directory();

        if ($q !== '') {
            $needle = mb_strtolower($q);
            $rows = array_values(array_filter(
                $rows,
                fn (array $person) => str_contains(mb_strtolower($person['name'].' '.$person['email']), $needle),
            ));
        }

        if ($status !== '') {
            $rows = array_values(array_filter($rows, fn (array $person) => $person['status'] === $status));
        }

        if ($platform !== '') {
            $rows = array_values(array_filter(
                $rows,
                fn (array $person) => array_key_exists($platform, $person['roles']),
            ));
        }

        return $rows;
    }
}
