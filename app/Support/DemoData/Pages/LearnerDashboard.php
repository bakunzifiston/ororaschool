<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\DemoData;
use App\Support\DemoData\IssuedCertificates;
use App\Support\DemoData\LearnerProgress;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LearnerDashboard
{
    public static function data(bool $empty = false): array
    {
        $user = DemoData::currentUser('learner');
        $inProgress = $empty ? [] : LearnerProgress::inProgress();
        $completed = $empty ? [] : LearnerProgress::completed();
        $continue = $empty ? null : LearnerProgress::continueLesson();
        $certificates = $empty ? [] : IssuedCertificates::forLearner(LearnerProgress::LEARNER_ID);
        $platforms = array_values(array_unique(array_column(array_merge($inProgress, $completed), 'platform_name')));

        return [
            'user' => $user,
            'header' => LearnerHeader::make(
                'Welcome back, '.$user['name'],
                $platforms === []
                    ? 'One learning record across every platform you train on. Nothing here is a separate account.'
                    : 'One learning record — '.implode(', ', $platforms).' together. Nothing here is a separate account.',
            ),
            'continue' => $continue,
            'inProgress' => $inProgress,
            'completed' => $completed,
            'certificatesCount' => count($certificates),
        ];
    }
}
