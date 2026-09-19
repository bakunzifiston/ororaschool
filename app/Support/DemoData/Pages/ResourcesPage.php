<?php

namespace App\Support\DemoData\Pages;

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
        $current = Platforms::find($platform) ?? Platforms::find('gemura');
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

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Resources',
                'Handouts for '.$current['name'].'. Each row says whether it hangs off a course, module, lesson or academy.',
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
        $current = Platforms::find($platform) ?? Platforms::find('gemura');

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Upload a resource',
                'Files stay on '.$current['name'].'. Upload is a placeholder in this build.',
                [
                    ['label' => 'Resources', 'route' => 'workspace.resources', 'params' => ['platform' => $platform]],
                    ['label' => 'Upload'],
                ],
            ),
            'types' => Resources::types(),
            'kinds' => ['course' => 'Course', 'module' => 'Module', 'lesson' => 'Lesson', 'academy' => 'Academy'],
        ];
    }
}
