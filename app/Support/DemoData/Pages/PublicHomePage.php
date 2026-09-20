<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\PublicCatalog;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class PublicHomePage
{
    public static function data(): array
    {
        $stats = PublicCatalog::stats();

        return [
            'platforms' => PublicCatalog::activePlatforms(),
            'featured' => PublicCatalog::featured(4),
            'stats' => $stats,
        ];
    }
}
