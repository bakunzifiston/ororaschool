<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Permissions;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Read-only reference. The role editor is where keys get assigned.
 */
class PermissionsPage
{
    public static function index(bool $empty = false): array
    {
        $rows = $empty ? [] : self::rows();

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Permissions'],
                ],
                'title' => 'Permissions',
                'subtitle' => 'What a role can be given. Assign these on a role — not to a person.',
            ],
            'columns' => [
                ['key' => 'label', 'label' => 'Permission'],
                ['key' => 'key', 'label' => 'Key'],
                ['key' => 'area', 'label' => 'Area'],
            ],
            'rows' => $rows,
            'emptyTitle' => 'No permissions in the catalogue',
            'emptyMessage' => 'Keys are grouped by area (platforms.*, courses.*, certificates.*). They are assigned on a role, not granted to people directly.',
        ];
    }

    /**
     * @return list<array{label: string, key: string, area: string}>
     */
    private static function rows(): array
    {
        $rows = [];

        foreach (Permissions::catalog() as $group) {
            foreach ($group['permissions'] as $permission) {
                $rows[] = [
                    'label' => $permission['label'],
                    'key' => $permission['key'],
                    'area' => $group['label'],
                ];
            }
        }

        return $rows;
    }
}
