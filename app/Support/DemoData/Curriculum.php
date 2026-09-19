<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Modules with nested lessons. Content types: video, text, pdf, audio,
 * external, live_session.
 */
class Curriculum
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function modulesFor(string $courseSlug): array
    {
        return self::catalogue()[$courseSlug] ?? self::fallback($courseSlug);
    }

    /**
     * Flat lesson list for a platform (Lessons nav item).
     *
     * @return list<array<string, mixed>>
     */
    public static function lessonsOn(string $platform): array
    {
        $lessons = [];

        foreach (Courses::forPlatform($platform) as $course) {
            foreach (self::modulesFor($course['slug']) as $module) {
                foreach ($module['lessons'] as $lesson) {
                    $lessons[] = array_merge($lesson, [
                        'course' => $course['title'],
                        'course_slug' => $course['slug'],
                        'module' => $module['title'],
                    ]);
                }
            }
        }

        return $lessons;
    }

    /**
     * Flattened lessons for one course, in module order.
     *
     * @return list<array<string, mixed>>
     */
    public static function lessonsFor(string $courseSlug): array
    {
        $lessons = [];

        foreach (self::modulesFor($courseSlug) as $module) {
            foreach ($module['lessons'] as $lesson) {
                $lessons[] = array_merge($lesson, [
                    'course_slug' => $courseSlug,
                    'module' => $module['title'],
                    'module_id' => $module['id'],
                    'body' => self::body($lesson['id']),
                    'quiz' => self::quizFor($lesson['id']),
                ]);
            }
        }

        return $lessons;
    }

    public static function findLesson(string $courseSlug, string $lessonId): ?array
    {
        foreach (self::lessonsFor($courseSlug) as $index => $lesson) {
            if ($lesson['id'] === $lessonId) {
                return array_merge($lesson, ['index' => $index]);
            }
        }

        return null;
    }

    public static function body(string $lessonId): string
    {
        return match ($lessonId) {
            'l-cmt-1' => 'Hold the paddle level. Strip four streams from each quarter into its cup, then add an equal volume of reagent. Tilt and swirl for ten seconds before you score.',
            'l-cmt-2' => 'Trace is a slight slime that disappears as you swirl. Weak positive stays as a slime. Strong positive gels and pulls away from the cup wall. A strong positive is a rejection, not a second opinion.',
            'l-cmt-3' => 'Print the field sheet and fill one row per cow, not per kraal. The collection centre will not accept a sheet that names the farmer but not the quarters.',
            'l-milk-1' => 'Fore-strip onto the paddle or the ground — never into the bulk. The first streams carry the highest cell count and the dirt from the teat canal.',
            'l-milk-2' => 'Listen for the sequence: strip, dip, wipe, attach. If the cluster goes on before the wipe, the routine has already failed.',
            'l-milk-3' => 'The MINAGRI note sets the rejection band for collection centres. Open it, read the temperature and lactometer limits, then come back to the course.',
            'l-milk-4' => 'Bring a paddle and a bottle of reagent. The clinic scores live samples from Kinigi so the slime is not a photograph.',
            'l-tag-1' => 'The tag sits in the middle third of the ear, between the ridges, so it cannot tear out on a thorn. The pliers close once. A second squeeze splits the cartilage.',
            'l-tag-2' => 'Each column on the herd register is a promise to the district officer: number, sex, colour, dam, date. An empty cell is a discrepancy waiting to be written up.',
            default => 'Read the notes, then mark the lesson complete when you can do the step at the kraal without looking back.',
        };
    }

    public static function quizFor(string $lessonId): ?string
    {
        return match ($lessonId) {
            'l-cmt-2' => 'cmt-paddle-reading',
            'l-tag-1' => 'ear-tag-placement',
            default => null,
        };
    }

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    private static function catalogue(): array
    {
        return [
            'mastitis-milk-hygiene' => [
                [
                    'id' => 'm-hygiene-1', 'title' => 'Why somatic cell counts move', 'order' => 1,
                    'lessons' => [
                        ['id' => 'l-cmt-1', 'title' => 'Reading a CMT paddle', 'type' => 'video', 'duration' => 12],
                        ['id' => 'l-cmt-2', 'title' => 'Scoring trace, weak positive and strong positive', 'type' => 'text', 'duration' => 8],
                        ['id' => 'l-cmt-3', 'title' => 'CMT field sheet', 'type' => 'pdf', 'duration' => 4],
                    ],
                ],
                [
                    'id' => 'm-hygiene-2', 'title' => 'The milking routine', 'order' => 2,
                    'lessons' => [
                        ['id' => 'l-milk-1', 'title' => 'Fore-stripping at the kraal', 'type' => 'video', 'duration' => 9],
                        ['id' => 'l-milk-2', 'title' => 'Audio: a clean milking sequence', 'type' => 'audio', 'duration' => 6],
                        ['id' => 'l-milk-3', 'title' => 'MINAGRI milk hygiene note', 'type' => 'external', 'duration' => 5],
                    ],
                ],
                [
                    'id' => 'm-hygiene-3', 'title' => 'Clinic: paddles together', 'order' => 3,
                    'lessons' => [
                        ['id' => 'l-milk-4', 'title' => 'Reading CMT paddles together', 'type' => 'live_session', 'duration' => 45],
                    ],
                ],
            ],
            'animal-identification-eartags' => [
                [
                    'id' => 'm-tag-1', 'title' => 'The tag and the pliers', 'order' => 1,
                    'lessons' => [
                        ['id' => 'l-tag-1', 'title' => 'Placing an ear tag without tearing', 'type' => 'video', 'duration' => 11],
                        ['id' => 'l-tag-2', 'title' => 'The herd register columns', 'type' => 'text', 'duration' => 7],
                        ['id' => 'l-tag-3', 'title' => 'Ear-tag application checklist', 'type' => 'pdf', 'duration' => 3],
                    ],
                ],
                [
                    'id' => 'm-tag-2', 'title' => 'When a tag is lost', 'order' => 2,
                    'lessons' => [
                        ['id' => 'l-tag-4', 'title' => 'Replacement protocol at the kraal', 'type' => 'video', 'duration' => 8],
                        ['id' => 'l-tag-5', 'title' => 'District officer call-in (audio)', 'type' => 'audio', 'duration' => 5],
                        ['id' => 'l-tag-6', 'title' => 'RAB identification circular', 'type' => 'external', 'duration' => 6],
                    ],
                ],
                [
                    'id' => 'm-tag-3', 'title' => 'Clinic: tagging a batch', 'order' => 3,
                    'lessons' => [
                        ['id' => 'l-tag-7', 'title' => 'Tagging a batch at Rubengera', 'type' => 'live_session', 'duration' => 50],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function fallback(string $courseSlug): array
    {
        $course = Courses::find($courseSlug);
        $count = max(2, (int) ($course['modules'] ?? 2));
        $modules = [];

        for ($i = 1; $i <= $count; $i++) {
            $modules[] = [
                'id' => 'm-'.$courseSlug.'-'.$i,
                'title' => 'Module '.$i,
                'order' => $i,
                'lessons' => [
                    ['id' => 'l-'.$courseSlug.'-'.$i.'-a', 'title' => 'Introduction', 'type' => 'video', 'duration' => 8],
                    ['id' => 'l-'.$courseSlug.'-'.$i.'-b', 'title' => 'Field notes', 'type' => 'text', 'duration' => 6],
                ],
            ];
        }

        return $modules;
    }
}
