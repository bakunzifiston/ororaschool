<?php

namespace App\Support\DemoData\Pages;

/**
 * Shared header shape for public screens.
 */
class PublicHeader
{
    /**
     * @param  list<array<string, mixed>>  $trail
     * @return array{breadcrumb: list<array<string, mixed>>, title: string, subtitle: ?string}
     */
    public static function make(string $title, ?string $subtitle = null, array $trail = []): array
    {
        $breadcrumb = [
            ['label' => 'Home', 'route' => 'home'],
        ];

        foreach ($trail as $crumb) {
            $breadcrumb[] = $crumb;
        }

        $lastLabel = $trail === [] ? ($breadcrumb[0]['label'] ?? null) : ($trail[array_key_last($trail)]['label'] ?? null);
        if (strcasecmp((string) $lastLabel, $title) !== 0) {
            $breadcrumb[] = ['label' => $title];
        }

        return [
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'subtitle' => $subtitle,
        ];
    }
}
