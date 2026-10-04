<?php

namespace App\Support\DemoData\Pages;

use App\Models\LearningResource;
use App\Models\Platform;
use App\Support\DemoData\Paging;
use App\Support\DemoData\Platforms;
use App\Support\DemoData\Resources;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class ResourcesPage
{
    public static function index(string $platform, bool $empty = false, int $page = 1, string $type = ''): array
    {
        $current = Platforms::require($platform);
        $rows = $empty ? [] : Resources::forPlatform($platform);

        if ($type !== '') {
            $rows = array_values(array_filter($rows, fn (array $row) => $row['type'] === $type));
        }

        $paged = Paging::paginate(
            $rows,
            $page,
            10,
            '/workspace/'.$platform.'/resources',
            array_filter(['empty' => $empty ? 1 : null, 'type' => $type !== '' ? $type : null]),
        );
        $paged['rows'] = array_map(function (array $row): array {
            $row['type_label'] = Resources::typeLabel($row['type']);

            return $row;
        }, $paged['rows']);

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Resources',
                'Handouts, templates and clips used on '.$current['name'].'.',
            ),
            'filters' => ['type' => $type, 'types' => Resources::types()],
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'No resources on '.$current['name'].' yet',
            'emptyMessage' => 'Upload a field sheet, a manual or a clip. Attach it to a course, a module, a lesson or an academy.',
        ];
    }

    public static function form(string $platform): array
    {
        $current = Platforms::require($platform);

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Upload a resource',
                'Academy files are open. Course, module and lesson files wait until a learner enrols.',
                [
                    ['label' => 'Resources', 'route' => 'workspace.resources', 'params' => ['platform' => $platform]],
                    ['label' => 'Upload a resource'],
                ],
            ),
            'types' => Resources::formTypes(),
            'kinds' => [
                'academy' => 'Academy',
                'course' => 'Course',
                'module' => 'Module',
                'lesson' => 'Lesson',
            ],
            'targets' => LearningResource::targetsFor(
                Platform::query()->where('slug', $platform)->first() ?? Platform::firstOrCreateFromSlug($platform),
            ),
            'accepts' => Resources::accepts(),
            'hints' => Resources::fileHints(),
        ];
    }
}
