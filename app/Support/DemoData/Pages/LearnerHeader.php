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
