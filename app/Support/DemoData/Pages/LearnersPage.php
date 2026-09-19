<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Enrolments;
use App\Support\DemoData\Paging;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LearnersPage
{
    public static function index(string $platform, bool $empty = false, int $page = 1): array
    {
        $current = Platforms::find($platform) ?? Platforms::find('gemura');
        $rows = $empty ? [] : People::learnersOn($platform);
        $paged = Paging::paginate($rows, $page, 10, '/workspace/'.$platform.'/learners', array_filter(['empty' => $empty ? 1 : null]));

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Learners',
                $current['name'].' roster — enrolment counts and progress on this platform only.',
            ),
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'No learners on '.$current['name'].' yet',
            'emptyMessage' => 'Enrol a cohort onto a '.$current['name'].' course and they land here with their progress.',
        ];
    }

    public static function show(string $platform, int $id): ?array
    {
        $current = Platforms::find($platform);
        $person = People::find($id);

        if (! $person || ($person['kind'] ?? '') !== 'learner' || ! array_key_exists($platform, $person['roles'] ?? [])) {
            return null;
        }

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                $person['name'],
                ($person['email'] ?? '').' · '.($person['cohort'] ?? $person['district']),
                [
                    ['label' => 'Learners', 'route' => 'workspace.learners', 'params' => ['platform' => $platform]],
                    ['label' => $person['name']],
                ],
            ),
            'person' => $person,
            'enrolments' => Enrolments::forLearnerOnPlatform($id, $platform),
        ];
    }
}
