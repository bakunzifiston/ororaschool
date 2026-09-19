<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Nested taxonomy: academy → category → sub-category, scoped to a platform.
 */
class Categories
{
    /**
     * @return list<array{slug: string, name: string, platform: string, categories: list<array<string, mixed>>}>
     */
    public static function tree(string $platform): array
    {
        return array_values(array_filter(
            self::all(),
            fn (array $academy) => $academy['platform'] === $platform,
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'slug' => 'milk-hygiene', 'platform' => 'gemura', 'name' => 'Milk hygiene',
                'categories' => [
                    ['slug' => 'milking-routine', 'name' => 'Milking routine', 'children' => [
                        ['slug' => 'cmt-scoring', 'name' => 'CMT scoring'],
                        ['slug' => 'fore-stripping', 'name' => 'Fore-stripping'],
                    ]],
                    ['slug' => 'calf-rearing', 'name' => 'Calf rearing', 'children' => [
                        ['slug' => 'colostrum-timing', 'name' => 'Colostrum timing'],
                    ]],
                ],
            ],
            [
                'slug' => 'herd-fertility', 'platform' => 'gemura', 'name' => 'Herd fertility',
                'categories' => [
                    ['slug' => 'artificial-insemination', 'name' => 'Artificial insemination', 'children' => [
                        ['slug' => 'standing-heat', 'name' => 'Standing heat'],
                        ['slug' => 'morning-evening-rule', 'name' => 'Morning–evening rule'],
                    ]],
                    ['slug' => 'dry-season-feed', 'name' => 'Dry-season feed', 'children' => [
                        ['slug' => 'pit-silage', 'name' => 'Pit silage'],
                    ]],
                ],
            ],
            [
                'slug' => 'collection-centres', 'platform' => 'gemura', 'name' => 'Collection centres',
                'categories' => [
                    ['slug' => 'temperature-logs', 'name' => 'Temperature logs', 'children' => [
                        ['slug' => 'rejection-protocol', 'name' => 'Rejection protocol'],
                    ]],
                    ['slug' => 'evening-intake', 'name' => 'Evening intake', 'children' => [
                        ['slug' => 'lactometer', 'name' => 'Lactometer checks'],
                    ]],
                ],
            ],
            [
                'slug' => 'identification', 'platform' => 'buchapro', 'name' => 'Animal identification',
                'categories' => [
                    ['slug' => 'ear-tag-application', 'name' => 'Ear-tag application', 'children' => [
                        ['slug' => 'lost-tags', 'name' => 'Lost-tag replacements'],
                        ['slug' => 'herd-register', 'name' => 'Herd register'],
                    ]],
                    ['slug' => 'kraal-walk', 'name' => 'Kraal walk', 'children' => [
                        ['slug' => 'discrepancy-report', 'name' => 'Discrepancy report'],
                    ]],
                ],
            ],
            [
                'slug' => 'movement', 'platform' => 'buchapro', 'name' => 'Movement and transport',
                'categories' => [
                    ['slug' => 'roadblock-checks', 'name' => 'Roadblock checks', 'children' => [
                        ['slug' => 'permit-fields', 'name' => 'Permit fields'],
                    ]],
                    ['slug' => 'market-gates', 'name' => 'Market gates', 'children' => [
                        ['slug' => 'batch-lists', 'name' => 'Batch lists'],
                    ]],
                ],
            ],
            [
                'slug' => 'traceback', 'platform' => 'buchapro', 'name' => 'Outbreak traceback',
                'categories' => [
                    ['slug' => 'slaughter-batches', 'name' => 'Slaughter batches', 'children' => [
                        ['slug' => 'carcass-link', 'name' => 'Carcass to kraal'],
                    ]],
                    ['slug' => 'exposure-windows', 'name' => 'Exposure windows', 'children' => [
                        ['slug' => 'contact-animals', 'name' => 'Contact animals'],
                    ]],
                ],
            ],
            [
                'slug' => 'plot-records', 'platform' => 'ororafarm', 'name' => 'Plot records and costing',
                'categories' => [
                    ['slug' => 'field-books', 'name' => 'Field books', 'children' => [
                        ['slug' => 'planting-dates', 'name' => 'Planting dates'],
                    ]],
                    ['slug' => 'margins', 'name' => 'Margins', 'children' => [
                        ['slug' => 'family-labour', 'name' => 'Family labour'],
                    ]],
                ],
            ],
            [
                'slug' => 'season-planning', 'platform' => 'ororafarm', 'name' => 'Season planning',
                'categories' => [
                    ['slug' => 'plot-mapping', 'name' => 'Plot mapping', 'children' => [
                        ['slug' => 'terraces', 'name' => 'Terraces'],
                    ]],
                ],
            ],
            [
                'slug' => 'cooperative-books', 'platform' => 'ororafarm', 'name' => 'Cooperative books',
                'categories' => [
                    ['slug' => 'share-register', 'name' => 'Share register', 'children' => []],
                ],
            ],
            [
                'slug' => 'ration', 'platform' => 'feedgrid', 'name' => 'Ration formulation',
                'categories' => [
                    ['slug' => 'local-ingredients', 'name' => 'Local ingredients', 'children' => [
                        ['slug' => 'price-sheets', 'name' => 'Price sheets'],
                    ]],
                ],
            ],
            [
                'slug' => 'feed-safety', 'platform' => 'feedgrid', 'name' => 'Feed safety and storage',
                'categories' => [
                    ['slug' => 'moisture-control', 'name' => 'Moisture control', 'children' => [
                        ['slug' => 'pallet-stacking', 'name' => 'Pallet stacking'],
                    ]],
                    ['slug' => 'mineral-licks', 'name' => 'Mineral licks', 'children' => []],
                ],
            ],
        ];
    }
}
