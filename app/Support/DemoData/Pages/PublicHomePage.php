<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\PublicCatalog;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class PublicHomePage
{
    /**
     * @return array{
     *     platforms: list<array<string, mixed>>,
     *     featured: list<array<string, mixed>>,
     *     stats: list<array{value: string, label: string, icon: string}>
     * }
     */
    public static function data(): array
    {
        $stats = PublicCatalog::stats();

        return [
            'platforms' => PublicCatalog::activePlatforms(),
            'featured' => PublicCatalog::featured(4),
            'stats' => [
                [
                    'value' => self::formatCourseCount($stats['courses']),
                    'label' => 'Published courses',
                    'icon' => 'book',
                ],
                [
                    'value' => (string) $stats['platforms'],
                    'label' => 'Connected academies',
                    'icon' => 'layers',
                ],
                [
                    'value' => (string) $stats['resources'],
                    'label' => 'Open resources',
                    'icon' => 'file',
                ],
                [
                    'value' => (string) $stats['users'],
                    'label' => 'Users',
                    'icon' => 'users',
                ],
            ],
        ];
    }

    private static function formatCourseCount(int $count): string
    {
        return $count >= 10 ? $count.'+' : (string) $count;
    }
}
