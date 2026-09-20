<?php

namespace App\Support\DemoData\Pages;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class SettingsPage
{
    public static function data(): array
    {
        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'Orora School', 'route' => 'admin.dashboard'],
                    ['label' => 'Settings'],
                ],
                'title' => 'Settings',
                'subtitle' => 'Estate-wide defaults. Platform workspaces inherit these unless they override them later.',
            ],
            'values' => [
                'certificate_format' => 'OS-{PLATFORM}-{YEAR}-{SEQ:4}',
                'pagination' => '10',
                'support_name' => 'Orora School desk',
                'support_email' => 'help@ororaschool.rw',
                'support_phone' => '+250 788 400 210',
                'session_timeout' => '60',
                'locale' => 'en',
            ],
            'formats' => [
                'OS-{PLATFORM}-{YEAR}-{SEQ:4}' => 'OS-GEM-2026-1847',
                'ORORA-{YEAR}{SEQ:5}' => 'ORORA-20261847',
                '{PLATFORM}/{YEAR}/{SEQ:3}' => 'GEM/2026/147',
            ],
            'paginationSizes' => ['10' => '10 per page', '25' => '25 per page', '50' => '50 per page'],
            'locales' => ['en' => 'English', 'rw' => 'Kinyarwanda', 'fr' => 'French'],
        ];
    }
}
