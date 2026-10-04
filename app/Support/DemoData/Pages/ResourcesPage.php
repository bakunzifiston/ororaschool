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
        $paged['rows'] = array_map(function (array $row) use ($platform): array {
            $row['type_label'] = Resources::typeLabel($row['type']);
            $row['href'] = route('workspace.resources.show', [
                'platform' => $platform,
                'resource' => $row['slug'],
            ]);
            $row['edit'] = route('workspace.resources.edit', [
                'platform' => $platform,
                'resource' => $row['slug'],
            ]);
            $row['delete'] = 'delete-'.$row['slug'];
            $row['destroy'] = route('workspace.resources.destroy', [
                'platform' => $platform,
                'resource' => $row['slug'],
            ]);

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

    /**
     * @return array<string, mixed>|null
     */
    public static function form(string $platform, ?string $slug = null): ?array
    {
        $current = Platforms::require($platform);
        $record = Platform::query()->where('slug', $platform)->first() ?? Platform::firstOrCreateFromSlug($platform);
        $resource = null;
        $values = [
            'title' => '',
            'type' => '',
            'attached_kind' => '',
            'attached_key' => '',
            'source_url' => '',
        ];

        if ($slug !== null) {
            $resource = Resources::find($slug, $platform);

            if ($resource === null) {
                return null;
            }

            $resource = Resources::present($resource);
            $values = [
                'title' => $resource['title'],
                'type' => $resource['type'],
                'attached_kind' => $resource['attached_kind'],
                'attached_key' => $resource['attached_key'] ?? LearningResource::keyForAttachedTo(
                    $record,
                    (string) $resource['attached_kind'],
                    (string) $resource['attached_to'],
                ),
                'source_url' => $resource['source_url'] ?? '',
            ];
        }

        return [
            'platform' => $current,
            'resource' => $resource,
            'values' => $values,
            'header' => WorkspaceHeader::make(
                $current,
                $slug === null ? 'Upload a resource' : 'Edit resource',
                'Academy files are open. Course, module and lesson files wait until a learner enrols.',
                [
                    ['label' => 'Resources', 'route' => 'workspace.resources', 'params' => ['platform' => $platform]],
                    ['label' => $slug === null ? 'Upload a resource' : $resource['title']],
                ],
            ),
            'types' => Resources::formTypes(),
            'kinds' => [
                'academy' => 'Academy',
                'course' => 'Course',
                'module' => 'Module',
                'lesson' => 'Lesson',
            ],
            'targets' => LearningResource::targetsFor($record),
            'accepts' => Resources::accepts(),
            'hints' => Resources::fileHints(),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function show(string $platform, string $slug): ?array
    {
        $current = Platforms::require($platform);
        $resource = Resources::find($slug, $platform);

        if ($resource === null) {
            return null;
        }

        $resource = Resources::withFileRoutes($resource, 'workspace.resources.file', [
            'platform' => $platform,
            'resource' => $slug,
        ]);

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                $resource['title'],
                $resource['type_label'].' · '.$resource['attached_to'],
                [
                    ['label' => 'Resources', 'route' => 'workspace.resources', 'params' => ['platform' => $platform]],
                    ['label' => $resource['title']],
                ],
            ),
            'resource' => array_merge($resource, [
                'edit' => route('workspace.resources.edit', [
                    'platform' => $platform,
                    'resource' => $slug,
                ]),
                'delete' => 'delete-'.$slug,
                'destroy' => route('workspace.resources.destroy', [
                    'platform' => $platform,
                    'resource' => $slug,
                ]),
            ]),
        ];
    }
}
