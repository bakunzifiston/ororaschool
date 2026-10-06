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
        $paths = $empty ? [] : array_map([self::class, 'decorate'], LearningPaths::all());

        return [
            'header' => LearnerHeader::make(
                'Learning paths',
                'An ordered sequence of courses. Some stay on one academy; others stitch two or three together.',
                [
                    ['label' => 'Learning paths'],
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

        $path = self::decorate($path);

        return [
            'header' => LearnerHeader::make(
                $path['title'],
                $path['cross_platform']
                    ? $path['platform_count'].' academies on one path'
                    : 'All on '.$path['platforms'][0],
                [
                    ['label' => 'Learning paths', 'route' => 'learner.paths'],
                    ['label' => $path['title']],
                ],
            ),
            'path' => $path,
        ];
    }

    /**
     * @param  array<string, mixed>  $path
     * @return array<string, mixed>
     */
    private static function decorate(array $path): array
    {
        $next = null;

        foreach ($path['items'] as $item) {
            if (($item['state'] ?? '') !== 'completed') {
                $next = $item;
                break;
            }
        }

        $path['course_count'] = count($path['items']);
        $path['next'] = $next;
        $path['started'] = (int) ($path['progress'] ?? 0) > 0;
        $path['cta'] = $path['started'] ? 'Continue path' : 'Start learning';

        return $path;
    }
}
