<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Paging;
use App\Support\DemoData\Permissions;
use App\Support\DemoData\Roles;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class RolesPage
{
    public static function index(bool $empty = false, int $page = 1): array
    {
        $roles = $empty ? [] : Roles::visible();
        $paged = Paging::paginate(
            $roles,
            $page,
            5,
            '/admin/roles',
            array_filter(['empty' => $empty ? 1 : null]),
        );

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Roles'],
                ],
                'title' => 'Roles',
                'subtitle' => 'Who can do what on FarmSchool. System roles stay locked; custom roles can be edited.',
            ],
            'columns' => [
                ['key' => 'label', 'label' => 'Role'],
                ['key' => 'type', 'label' => 'Type'],
                ['key' => 'scope', 'label' => 'Scope'],
                ['key' => 'holders', 'label' => 'Holders', 'align' => 'right', 'numeric' => true],
                ['key' => 'actions', 'label' => ''],
            ],
            'roles' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'No roles defined',
            'emptyMessage' => 'The five system roles are created with the estate. Custom roles — Reviewer, Field Coordinator — are added here.',
        ];
    }

    public static function edit(string $key): ?array
    {
        return self::detail($key, grantedOnly: false);
    }

    public static function show(string $key): ?array
    {
        return self::detail($key, grantedOnly: true);
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function detail(string $key, bool $grantedOnly): ?array
    {
        $role = Roles::find($key);
        $known = array_column(Roles::visible(), 'key');

        if (! in_array($role['key'], $known, true)) {
            return null;
        }

        $held = Permissions::forRole($role['key']);
        $catalog = Permissions::catalog();

        if ($grantedOnly) {
            $catalog = array_values(array_filter(
                array_map(function (array $group) use ($held): array {
                    $group['permissions'] = array_values(array_filter(
                        $group['permissions'],
                        fn (array $permission) => in_array($permission['key'], $held, true),
                    ));

                    return $group;
                }, $catalog),
                fn (array $group) => $group['permissions'] !== [],
            ));
        }

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Roles', 'route' => 'admin.roles'],
                    ['label' => $role['label']],
                ],
                'title' => $role['label'],
                'subtitle' => $role['description'],
            ],
            'role' => $role,
            'catalog' => $catalog,
            'held' => $held,
            'heldCount' => count($held),
            'totalCount' => count(Permissions::keys()),
        ];
    }

    public static function create(): array
    {
        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Roles', 'route' => 'admin.roles'],
                    ['label' => 'New custom role'],
                ],
                'title' => 'New custom role',
                'subtitle' => 'System roles cannot be created from here. Tick the permissions this role should carry.',
            ],
            'role' => [
                'key' => '',
                'label' => '',
                'scope' => 'Academy',
                'elevated' => false,
                'system' => false,
                'description' => '',
            ],
            'catalog' => Permissions::catalog(),
            'held' => [],
            'heldCount' => 0,
            'totalCount' => count(Permissions::keys()),
        ];
    }
}
