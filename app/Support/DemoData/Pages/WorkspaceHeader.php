<?php

namespace App\Support\DemoData\Pages;

/**
 * Shared header shape for workspace screens.
 */
class WorkspaceHeader
{
    /**
     * @param  array<string, mixed>  $platform
     * @param  list<array<string, mixed>>  $trail
     * @return array{breadcrumb: list<array<string, mixed>>, title: string, subtitle: string}
     */
    public static function make(array $platform, string $title, string $subtitle, array $trail = []): array
    {
        $breadcrumb = [
            ['label' => $platform['name'], 'route' => 'workspace.dashboard', 'params' => ['platform' => $platform['slug']]],
        ];

        foreach ($trail as $crumb) {
            $breadcrumb[] = $crumb;
        }

        if ($trail === [] || ($trail[array_key_last($trail)]['label'] ?? null) !== $title) {
            $breadcrumb[] = ['label' => $title];
        }

        return [
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'subtitle' => $subtitle,
        ];
    }
}
