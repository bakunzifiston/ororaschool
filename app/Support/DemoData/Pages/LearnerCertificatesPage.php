<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\IssuedCertificates;
use App\Support\DemoData\LearnerProgress;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LearnerCertificatesPage
{
    public static function index(bool $empty = false): array
    {
        $rows = $empty ? [] : array_map(function (array $row) {
            $platform = Platforms::find($row['platform']);

            return array_merge($row, [
                'platform_name' => $platform['name'] ?? $row['platform'],
            ]);
        }, IssuedCertificates::forLearner(LearnerProgress::LEARNER_ID));

        return [
            'header' => LearnerHeader::make(
                'Certificates',
                'Numbers minted against your record, from every platform you have finished on.',
                [
                    ['label' => 'Certificates'],
                ],
            ),
            'rows' => $rows,
            'emptyTitle' => 'No certificates yet',
            'emptyMessage' => 'Finish an eligible course and a number is minted here the same day.',
        ];
    }
}
