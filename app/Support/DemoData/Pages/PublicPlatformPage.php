<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Paging;
use App\Support\DemoData\PublicCatalog;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class PublicPlatformPage
{
    public static function index(): array
    {
        return [
            'header' => PublicHeader::make(
                'Platforms',
                'Four live academies. Each teaches the work of its own system. Your training record is the same wherever you enrol.',
            ),
            'platforms' => PublicCatalog::activePlatforms(),
        ];
    }

    /**
     * @param  array<string, string>  $filters
     * @return array<string, mixed>|null
     */
    public static function show(string $slug, array $filters, int $page = 1, bool $empty = false): ?array
    {
        $platform = PublicCatalog::findActivePlatform($slug);

        if (! $platform) {
            return null;
        }

        $filters['platform'] = $slug;
        $rows = $empty ? [] : PublicCatalog::filter($filters);
        $path = route('catalog.platforms.show', ['platform' => $slug]);
        $query = array_filter($filters, fn ($value) => $value !== '' && $value !== null);
        unset($query['platform']);
        $paged = Paging::paginate($rows, $page, PublicCatalog::PER_PAGE, $path, $query);

        return [
            'header' => PublicHeader::make(
                $platform['name'],
                $platform['discipline'].' · '.$platform['region'],
                [
                    ['label' => 'Platforms', 'route' => 'catalog.platforms'],
                    ['label' => $platform['name']],
                ],
            ),
            'platform' => $platform,
            'filters' => array_merge([
                'platform' => $slug,
                'academy' => '',
                'difficulty' => '',
                'language' => '',
                'price' => '',
                'q' => '',
            ], $filters),
            'options' => PublicCatalog::filterOptions($slug),
            'showPlatformFilter' => false,
            'formAction' => $path,
            'courses' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'No '.$platform['name'].' courses match those filters',
            'emptyMessage' => 'Clear a filter, or browse the full catalogue — this page only lists published '.$platform['name'].' courses.',
        ];
    }
}
