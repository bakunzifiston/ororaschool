<?php

namespace App\Support\DemoData\Pages;

/**
 * Shared header shape for learner screens.
 */
class LearnerHeader
{
    /**
     * @param  list<array<string, mixed>>  $trail
     * @return array{breadcrumb: list<array<string, mixed>>, title: string, subtitle: string}
     */
    public static function make(string $title, string $subtitle, array $trail = []): array
    {
        $breadcrumb = [
            ['label' => 'Orora School', 'route' => 'learner.dashboard'],
        ];

        foreach ($trail as $crumb) {
            $breadcrumb[] = $crumb;
        }

        // An empty trail means there is no section under the home crumb — do
        // not repeat a greeting or other page title as a fake last segment.
        $lastLabel = $trail === [] ? null : ($trail[array_key_last($trail)]['label'] ?? null);
        if ($trail !== [] && strcasecmp((string) $lastLabel, $title) !== 0) {
            $breadcrumb[] = ['label' => $title];
        }

        return [
            'breadcrumb' => $breadcrumb,
            'title' => $title,
            'subtitle' => $subtitle,
        ];
    }
}
