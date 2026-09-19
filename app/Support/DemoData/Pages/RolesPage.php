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
        $roles = $empty ? [] : Roles::all();
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
                    ['label' => 'Orora School', 'route' => 'admin.dashboard'],
                    ['label' => 'Roles'],
                ],
                'title' => 'Roles',
                'subtitle' => 'Five system roles are protected. Custom roles are data: a name and a set of permission keys.',
            ],
            'roles' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'No roles defined',
            'emptyMessage' => 'The five system roles are created with the estate. Custom roles — Reviewer, Field Coordinator — are added here.',
        ];
    }

    public static function edit(string $key): ?array
    {
        $role = Roles::find($key);
        $known = array_column(Roles::all(), 'key');

        if (! in_array($role['key'], $known, true)) {
            return null;
        }

        $held = Permissions::forRole($role['key']);

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'Orora School', 'route' => 'admin.dashboard'],
                    ['label' => 'Roles', 'route' => 'admin.roles'],
                    ['label' => $role['label']],
                ],
                'title' => $role['label'],
                'subtitle' => $role['description'],
            ],
            'role' => $role,
            'catalog' => Permissions::catalog(),
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
                    ['label' => 'Orora School', 'route' => 'admin.dashboard'],
                    ['label' => 'Roles', 'route' => 'admin.roles'],
                    ['label' => 'New role'],
                ],
                'title' => 'New custom role',
                'subtitle' => 'System roles cannot be created from here. Tick the permissions this role should carry.',
            ],
            'role' => [
                'key' => '',
                'label' => '',
                'scope' => 'Platform',
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
