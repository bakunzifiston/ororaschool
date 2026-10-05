<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Paging;
use App\Support\DemoData\PublicCatalog;
use App\Support\DemoData\Resources;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class PublicResourcesPage
{
    /**
     * @param  array<string, string>  $filters
     * @return array<string, mixed>
     */
    public static function index(array $filters, int $page = 1, bool $empty = false): array
    {
        $rows = $empty ? [] : self::filter($filters);
        $path = route('catalog.resources');
        $query = array_filter($filters, fn ($value) => $value !== '' && $value !== null);
        $paged = Paging::paginate($rows, $page, PublicCatalog::PER_PAGE, $path, $query);

        $platforms = ['' => 'All academies'];

        foreach (PublicCatalog::activePlatforms() as $platform) {
            $platforms[$platform['slug']] = $platform['name'];
        }

        return [
            'header' => PublicHeader::make(
                'Resources',
                'Open academy handouts. Course, module and lesson files stay with enrolment.',
            ),
            'title' => 'Resources',
            'subtitle' => 'Open academy handouts. Course, module and lesson files stay with enrolment.',
            'filters' => array_merge([
                'platform' => '',
                'type' => '',
                'q' => '',
            ], $filters),
            'options' => [
                'platforms' => $platforms,
                'types' => Resources::types(),
            ],
            'formAction' => $path,
            'resources' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'No resources match those filters',
            'emptyMessage' => 'Clear a filter or search for a different word. This page only lists open academy handouts.',
        ];
    }

    /**
     * @param  array<string, string>  $filters
     * @return list<array<string, mixed>>
     */
    private static function filter(array $filters): array
    {
        $platform = (string) ($filters['platform'] ?? '');
        $type = (string) ($filters['type'] ?? '');
        $q = strtolower(trim((string) ($filters['q'] ?? '')));

        return array_values(array_filter(
            Resources::open(),
            function (array $resource) use ($platform, $type, $q): bool {
                if ($platform !== '' && $resource['platform'] !== $platform) {
                    return false;
                }

                if ($type !== '' && $resource['type'] !== $type) {
                    return false;
                }

                if ($q === '') {
                    return true;
                }

                $haystack = strtolower(implode(' ', [
                    $resource['title'],
                    $resource['attached_to'],
                    $resource['platform_name'] ?? '',
                    $resource['type_label'] ?? '',
                ]));

                return str_contains($haystack, $q);
            },
        ));
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function show(string $slug): ?array
    {
        $resource = Resources::find($slug);

        if ($resource === null || ! Resources::isPubliclyOpen($resource)) {
            return null;
        }

        $resource = Resources::withFileRoutes($resource, 'catalog.resources.file', ['resource' => $slug]);

        return [
            'header' => PublicHeader::make(
                $resource['title'],
                $resource['type_label'].' · '.$resource['platform_name'],
                [
                    ['label' => 'Resources', 'route' => 'catalog.resources'],
                    ['label' => $resource['title']],
                ],
            ),
            'resource' => $resource,
        ];
    }
}
