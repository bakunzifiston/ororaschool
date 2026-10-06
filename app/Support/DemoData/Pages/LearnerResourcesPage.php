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
    /**
     * @param  array{type?: string, platform?: string}  $filters
     */
    public static function index(array $filters = [], bool $empty = false): array
    {
        $type = (string) ($filters['type'] ?? '');
        $platform = (string) ($filters['platform'] ?? '');
        $filtersActive = $type !== '' || $platform !== '';

        $allRows = $empty ? [] : LearnerProgress::resources();
        $platformOptions = self::platformOptions($allRows);

        $rows = array_values(array_filter(
            $allRows,
            function (array $row) use ($type, $platform): bool {
                if ($type !== '' && ($row['type'] ?? '') !== $type) {
                    return false;
                }

                if ($platform !== '' && ($row['platform'] ?? '') !== $platform) {
                    return false;
                }

                return true;
            },
        ));

        $rows = array_map(function (array $row) {
            $current = Platforms::find($row['platform']);

            return array_merge($row, [
                'platform_name' => $current['name'] ?? $row['platform'],
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
            'filters' => [
                'type' => $type,
                'platform' => $platform,
                'types' => Resources::types(),
                'platforms' => $platformOptions,
            ],
            'rows' => $rows,
            'emptyTitle' => $filtersActive ? 'No resources match those filters' : 'No resources on your courses yet',
            'emptyMessage' => $filtersActive
                ? 'Try another academy or type, or clear the filters to see everything on your record.'
                : 'Academy handouts appear without enrolment. Course, module and lesson files wait until they are attached to a course you are on.',
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, string>
     */
    private static function platformOptions(array $rows): array
    {
        $platforms = ['' => 'All academies'];

        foreach ($rows as $row) {
            $slug = (string) ($row['platform'] ?? '');
            $current = Platforms::find($slug);
            $name = (string) ($current['name'] ?? $slug);

            if ($slug !== '' && $name !== '') {
                $platforms[$slug] = $name;
            }
        }

        asort($platforms, SORT_NATURAL | SORT_FLAG_CASE);

        return ['' => 'All academies'] + array_diff_key($platforms, ['' => true]);
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
