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
            ]);
        }, $rows);

        return [
            'header' => LearnerHeader::make(
                'Resources',
                'Handouts from the courses you are on. Download is a placeholder in this build.',
                [
                    ['label' => 'Resources'],
                ],
            ),
            'filters' => ['type' => $type, 'types' => Resources::types()],
            'rows' => $rows,
            'emptyTitle' => 'No resources on your courses yet',
            'emptyMessage' => 'Field sheets and manuals appear here once they are attached to a course you are enrolled in.',
        ];
    }
}
