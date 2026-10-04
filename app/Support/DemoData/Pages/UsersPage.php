<?php

namespace App\Support\DemoData\Pages;

use App\Models\User;
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
        $rows = $empty ? [] : array_merge(self::accountRows($q, $status, $platform), self::filtered($q, $status, $platform));
        $rows = array_map(function (array $row): array {
            if (($row['persisted'] ?? false) === true) {
                return $row;
            }

            return [
                ...$row,
                'persisted' => false,
                'view' => route('admin.users.show', $row['id']),
                'edit' => route('admin.users.edit', $row['id']),
                'delete' => null,
                'destroy' => null,
            ];
        }, $rows);
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
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Users'],
                ],
                'title' => 'Users',
                'subtitle' => 'Staff and learners across every academy. A person can hold a different role on each one.',
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
                'platforms' => ['' => 'Any academy'] + array_column(Platforms::all(), 'name', 'slug'),
            ],
            'columns' => [
                ['key' => 'name', 'label' => 'Name', 'type' => 'person'],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'district', 'label' => 'District'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
                ['key' => 'last_seen', 'label' => 'Last seen', 'align' => 'right'],
                ['key' => 'actions', 'label' => '', 'align' => 'right'],
            ],
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => $q !== '' || $status !== '' || $platform !== ''
                ? 'No one matches these filters'
                : 'No users on the estate yet',
            'emptyMessage' => $q !== '' || $status !== '' || $platform !== ''
                ? 'Clear the search or pick a different academy — the directory itself has not gone anywhere.'
                : 'Create an academy owner or a field coordinator and they will land here with their first assignment.',
        ];
    }

    public static function form(?int $id = null): array
    {
        $user = $id ? People::find($id) : null;
        $isEdit = (bool) $user;

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Users', 'route' => 'admin.users'],
                    ['label' => $isEdit ? 'Edit '.$user['name'] : 'Create a user'],
                ],
                'title' => $isEdit ? 'Edit '.$user['name'] : 'Create a user',
                'subtitle' => $isEdit
                    ? 'Account details only. Academy assignments live on the person’s page.'
                    : 'Create their sign-in and choose which academy dashboards they can open.',
            ],
            'user' => $user ?? [
                'id' => null,
                'name' => '',
                'email' => '',
                'district' => '',
                'status' => 'active',
                'role' => 'platform-staff',
                'platforms' => [],
            ],
            'isEdit' => $isEdit,
            'persisted' => false,
            'districts' => People::districts(),
            'statuses' => ['draft' => 'Draft', 'active' => 'Active', 'pending_review' => 'Pending review', 'archived' => 'Archived'],
            'roles' => [
                'platform-staff' => 'Academy staff — workspace dashboards',
                'learner' => 'Learner — learning record',
                'super-admin' => 'Super Admin — whole estate',
            ],
            'platforms' => array_map(fn (array $platform): array => [
                'slug' => $platform['slug'],
                'name' => $platform['name'],
                'discipline' => $platform['discipline'],
                'status' => $platform['status'],
            ], Platforms::visible()),
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
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Users', 'route' => 'admin.users'],
                    ['label' => $user['name']],
                ],
                'title' => $user['name'],
                'subtitle' => $user['email'].' · '.$user['district'],
            ],
            'user' => $user,
            'assignments' => $assignments,
            'persisted' => false,
            'canDelete' => false,
            'platforms' => array_column(Platforms::all(), 'name', 'slug'),
            'roles' => array_column(Roles::visible(), 'label', 'key'),
        ];
    }

    public static function showAccount(User $user): array
    {
        $assignments = $user->platforms()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($platform) use ($user): array {
                return [
                    'platform_slug' => $platform->slug,
                    'platform' => $platform->name,
                    'discipline' => $platform->discipline,
                    'role' => $user->role->value,
                    'role_label' => Roles::find($user->role->value)['label'],
                ];
            })
            ->all();

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Users', 'route' => 'admin.users'],
                    ['label' => $user->name],
                ],
                'title' => $user->name,
                'subtitle' => $user->email.' · '.$user->district,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'district' => $user->district,
                'status' => $user->status,
                'last_seen' => $user->updated_at?->diffForHumans() ?? 'Never',
            ],
            'assignments' => $assignments,
            'persisted' => true,
            'canDelete' => auth()->id() !== $user->id,
            'platforms' => array_column(Platforms::all(), 'name', 'slug'),
            'roles' => array_column(Roles::visible(), 'label', 'key'),
        ];
    }

    public static function formAccount(User $user): array
    {
        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Users', 'route' => 'admin.users'],
                    ['label' => 'Edit '.$user->name],
                ],
                'title' => 'Edit '.$user->name,
                'subtitle' => 'Update their sign-in and which academy dashboards they can open.',
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'district' => $user->district,
                'status' => $user->status,
                'role' => $user->role->value,
                'platforms' => $user->platforms()->orderBy('slug')->pluck('slug')->all(),
            ],
            'isEdit' => true,
            'persisted' => true,
            'districts' => People::districts(),
            'statuses' => ['draft' => 'Draft', 'active' => 'Active', 'pending_review' => 'Pending review', 'archived' => 'Archived'],
            'roles' => [
                'platform-staff' => 'Academy staff — workspace dashboards',
                'learner' => 'Learner — learning record',
                'super-admin' => 'Super Admin — whole estate',
            ],
            'platforms' => array_map(fn (array $platform): array => [
                'slug' => $platform['slug'],
                'name' => $platform['name'],
                'discipline' => $platform['discipline'],
                'status' => $platform['status'],
            ], Platforms::visible()),
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

    /**
     * @return list<array<string, mixed>>
     */
    private static function accountRows(string $q, string $status, string $platform): array
    {
        $fixtureEmails = array_column(People::directory(), 'email');

        $query = User::query()->with('platforms')->orderBy('name')->orderBy('id');

        if ($q !== '') {
            $query->where(function ($builder) use ($q): void {
                $builder->where('name', 'like', '%'.$q.'%')
                    ->orWhere('email', 'like', '%'.$q.'%');
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($platform !== '') {
            $query->whereHas('platforms', fn ($builder) => $builder->where('platforms.slug', $platform));
        }

        return $query->get()
            ->reject(fn (User $user): bool => in_array($user->email, $fixtureEmails, true))
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'district' => $user->district,
                'status' => $user->status,
                'last_seen' => $user->updated_at?->diffForHumans() ?? 'Never',
                'persisted' => true,
                'view' => route('admin.accounts.show', $user),
                'edit' => route('admin.accounts.edit', $user),
                'delete' => auth()->id() === $user->id ? null : 'delete-user-'.$user->id,
                'destroy' => route('admin.accounts.destroy', $user),
            ])
            ->values()
            ->all();
    }
}
