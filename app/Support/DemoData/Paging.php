<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Slices a fixture array the way a paginator would, and builds prev/next URLs
 * that keep the current query string (search, filters, empty=1).
 */
class Paging
{
    /**
     * @param  array<int, mixed>  $items
     * @param  array<string, mixed>  $query
     * @return array{rows: array<int, mixed>, pagination: array<string, mixed>}
     */
    public static function paginate(array $items, int $page, int $perPage, string $path, array $query = []): array
    {
        $total = count($items);
        $pages = max(1, (int) ceil(($total ?: 1) / $perPage));

        if ($total === 0) {
            return [
                'rows' => [],
                'pagination' => [
                    'page' => 1,
                    'pages' => 1,
                    'from' => 0,
                    'to' => 0,
                    'total' => 0,
                    'prev' => null,
                    'next' => null,
                ],
            ];
        }

        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;
        $rows = array_values(array_slice($items, $offset, $perPage));

        $url = function (int $target) use ($path, $query): string {
            $query['page'] = $target;

            return $path.'?'.http_build_query($query);
        };

        return [
            'rows' => $rows,
            'pagination' => [
                'page' => $page,
                'pages' => $pages,
                'from' => $offset + 1,
                'to' => $offset + count($rows),
                'total' => $total,
                'prev' => $page > 1 ? $url($page - 1) : null,
                'next' => $page < $pages ? $url($page + 1) : null,
            ],
        ];
    }
}
