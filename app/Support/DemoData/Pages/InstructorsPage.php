<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Courses;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class InstructorsPage
{
    public static function index(string $platform, bool $empty = false): array
    {
        $current = Platforms::find($platform) ?? Platforms::find('gemura');
        $rows = $empty ? [] : array_map(function (array $person) use ($platform) {
            $taught = array_values(array_filter(
                Courses::forPlatform($platform),
                fn (array $course) => $course['instructor'] === $person['name'],
            ));

            return array_merge($person, [
                'courses_count' => count($taught),
                'courses' => array_column($taught, 'title'),
            ]);
        }, People::instructorsOn($platform));

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Instructors',
                'People who teach on '.$current['name'].'. A person can hold a different role on another platform.',
            ),
            'rows' => $rows,
            'emptyTitle' => 'No instructors on '.$current['name'].' yet',
            'emptyMessage' => 'Assign an instructor role, or attach a person as the teacher of a course, and they appear here.',
        ];
    }

    public static function show(string $platform, int $id): ?array
    {
        $current = Platforms::find($platform);
        $person = People::find($id);
        $onPlatform = false;

        foreach (People::instructorsOn($platform) as $instructor) {
            if ($instructor['id'] === $id) {
                $onPlatform = true;
                break;
            }
        }

        if (! $person || ! $onPlatform) {
            return null;
        }

        $taught = array_values(array_filter(
            Courses::forPlatform($platform),
            fn (array $course) => $course['instructor'] === $person['name'],
        ));

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                $person['name'],
                ($person['email'] ?? '').' · '.$person['district'],
                [
                    ['label' => 'Instructors', 'route' => 'workspace.instructors', 'params' => ['platform' => $platform]],
                    ['label' => $person['name']],
                ],
            ),
            'person' => $person,
            'role' => $person['roles'][$platform] ?? 'instructor',
            'courses' => $taught,
        ];
    }
}
