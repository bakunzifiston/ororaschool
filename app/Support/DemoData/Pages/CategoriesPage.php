<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Categories;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class CategoriesPage
{
    public static function index(string $platform, bool $empty = false): array
    {
        $current = Platforms::require($platform);
        $tree = $empty ? [] : Categories::tree($platform);

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Categories',
                'Academy → category → sub-category for '.$current['name'].'. Add a category under an academy, or a sub-category under a category. Drag handles are visual — nothing is persisted in this build.',
            ),
            'tree' => $tree,
            'emptyTitle' => 'No taxonomy on '.$current['name'].' yet',
            'emptyMessage' => 'Academies and their categories are authored here. The first academy becomes the container for courses.',
        ];
    }
}
