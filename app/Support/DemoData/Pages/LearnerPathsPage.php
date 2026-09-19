<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\LearningPaths;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LearnerPathsPage
{
    public static function index(bool $empty = false): array
    {
        $paths = $empty ? [] : LearningPaths::all();

        return [
            'header' => LearnerHeader::make(
                'Learning paths',
                'An ordered sequence of courses. Some stay on one platform; others stitch two or three together.',
                [
                    ['label' => 'Learning Paths'],
                ],
            ),
            'paths' => $paths,
            'emptyTitle' => 'No learning path yet',
            'emptyMessage' => 'A path strings courses together in the order that makes sense for your work. Ask your field coordinator to put you on one.',
        ];
    }

    public static function show(string $slug): ?array
    {
        $path = LearningPaths::find($slug);

        if (! $path) {
            return null;
        }

        return [
            'header' => LearnerHeader::make(
                $path['title'],
                $path['cross_platform']
                    ? $path['platform_count'].' platforms on one path'
                    : 'All on '.$path['platforms'][0],
                [
                    ['label' => 'Learning Paths', 'route' => 'learner.paths'],
                    ['label' => $path['title']],
                ],
            ),
            'path' => $path,
        ];
    }
}
