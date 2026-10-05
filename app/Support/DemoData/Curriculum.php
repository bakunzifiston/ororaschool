<?php

namespace App\Support\DemoData;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Support\Str;

/**
 * Course syllabus. Reads modules and lessons from MySQL. The catalogue()
 * fixture remains the seed source until authors write curriculum in the app.
 */
class Curriculum
{
    /**
     * Seed payload for one course: authored rows, or a generated fallback.
     *
     * @return list<array<string, mixed>>
     */
    public static function fixtureModulesFor(string $courseSlug): array
    {
        return self::catalogue()[$courseSlug] ?? self::fallback($courseSlug);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function modulesFor(string $courseSlug): array
    {
        $course = self::courseWithSyllabus($courseSlug);

        if (! $course) {
            return [];
        }

        return $course->curriculumModules
            ->map(fn (Module $module) => $module->toSyllabusArray())
            ->all();
    }

    /**
     * @return array{slug: string, title: string}|null
     */
    public static function addModule(string $platform, string $courseSlug, string $title): ?array
    {
        $course = self::courseOnPlatform($platform, $courseSlug);

        if ($course === null) {
            return null;
        }

        $slug = self::uniqueSlug(
            $course->curriculumModules()->pluck('slug')->all(),
            Str::slug($title) ?: 'module',
        );

        $module = $course->curriculumModules()->create([
            'slug' => $slug,
            'title' => $title,
            'sort_order' => ((int) $course->curriculumModules()->max('sort_order')) + 1,
        ]);

        return ['slug' => $module->slug, 'title' => $module->title];
    }

    /**
     * @return array{slug: string, title: string}|null
     */
    public static function addLesson(string $platform, string $courseSlug, string $moduleSlug, string $title, string $type): ?array
    {
        $course = self::courseOnPlatform($platform, $courseSlug);

        if ($course === null) {
            return null;
        }

        $module = $course->curriculumModules()->where('slug', $moduleSlug)->first();

        if ($module === null) {
            return null;
        }

        $slug = self::uniqueSlug(
            $course->curriculumLessons()->pluck('slug')->all(),
            Str::slug($title) ?: 'lesson',
        );

        $lesson = $module->lessons()->create([
            'course_id' => $course->id,
            'slug' => $slug,
            'title' => $title,
            'type' => $type,
            'duration' => 8,
            'body' => self::body($slug),
            'is_preview' => false,
            'quiz_slug' => null,
            'sort_order' => ((int) $module->lessons()->max('sort_order')) + 1,
        ]);

        return ['slug' => $lesson->slug, 'title' => $lesson->title];
    }

    /**
     * Flat lesson list for a platform (Lessons nav item).
     *
     * @return list<array<string, mixed>>
     */
    public static function lessonsOn(string $platform): array
    {
        $courses = Course::query()
            ->whereHas('platform', fn ($query) => $query->where('slug', $platform))
            ->with(['curriculumModules.lessons'])
            ->orderBy('id')
            ->get();

        $lessons = [];

        foreach ($courses as $course) {
            foreach ($course->curriculumModules as $module) {
                foreach ($module->lessons as $lesson) {
                    $lessons[] = array_merge($lesson->toSyllabusArray(), [
                        'course' => $course->title,
                        'course_slug' => $course->slug,
                        'module' => $module->title,
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
        $course = self::courseWithSyllabus($courseSlug);

        if (! $course) {
            return [];
        }

        $lessons = [];

        foreach ($course->curriculumModules as $module) {
            foreach ($module->lessons as $lesson) {
                $lesson->setRelation('course', $course);
                $lesson->setRelation('module', $module);
                $lessons[] = $lesson->toPageArray();
            }
        }

        return $lessons;
    }

    private static function courseWithSyllabus(string $courseSlug): ?Course
    {
        return Course::query()
            ->where('slug', $courseSlug)
            ->with(['curriculumModules.lessons'])
            ->first();
    }

    private static function courseOnPlatform(string $platform, string $courseSlug): ?Course
    {
        return Course::query()
            ->where('slug', $courseSlug)
            ->whereHas('platform', fn ($query) => $query->where('slug', $platform))
            ->first();
    }

    /**
     * @param  list<string>  $used
     */
    private static function uniqueSlug(array $used, string $base): string
    {
        $index = array_fill_keys($used, true);
        $slug = $base;
        $suffix = 2;

        while (isset($index[$slug])) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
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
                        ['id' => 'l-cmt-1', 'title' => 'Reading a CMT paddle', 'type' => 'video', 'duration' => 12, 'is_preview' => true],
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
                        ['id' => 'l-tag-1', 'title' => 'Placing an ear tag without tearing', 'type' => 'video', 'duration' => 11, 'is_preview' => true],
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
                    ['id' => 'l-'.$courseSlug.'-'.$i.'-a', 'title' => 'Introduction', 'type' => 'video', 'duration' => 8, 'is_preview' => $i === 1 && in_array($courseSlug, ['farm-record-keeping', 'evening-intake-lactometer'], true)],
                    ['id' => 'l-'.$courseSlug.'-'.$i.'-b', 'title' => 'Field notes', 'type' => 'text', 'duration' => 6],
                ],
            ];
        }

        return $modules;
    }
}
