<?php

namespace App\Support\DemoData;

use Illuminate\Support\Str;

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
        $stored = session(self::sessionKey($platform));

        if (is_array($stored)) {
            return $stored;
        }

        return self::fixtureTree($platform);
    }

    /**
     * @return array{slug: string, name: string}|null
     */
    public static function add(string $platform, string $academySlug, string $name, ?string $parentSlug = null): ?array
    {
        $tree = self::tree($platform);
        $academyIndex = self::academyIndex($tree, $academySlug);

        if ($academyIndex === null) {
            return null;
        }

        $slug = self::uniqueSlug($tree, Str::slug($name) ?: 'category');

        if ($parentSlug === null || $parentSlug === '') {
            $tree[$academyIndex]['categories'][] = [
                'slug' => $slug,
                'name' => $name,
                'children' => [],
            ];
            self::store($platform, $tree);

            return ['slug' => $slug, 'name' => $name];
        }

        foreach ($tree[$academyIndex]['categories'] as $index => $category) {
            if (($category['slug'] ?? '') !== $parentSlug) {
                continue;
            }

            $tree[$academyIndex]['categories'][$index]['children'][] = [
                'slug' => $slug,
                'name' => $name,
            ];
            self::store($platform, $tree);

            return ['slug' => $slug, 'name' => $name];
        }

        return null;
    }

    /**
     * @return array{slug: string, name: string}|null
     */
    public static function remove(string $platform, string $slug): ?array
    {
        $tree = self::tree($platform);

        foreach ($tree as $academyIndex => $academy) {
            foreach ($academy['categories'] as $categoryIndex => $category) {
                if (($category['slug'] ?? '') === $slug) {
                    $removed = ['slug' => $slug, 'name' => $category['name']];
                    unset($tree[$academyIndex]['categories'][$categoryIndex]);
                    $tree[$academyIndex]['categories'] = array_values($tree[$academyIndex]['categories']);
                    self::store($platform, $tree);

                    return $removed;
                }

                foreach ($category['children'] ?? [] as $childIndex => $child) {
                    if (($child['slug'] ?? '') !== $slug) {
                        continue;
                    }

                    $removed = ['slug' => $slug, 'name' => $child['name']];
                    unset($tree[$academyIndex]['categories'][$categoryIndex]['children'][$childIndex]);
                    $tree[$academyIndex]['categories'][$categoryIndex]['children'] = array_values(
                        $tree[$academyIndex]['categories'][$categoryIndex]['children'],
                    );
                    self::store($platform, $tree);

                    return $removed;
                }
            }
        }

        return null;
    }

    /**
     * @return list<array{slug: string, name: string, platform: string, categories: list<array<string, mixed>>}>
     */
    private static function fixtureTree(string $platform): array
    {
        $tree = [];

        foreach (self::all() as $academy) {
            if ($academy['platform'] === $platform) {
                $tree[$academy['slug']] = $academy;
            }
        }

        foreach (Academies::forPlatform($platform) as $academy) {
            if (! isset($tree[$academy['slug']])) {
                $tree[$academy['slug']] = [
                    'slug' => $academy['slug'],
                    'platform' => $platform,
                    'name' => $academy['name'],
                    'categories' => [],
                ];
            }
        }

        return array_values($tree);
    }

    /**
     * @param  list<array<string, mixed>>  $tree
     */
    private static function academyIndex(array $tree, string $slug): ?int
    {
        foreach ($tree as $index => $academy) {
            if (($academy['slug'] ?? '') === $slug) {
                return $index;
            }
        }

        return null;
    }

    /**
     * @param  list<array<string, mixed>>  $tree
     */
    private static function uniqueSlug(array $tree, string $base): string
    {
        $used = [];

        foreach ($tree as $academy) {
            $used[$academy['slug']] = true;

            foreach ($academy['categories'] ?? [] as $category) {
                $used[$category['slug']] = true;

                foreach ($category['children'] ?? [] as $child) {
                    $used[$child['slug']] = true;
                }
            }
        }

        $slug = $base;
        $suffix = 2;

        while (isset($used[$slug])) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @param  list<array<string, mixed>>  $tree
     */
    private static function store(string $platform, array $tree): void
    {
        session([self::sessionKey($platform) => $tree]);
    }

    private static function sessionKey(string $platform): string
    {
        return 'demo.categories.'.$platform;
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
