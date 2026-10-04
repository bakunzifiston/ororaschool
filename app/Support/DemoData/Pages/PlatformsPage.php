<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Paging;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Pass $empty = true (from ?empty=1) to render the list empty state without
 * rewriting the fixture.
 */
class PlatformsPage
{
    public static function index(bool $empty = false, int $page = 1): array
    {
        $rows = $empty ? [] : Platforms::all();
        $paged = Paging::paginate(
            $rows,
            $page,
            5,
            '/admin/platforms',
            array_filter(['empty' => $empty ? 1 : null]),
        );

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Academies'],
                ],
                'title' => 'Academies',
                'subtitle' => 'Every academy on FarmSchool. Activate one to open a workspace; deactivate one and its certificates still resolve.',
            ],
            'columns' => [
                ['key' => 'name', 'label' => 'Academy'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
                ['key' => 'academies', 'label' => 'Academies', 'align' => 'right', 'numeric' => true],
                ['key' => 'users', 'label' => 'Users', 'align' => 'right', 'numeric' => true],
                ['key' => 'created', 'label' => 'Created'],
                ['key' => 'actions', 'label' => ''],
            ],
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'No academies on the estate yet',
            'emptyMessage' => 'Add the first academy — OroraFarm, Gemura, BuchaPro or a new one — and its workspace and staff roster will appear here.',
        ];
    }

    public static function form(?string $slug = null): array
    {
        $platform = $slug ? Platforms::find($slug) : null;
        $isEdit = (bool) $platform;

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Academies', 'route' => 'admin.platforms'],
                    ['label' => $isEdit ? 'Edit '.$platform['name'] : 'Add an academy'],
                ],
                'title' => $isEdit ? 'Edit '.$platform['name'] : 'Add an academy',
                'subtitle' => $isEdit
                    ? 'Changes apply across every workspace that already points at this tenant.'
                    : 'It starts inactive until you assign an owner and switch it on.',
            ],
            'platform' => $platform ?? [
                'slug' => '',
                'name' => '',
                'description' => '',
                'status' => 'inactive',
                'discipline' => '',
            ],
            'isEdit' => $isEdit,
        ];
    }
}
