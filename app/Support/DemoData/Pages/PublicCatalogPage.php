<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Paging;
use App\Support\DemoData\PublicCatalog;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class PublicCatalogPage
{
    /**
     * @param  array<string, string>  $filters
     * @return array<string, mixed>
     */
    public static function index(array $filters, int $page = 1, bool $empty = false): array
    {
        $rows = $empty ? [] : PublicCatalog::filter($filters);
        $path = route('catalog.courses');
        $query = array_filter($filters, fn ($value) => $value !== '' && $value !== null);
        $paged = Paging::paginate($rows, $page, PublicCatalog::PER_PAGE, $path, $query);

        return [
            'header' => PublicHeader::make(
                'Explore courses',
                'Every published course across the four live platforms.',
            ),
            'title' => 'Explore courses',
            'subtitle' => 'Every published course across the four live platforms.',
            'filters' => array_merge([
                'platform' => '',
                'academy' => '',
                'difficulty' => '',
                'language' => '',
                'price' => '',
                'q' => '',
            ], $filters),
            'options' => PublicCatalog::filterOptions($filters['platform'] ?? null),
            'showPlatformFilter' => true,
            'formAction' => $path,
            'courses' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'Nothing matches those filters',
            'emptyMessage' => 'Clear a filter or search for a different word. The catalogue only lists published courses on live platforms.',
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function show(string $slug): ?array
    {
        $course = PublicCatalog::findPublished($slug);

        if (! $course) {
            return null;
        }

        $syllabus = PublicCatalog::previewSyllabus($slug);
        $hasPreview = false;

        foreach ($syllabus as $module) {
            foreach ($module['lessons'] as $lesson) {
                if ($lesson['is_preview']) {
                    $hasPreview = true;
                    break 2;
                }
            }
        }

        $cta = ($course['enrollment_required'] ?? true)
            ? 'Sign in to enrol'
            : 'Sign in to start learning';

        return [
            'course' => $course,
            'syllabus' => $syllabus,
            'hasPreview' => $hasPreview,
            'cta' => $cta,
        ];
    }
}
