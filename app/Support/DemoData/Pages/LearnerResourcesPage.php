<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\LearnerProgress;
use App\Support\DemoData\Platforms;
use App\Support\DemoData\Resources;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LearnerResourcesPage
{
    public static function index(string $type = '', bool $empty = false): array
    {
        $rows = $empty ? [] : LearnerProgress::resources();

        if ($type !== '') {
            $rows = array_values(array_filter($rows, fn (array $row) => $row['type'] === $type));
        }

        $rows = array_map(function (array $row) {
            $platform = Platforms::find($row['platform']);

            return array_merge($row, [
                'platform_name' => $platform['name'] ?? $row['platform'],
                'href' => route('learner.resources.show', ['resource' => $row['slug']]),
            ]);
        }, $rows);

        return [
            'header' => LearnerHeader::make(
                'Resources',
                'Academy handouts are open. Course files wait until you enrol.',
                [
                    ['label' => 'Resources'],
                ],
            ),
            'filters' => ['type' => $type, 'types' => Resources::types()],
            'rows' => $rows,
            'emptyTitle' => 'No resources on your courses yet',
            'emptyMessage' => 'Academy handouts appear without enrolment. Course, module and lesson files wait until they are attached to a course you are on.',
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function show(string $slug): ?array
    {
        if (! LearnerProgress::canOpenResource($slug)) {
            return null;
        }

        $resource = Resources::find($slug);

        if ($resource === null) {
            return null;
        }

        $resource = Resources::withFileRoutes($resource, 'learner.resources.file', ['resource' => $slug]);

        return [
            'header' => LearnerHeader::make(
                $resource['title'],
                $resource['type_label'].' · '.$resource['attached_to'],
                [
                    ['label' => 'Resources', 'route' => 'learner.resources'],
                    ['label' => $resource['title']],
                ],
            ),
            'resource' => $resource,
        ];
    }
}
