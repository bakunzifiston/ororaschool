<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\DemoData;
use App\Support\DemoData\LearnerProgress;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LearnerProfilePage
{
    public static function data(): array
    {
        $authenticated = auth()->user();
        $user = DemoData::currentUser('learner');

        if ($authenticated) {
            $user['name'] = $authenticated->name;
            $user['email'] = $authenticated->email;
            $user['district'] = $authenticated->district;
        }

        $platformSlugs = array_values(array_unique(array_column(LearnerProgress::enrolments(), 'platform_slug')));

        if ($platformSlugs === []) {
            $platformSlugs = $user['platforms'];
        }

        $platforms = array_values(array_filter(array_map(
            fn (string $slug) => Platforms::find($slug),
            $platformSlugs,
        )));

        return [
            'header' => LearnerHeader::make(
                'Profile',
                'Your details, and the learning history that follows you across every academy.',
                [
                    ['label' => 'Profile'],
                ],
            ),
            'user' => $user,
            'platforms' => $platforms,
            'history' => LearnerProgress::history(),
        ];
    }
}
