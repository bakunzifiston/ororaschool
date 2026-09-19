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
        $user = DemoData::currentUser('learner');
        $platforms = array_map(
            fn (string $slug) => Platforms::find($slug),
            $user['platforms'],
        );

        return [
            'header' => LearnerHeader::make(
                'Profile',
                'Your details, and the learning history that follows you across every platform.',
                [
                    ['label' => 'Profile'],
                ],
            ),
            'user' => $user,
            'platforms' => array_values(array_filter($platforms)),
            'history' => LearnerProgress::history(),
        ];
    }
}
