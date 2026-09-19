<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Ordered course sequences. A path may live on one platform or stitch courses
 * from several — that distinction is visible, not a separate enrolment.
 */
class LearningPaths
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        return array_map([self::class, 'hydrate'], self::records());
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $path) {
            if ($path['slug'] === $slug) {
                return $path;
            }
        }

        return null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function records(): array
    {
        return [
            [
                'slug' => 'milk-hygiene-track',
                'title' => 'Milk hygiene for collection-centre work',
                'summary' => 'Mastitis scoring, then the cold chain the tanker will actually accept. All on Gemura.',
                'courses' => ['mastitis-milk-hygiene', 'cold-chain-collection-centres', 'evening-intake-lactometer'],
            ],
            [
                'slug' => 'kraal-to-ration',
                'title' => 'From kraal to ration',
                'summary' => 'Identify the animal, keep the milk clean, then keep the feed from going mouldy. Three platforms, one record.',
                'courses' => ['animal-identification-eartags', 'mastitis-milk-hygiene', 'aflatoxin-control-maize-bran'],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $path
     * @return array<string, mixed>
     */
    private static function hydrate(array $path): array
    {
        $courses = [];
        $platforms = [];
        $progressValues = [];

        foreach ($path['courses'] as $slug) {
            $course = Courses::find($slug);
            $enrolment = LearnerProgress::findEnrolment($slug);
            $platform = Platforms::find($course['platform'] ?? '') ?? [];

            if (($platform['name'] ?? '') !== '') {
                $platforms[$platform['slug']] = $platform['name'];
            }

            $state = 'not_started';
            $progress = 0;

            if ($enrolment) {
                $state = $enrolment['status'] === 'completed' ? 'completed' : 'in_progress';
                $progress = (int) $enrolment['progress'];
            }

            $progressValues[] = $progress;
            $courses[] = [
                'course' => $course,
                'enrolment' => $enrolment,
                'platform_name' => $platform['name'] ?? '',
                'state' => $state,
                'progress' => $progress,
            ];
        }

        $count = count($progressValues) ?: 1;

        return array_merge($path, [
            'items' => $courses,
            'platforms' => array_values($platforms),
            'platform_count' => count($platforms),
            'cross_platform' => count($platforms) > 1,
            'progress' => (int) round(array_sum($progressValues) / $count),
        ]);
    }
}
