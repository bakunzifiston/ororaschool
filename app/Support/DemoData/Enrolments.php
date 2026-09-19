<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Per-learner course progress on a platform (the workspace learner detail).
 */
class Enrolments
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function forLearnerOnPlatform(int $learnerId, string $platform): array
    {
        $rows = [];

        foreach (self::all() as $row) {
            if ($row['learner_id'] === $learnerId && $row['platform'] === $platform) {
                $course = Courses::find($row['course_slug']);
                $rows[] = array_merge($row, [
                    'course' => $course['title'] ?? $row['course_slug'],
                    'instructor' => $course['instructor'] ?? '',
                ]);
            }
        }

        if ($rows !== []) {
            return $rows;
        }

        $person = People::find($learnerId);
        $published = array_values(array_filter(
            Courses::forPlatform($platform),
            fn (array $course) => $course['status'] === 'published',
        ));

        if (! $person || $published === []) {
            return [];
        }

        $take = min(max(1, (int) ($person['enrolled'] ?? 1)), count($published));

        for ($i = 0; $i < $take; $i++) {
            $course = $published[$i];
            $progress = (int) ($person['progress'] ?? 40);
            $rows[] = [
                'learner_id' => $learnerId,
                'platform' => $platform,
                'course_slug' => $course['slug'],
                'course' => $course['title'],
                'instructor' => $course['instructor'],
                'status' => $progress >= 100 ? 'completed' : 'active',
                'progress' => $i === 0 ? $progress : max(8, $progress - (15 * $i)),
                'lessons_done' => (int) round((($i === 0 ? $progress : max(8, $progress - (15 * $i))) / 100) * $course['lessons']),
            ];
        }

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            ['learner_id' => 101, 'platform' => 'gemura', 'course_slug' => 'mastitis-milk-hygiene', 'status' => 'active', 'progress' => 72, 'lessons_done' => 4],
            ['learner_id' => 101, 'platform' => 'gemura', 'course_slug' => 'cold-chain-collection-centres', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 12],
            ['learner_id' => 101, 'platform' => 'buchapro', 'course_slug' => 'movement-permits-transport', 'status' => 'active', 'progress' => 10, 'lessons_done' => 1],
            ['learner_id' => 101, 'platform' => 'buchapro', 'course_slug' => 'animal-identification-eartags', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 14],
            ['learner_id' => 101, 'platform' => 'feedgrid', 'course_slug' => 'aflatoxin-control-maize-bran', 'status' => 'active', 'progress' => 9, 'lessons_done' => 1],
            ['learner_id' => 103, 'platform' => 'gemura', 'course_slug' => 'mastitis-milk-hygiene', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 18],
            ['learner_id' => 103, 'platform' => 'gemura', 'course_slug' => 'heat-detection-ai-timing', 'status' => 'active', 'progress' => 12, 'lessons_done' => 2],
            ['learner_id' => 106, 'platform' => 'buchapro', 'course_slug' => 'animal-identification-eartags', 'status' => 'active', 'progress' => 31, 'lessons_done' => 4],
            ['learner_id' => 106, 'platform' => 'buchapro', 'course_slug' => 'movement-permits-transport', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 16],
            ['learner_id' => 114, 'platform' => 'buchapro', 'course_slug' => 'kraal-register-reconciliation', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 8],
            ['learner_id' => 114, 'platform' => 'buchapro', 'course_slug' => 'animal-identification-eartags', 'status' => 'active', 'progress' => 55, 'lessons_done' => 8],
            ['learner_id' => 115, 'platform' => 'buchapro', 'course_slug' => 'animal-identification-eartags', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 14],
            ['learner_id' => 115, 'platform' => 'buchapro', 'course_slug' => 'kraal-register-reconciliation', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 8],
            ['learner_id' => 115, 'platform' => 'buchapro', 'course_slug' => 'movement-permits-transport', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 16],
            ['learner_id' => 109, 'platform' => 'gemura', 'course_slug' => 'mastitis-milk-hygiene', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 18],
            ['learner_id' => 109, 'platform' => 'gemura', 'course_slug' => 'evening-intake-lactometer', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 9],
        ];
    }
}
