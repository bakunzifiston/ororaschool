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
    public static function data(): array
    {
        $user = DemoData::currentUser('learner');
        $inProgress = LearnerProgress::inProgress();
        $completed = LearnerProgress::completed();
        $continue = LearnerProgress::continueLesson();
        $certificates = IssuedCertificates::forLearner(LearnerProgress::LEARNER_ID);
        $platforms = array_values(array_unique(array_column(array_merge($inProgress, $completed), 'platform_name')));

        return [
            'user' => $user,
            'header' => LearnerHeader::make(
                'Welcome back, '.$user['name'],
                'One learning record — '.implode(', ', $platforms).' together. Nothing here is a separate account.',
            ),
            'stats' => [
                ['label' => 'Courses in progress', 'value' => (string) count($inProgress), 'trend' => null, 'direction' => null, 'note' => count($platforms).' platforms'],
                ['label' => 'Certificates earned', 'value' => (string) count($certificates), 'trend' => $certificates !== [] ? 'Latest '.$certificates[0]['issued'] : null, 'direction' => null, 'note' => null],
            ],
            'continue' => $continue,
            'inProgress' => $inProgress,
            'completed' => $completed,
            'certificatesCount' => count($certificates),
        ];
    }
}
