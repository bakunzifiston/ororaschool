<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Academies sit under a platform: a named school of practice (milk hygiene,
 * animal ID, ration formulation) that groups courses. The workspace owns CRUD;
 * Super Admin only needs the count and a jumping-off list.
 */
class Academies
{
    public static function all(): array
    {
        return [
            ['slug' => 'plot-records', 'platform' => 'ororafarm', 'name' => 'Plot records and costing', 'courses' => 2, 'status' => 'published'],
            ['slug' => 'cooperative-books', 'platform' => 'ororafarm', 'name' => 'Cooperative books', 'courses' => 1, 'status' => 'draft'],
            ['slug' => 'season-planning', 'platform' => 'ororafarm', 'name' => 'Season planning', 'courses' => 1, 'status' => 'published'],
            ['slug' => 'milk-hygiene', 'platform' => 'gemura', 'name' => 'Milk hygiene', 'courses' => 2, 'status' => 'published'],
            ['slug' => 'herd-fertility', 'platform' => 'gemura', 'name' => 'Herd fertility', 'courses' => 1, 'status' => 'approved'],
            ['slug' => 'collection-centres', 'platform' => 'gemura', 'name' => 'Collection centres', 'courses' => 1, 'status' => 'published'],
            ['slug' => 'identification', 'platform' => 'buchapro', 'name' => 'Animal identification', 'courses' => 1, 'status' => 'published'],
            ['slug' => 'movement', 'platform' => 'buchapro', 'name' => 'Movement and transport', 'courses' => 1, 'status' => 'published'],
            ['slug' => 'traceback', 'platform' => 'buchapro', 'name' => 'Outbreak traceback', 'courses' => 2, 'status' => 'pending_review'],
            ['slug' => 'ration', 'platform' => 'feedgrid', 'name' => 'Ration formulation', 'courses' => 1, 'status' => 'published'],
            ['slug' => 'feed-safety', 'platform' => 'feedgrid', 'name' => 'Feed safety and storage', 'courses' => 2, 'status' => 'published'],
            ['slug' => 'poultry-houses', 'platform' => 'ubworozi', 'name' => 'Poultry housing', 'courses' => 0, 'status' => 'archived'],
        ];
    }

    public static function forPlatform(string $slug): array
    {
        return array_values(array_filter(
            self::all(),
            fn (array $academy) => $academy['platform'] === $slug,
        ));
    }

    public static function countFor(string $slug): int
    {
        return count(self::forPlatform($slug));
    }
}
