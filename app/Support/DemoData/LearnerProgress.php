<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Placide's learning record — one identity, courses on Gemura, BuchaPro and
 * FeedGrid. This is the Fiston pattern from the architecture: the record is
 * central; platforms are labels on courses, not separate accounts.
 */
class LearnerProgress
{
    public const LEARNER_ID = 101;

    /**
     * @return list<array<string, mixed>>
     */
    public static function enrolments(): array
    {
        return array_map([self::class, 'hydrate'], Courses::enrolments());
    }

    public static function findEnrolment(string $courseSlug): ?array
    {
        foreach (self::enrolments() as $enrolment) {
            if ($enrolment['course'] === $courseSlug) {
                return $enrolment;
            }
        }

        return null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function inProgress(): array
    {
        return array_values(array_filter(
            self::enrolments(),
            fn (array $row) => $row['status'] === 'active',
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function completed(): array
    {
        return array_values(array_filter(
            self::enrolments(),
            fn (array $row) => $row['status'] === 'completed',
        ));
    }

    /**
     * Next incomplete lesson across every platform, soonest due first.
     *
     * @return array<string, mixed>|null
     */
    public static function continueLesson(): ?array
    {
        $active = self::inProgress();

        if ($active === []) {
            return null;
        }

        $next = $active[0];
        $lessons = Curriculum::lessonsFor($next['course']);
        $index = min($next['lessons_done'], max(0, count($lessons) - 1));
        $lesson = $lessons[$index] ?? null;

        if (! $lesson) {
            return $next;
        }

        return array_merge($next, [
            'lesson' => $lesson,
            'next' => $lesson['title'],
        ]);
    }

    /**
     * Enrolments grouped by platform, preserving first-seen order.
     *
     * @return list<array<string, mixed>>
     */
    public static function groupedByPlatform(string $status = ''): array
    {
        $rows = self::enrolments();

        if ($status !== '') {
            $rows = array_values(array_filter($rows, fn (array $row) => $row['status'] === $status));
        }

        $groups = [];

        foreach ($rows as $row) {
            $slug = $row['platform_slug'];

            if (! isset($groups[$slug])) {
                $groups[$slug] = [
                    'slug' => $slug,
                    'name' => $row['platform_name'],
                    'discipline' => $row['platform_discipline'],
                    'courses' => [],
                ];
            }

            $groups[$slug]['courses'][] = $row;
        }

        return array_values($groups);
    }

    /**
     * Syllabus overlay: each lesson is completed, in_progress, or locked.
     *
     * @return array<string, mixed>|null
     */
    public static function syllabus(string $courseSlug): ?array
    {
        $course = Courses::find($courseSlug);

        if (! $course || $course['status'] === 'draft') {
            return null;
        }

        $enrolment = self::findEnrolment($courseSlug);
        $enrolled = (bool) $enrolment;
        $requiresEnrolment = (bool) ($course['enrollment_required'] ?? true);
        $lessons = Curriculum::lessonsFor($courseSlug);
        $done = $enrolled ? (int) $enrolment['lessons_done'] : 0;

        if ($enrolled && ($enrolment['status'] ?? '') === 'completed') {
            $done = count($lessons);
        }

        $modules = [];

        foreach (Curriculum::modulesFor($courseSlug) as $module) {
            $items = [];

            foreach ($module['lessons'] as $lesson) {
                $flatIndex = self::indexOf($lessons, $lesson['id']);
                $state = self::lessonState($flatIndex, $done, $enrolled, $requiresEnrolment);

                $items[] = array_merge($lesson, [
                    'state' => $state,
                    'quiz' => Curriculum::quizFor($lesson['id']),
                ]);
            }

            $modules[] = array_merge($module, ['lessons' => $items]);
        }

        $current = null;

        foreach ($lessons as $index => $lesson) {
            if (self::lessonState($index, $done, $enrolled, $requiresEnrolment) === 'in_progress') {
                $current = $lesson;
                break;
            }
        }

        if ($current === null && $enrolled && $lessons !== []) {
            $current = $lessons[min($done, count($lessons) - 1)];
        }

        if ($current === null && ! $requiresEnrolment && $lessons !== []) {
            $current = $lessons[0];
        }

        $platform = Platforms::find($course['platform']) ?? [];

        return [
            'course' => $course,
            'platform' => $platform,
            'enrolment' => $enrolment,
            'enrolled' => $enrolled,
            'requires_enrolment' => $requiresEnrolment,
            'modules' => $modules,
            'current' => $current,
            'progress' => $enrolment['progress'] ?? 0,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function lessonPage(string $courseSlug, string $lessonId): ?array
    {
        $syllabus = self::syllabus($courseSlug);

        if (! $syllabus) {
            return null;
        }

        $lessons = Curriculum::lessonsFor($courseSlug);
        $current = Curriculum::findLesson($courseSlug, $lessonId);

        if (! $current) {
            return null;
        }

        $done = $syllabus['enrolled'] ? (int) ($syllabus['enrolment']['lessons_done'] ?? 0) : 0;

        if ($syllabus['enrolled'] && ($syllabus['enrolment']['status'] ?? '') === 'completed') {
            $done = count($lessons);
        }

        $state = self::lessonState(
            $current['index'],
            $done,
            $syllabus['enrolled'],
            $syllabus['requires_enrolment'],
        );

        if ($state === 'locked') {
            return null;
        }

        $prev = $lessons[$current['index'] - 1] ?? null;
        $next = $lessons[$current['index'] + 1] ?? null;

        if ($next && self::lessonState($current['index'] + 1, $done, $syllabus['enrolled'], $syllabus['requires_enrolment']) === 'locked') {
            $next = null;
        }

        return [
            'syllabus' => $syllabus,
            'lesson' => array_merge($current, ['state' => $state]),
            'prev' => $prev,
            'next' => $next,
            'session' => self::sessionForLesson($courseSlug, $current),
        ];
    }

    /**
     * Published courses the learner can still open, for Enroll vs Start.
     *
     * @return list<array<string, mixed>>
     */
    public static function available(): array
    {
        $taken = array_column(Courses::enrolments(), 'course');
        $rows = [];

        foreach (['evening-intake-lactometer', 'kraal-register-reconciliation'] as $slug) {
            $course = Courses::find($slug);

            if (! $course || in_array($slug, $taken, true)) {
                continue;
            }

            $platform = Platforms::find($course['platform']) ?? [];
            $rows[] = [
                'course' => $course,
                'platform_name' => $platform['name'] ?? '',
                'platform_slug' => $course['platform'],
            ];
        }

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function sessions(): array
    {
        $slugs = array_column(Courses::enrolments(), 'course');

        return array_values(array_filter(
            LiveSessions::all(),
            fn (array $session) => in_array($session['course_slug'], $slugs, true),
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function resources(): array
    {
        $enrolments = self::enrolments();
        $platforms = array_unique(array_column($enrolments, 'platform_slug'));
        $needles = [];

        foreach ($enrolments as $enrolment) {
            $needles[] = strtolower($enrolment['course_data']['title'] ?? '');

            foreach (Curriculum::lessonsFor($enrolment['course']) as $lesson) {
                $needles[] = strtolower($lesson['title']);
            }
        }

        $needles = array_values(array_filter($needles));

        return array_values(array_filter(
            Resources::all(),
            function (array $resource) use ($platforms, $needles) {
                if (! in_array($resource['platform'], $platforms, true)) {
                    return false;
                }

                if ($resource['attached_kind'] === 'academy') {
                    return true;
                }

                $haystack = strtolower($resource['attached_to']);

                foreach ($needles as $needle) {
                    if ($needle !== '' && str_contains($haystack, $needle)) {
                        return true;
                    }
                }

                return false;
            },
        ));
    }

    /**
     * Platform-agnostic timeline. Newest first.
     *
     * @return list<array<string, mixed>>
     */
    public static function history(): array
    {
        return [
            ['at' => '14 Sep 2026', 'platform' => 'Gemura', 'title' => 'Picked up Mastitis Detection again', 'detail' => 'Finished fore-stripping. Next: the audio of a clean milking sequence.'],
            ['at' => '08 Sep 2026', 'platform' => 'BuchaPro', 'title' => 'Started Movement Permits and Livestock Transport Records', 'detail' => 'Two lessons in. The roadblock check is the one that still fails in the field.'],
            ['at' => '02 Sep 2026', 'platform' => 'Gemura', 'title' => 'Certificate OS-GEM-2026-1841 issued', 'detail' => 'Cold Chain Discipline at Milk Collection Centres.'],
            ['at' => '28 Aug 2026', 'platform' => 'FeedGrid', 'title' => 'Opened Aflatoxin Control in Maize Bran Storage', 'detail' => 'First lesson only — moisture is the whole story.'],
            ['at' => '14 Aug 2026', 'platform' => 'BuchaPro', 'title' => 'Certificate OS-BCH-2026-0498 issued', 'detail' => 'Animal Identification and Ear-Tag Registration.'],
            ['at' => '04 May 2024', 'platform' => 'Orora School', 'title' => 'Learning record opened', 'detail' => 'One record. Courses from every platform sit on it.'],
        ];
    }

    /**
     * @param  array<string, mixed>  $enrolment
     * @return array<string, mixed>
     */
    private static function hydrate(array $enrolment): array
    {
        $course = Courses::find($enrolment['course']) ?? [];
        $platform = Platforms::find($course['platform'] ?? '') ?? [];
        $lessons = Curriculum::lessonsFor($enrolment['course']);
        $index = min((int) $enrolment['lessons_done'], max(0, count($lessons) - 1));

        return $enrolment + [
            'course_data' => $course,
            'platform_name' => $platform['name'] ?? '',
            'platform_slug' => $course['platform'] ?? '',
            'platform_discipline' => $platform['discipline'] ?? '',
            'lesson' => $lessons[$index] ?? null,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $lessons
     */
    private static function indexOf(array $lessons, string $id): int
    {
        foreach ($lessons as $index => $lesson) {
            if ($lesson['id'] === $id) {
                return $index;
            }
        }

        return 0;
    }

    private static function lessonState(int $index, int $done, bool $enrolled, bool $requiresEnrolment): string
    {
        if (! $enrolled) {
            if (! $requiresEnrolment && $index === 0) {
                return 'in_progress';
            }

            return 'locked';
        }

        if ($index < $done) {
            return 'completed';
        }

        if ($index === $done) {
            return 'in_progress';
        }

        return 'locked';
    }

    /**
     * @param  array<string, mixed>  $lesson
     * @return array<string, mixed>|null
     */
    private static function sessionForLesson(string $courseSlug, array $lesson): ?array
    {
        if (($lesson['type'] ?? '') !== 'live_session') {
            return null;
        }

        foreach (LiveSessions::all() as $session) {
            if ($session['course_slug'] === $courseSlug) {
                return $session;
            }
        }

        return null;
    }
}
