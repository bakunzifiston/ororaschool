<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Academies;
use App\Support\DemoData\Courses;
use App\Support\DemoData\Paging;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * A jumping-off list, not a CRUD surface. Each row points into the workspace.
 */
class GlobalContentPage
{
    public static function index(bool $empty = false, int $page = 1): array
    {
        $rows = $empty ? [] : self::rows();
        $paged = Paging::paginate($rows, $page, 10, '/admin/content', array_filter(['empty' => $empty ? 1 : null]));

        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                    ['label' => 'Global content'],
                ],
                'title' => 'Global content',
                'subtitle' => 'Courses across the estate. Open a row to work on it inside that academy’s workspace.',
            ],
            'columns' => [
                ['key' => 'title', 'label' => 'Title'],
                ['key' => 'kind', 'label' => 'Kind'],
                ['key' => 'platform', 'label' => 'Academy'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
                ['key' => 'actions', 'label' => '', 'align' => 'right'],
            ],
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'Nothing published on any academy yet',
            'emptyMessage' => 'Courses are authored in an academy workspace. When the first one is saved, it will appear here as a shortcut into that workspace.',
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function rows(): array
    {
        $platforms = array_column(Platforms::all(), 'name', 'slug');
        $rows = [];

        foreach (Courses::all() as $course) {
            $rows[] = [
                'title' => $course['title'],
                'kind' => 'Course',
                'platform' => $platforms[$course['platform']] ?? $course['platform'],
                'platform_slug' => $course['platform'],
                'status' => $course['status'],
                'view' => route('workspace.courses.show', ['platform' => $course['platform'], 'course' => $course['slug']]),
                'edit' => route('workspace.courses.edit', ['platform' => $course['platform'], 'course' => $course['slug']]),
            ];
        }

        foreach (Academies::all() as $academy) {
            $rows[] = [
                'title' => $academy['name'],
                'kind' => 'Academy',
                'platform' => $platforms[$academy['platform']] ?? $academy['platform'],
                'platform_slug' => $academy['platform'],
                'status' => $academy['status'],
                'view' => route('workspace.categories', ['platform' => $academy['platform']]),
                'edit' => null,
            ];
        }

        return $rows;
    }
}
