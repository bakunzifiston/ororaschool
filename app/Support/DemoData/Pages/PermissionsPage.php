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
        $catalog = $empty ? [] : Permissions::catalog();
        $total = $empty ? 0 : count(Permissions::keys());

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'Orora School', 'route' => 'admin.dashboard'],
                    ['label' => 'Permissions'],
                ],
                'title' => 'Permissions',
                'subtitle' => $total.' keys, grouped by area. Assign them on a role — they are not granted to people directly.',
            ],
            'catalog' => $catalog,
            'total' => $total,
            'emptyTitle' => 'No permissions in the catalogue',
            'emptyMessage' => 'Keys are grouped by area (platforms.*, courses.*, certificates.*). They are assigned on a role, not granted to people directly.',
        ];
    }
}
