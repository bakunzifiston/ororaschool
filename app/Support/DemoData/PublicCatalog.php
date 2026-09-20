<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * The public catalogue query. Every public page reads from here, never from
 * Courses::all() directly, so unpublished courses and courses on inactive
 * platforms cannot leak into a view by accident.
 */
class PublicCatalog
{
    public const PER_PAGE = 6;

    /**
     * Published courses on an active platform. This is the only set the
     * public site is allowed to display.
     *
     * @return list<array<string, mixed>>
     */
    public static function publishedCourses(): array
    {
        $active = array_column(Platforms::active(), 'slug');

        return array_values(array_filter(
            Courses::all(),
            fn (array $course) => $course['status'] === 'published'
                && in_array($course['platform'], $active, true),
        ));
    }

    /**
     * @param  array<string, mixed>  $course
     * @return array<string, mixed>
     */
    public static function present(array $course): array
    {
        $platform = Platforms::find($course['platform']) ?? [];

        return array_merge($course, [
            'platform_name' => $platform['name'] ?? '',
            'platform_discipline' => $platform['discipline'] ?? '',
            'platform_tagline' => $platform['tagline'] ?? '',
            'href' => route('catalog.courses.show', ['course' => $course['slug']]),
        ]);
    }

    /**
     * @param  array<string, string>  $filters
     * @return list<array<string, mixed>>
     */
    public static function filter(array $filters = []): array
    {
        $rows = self::publishedCourses();

        $platform = (string) ($filters['platform'] ?? '');
        $academy = (string) ($filters['academy'] ?? '');
        $difficulty = (string) ($filters['difficulty'] ?? '');
        $language = (string) ($filters['language'] ?? '');
        $price = (string) ($filters['price'] ?? '');
        $search = mb_strtolower(trim((string) ($filters['q'] ?? '')));

        if ($platform !== '') {
            $rows = array_values(array_filter($rows, fn (array $course) => $course['platform'] === $platform));
        }

        if ($academy !== '') {
            $rows = array_values(array_filter($rows, fn (array $course) => $course['academy_slug'] === $academy));
        }

        if ($difficulty !== '') {
            $rows = array_values(array_filter($rows, fn (array $course) => $course['difficulty'] === $difficulty));
        }

        if ($language !== '') {
            $rows = array_values(array_filter(
                $rows,
                fn (array $course) => str_contains((string) $course['language'], $language),
            ));
        }

        if ($price === 'free') {
            $rows = array_values(array_filter($rows, fn (array $course) => ! $course['paid']));
        }

        if ($price === 'paid') {
            $rows = array_values(array_filter($rows, fn (array $course) => (bool) $course['paid']));
        }

        if ($search !== '') {
            $rows = array_values(array_filter($rows, function (array $course) use ($search) {
                $haystack = mb_strtolower(implode(' ', [
                    $course['title'] ?? '',
                    $course['summary'] ?? '',
                    $course['description'] ?? '',
                ]));

                return str_contains($haystack, $search);
            }));
        }

        return array_map([self::class, 'present'], $rows);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function featured(int $limit = 4): array
    {
        $rows = self::publishedCourses();
        usort($rows, fn (array $a, array $b) => ($b['enrolled'] ?? 0) <=> ($a['enrolled'] ?? 0));

        return array_map([self::class, 'present'], array_slice($rows, 0, $limit));
    }

    public static function findPublished(string $slug): ?array
    {
        foreach (self::publishedCourses() as $course) {
            if ($course['slug'] === $slug) {
                return self::present($course);
            }
        }

        return null;
    }

    /**
     * Active platforms with a published-course count the public site can trust.
     *
     * @return list<array<string, mixed>>
     */
    public static function activePlatforms(): array
    {
        $published = self::publishedCourses();

        return array_map(function (array $platform) use ($published) {
            $count = count(array_filter(
                $published,
                fn (array $course) => $course['platform'] === $platform['slug'],
            ));

            $academies = array_values(array_filter(
                Academies::forPlatform($platform['slug']),
                fn (array $academy) => $academy['status'] === 'published',
            ));

            return array_merge($platform, [
                'public_courses' => $count,
                'public_academies' => $academies,
                'href' => route('catalog.platforms.show', ['platform' => $platform['slug']]),
            ]);
        }, Platforms::active());
    }

    public static function findActivePlatform(string $slug): ?array
    {
        foreach (self::activePlatforms() as $platform) {
            if ($platform['slug'] === $slug) {
                return $platform;
            }
        }

        return null;
    }

    /**
     * @return array{courses: int, platforms: int}
     */
    public static function stats(): array
    {
        return [
            'courses' => count(self::publishedCourses()),
            'platforms' => count(Platforms::active()),
        ];
    }

    /**
     * Module and lesson titles only. No body, no media.
     *
     * @return list<array<string, mixed>>
     */
    public static function previewSyllabus(string $slug): array
    {
        $modules = [];

        foreach (Curriculum::modulesFor($slug) as $module) {
            $lessons = [];

            foreach ($module['lessons'] as $lesson) {
                $lessons[] = [
                    'title' => $lesson['title'],
                    'is_preview' => (bool) ($lesson['is_preview'] ?? false),
                ];
            }

            $modules[] = [
                'title' => $module['title'],
                'lessons' => $lessons,
            ];
        }

        return $modules;
    }

    /**
     * Filter option maps derived from the published-on-active set, so a draft
     * academy never appears as a public filter.
     *
     * @return array<string, mixed>
     */
    public static function filterOptions(?string $platform = null): array
    {
        $platform = $platform !== null && $platform !== '' ? $platform : null;
        $source = $platform
            ? array_values(array_filter(self::publishedCourses(), fn (array $course) => $course['platform'] === $platform))
            : self::publishedCourses();

        $platforms = ['' => 'All platforms'];
        foreach (self::activePlatforms() as $row) {
            $platforms[$row['slug']] = $row['name'];
        }

        $academies = ['' => 'All academies'];
        foreach ($source as $course) {
            if (($course['academy_slug'] ?? '') !== '') {
                $academies[$course['academy_slug']] = $course['academy'];
            }
        }

        $difficulties = ['' => 'Any difficulty'];
        foreach ($source as $course) {
            $difficulties[$course['difficulty']] = $course['difficulty'];
        }

        return [
            'platforms' => $platforms,
            'academies' => $academies,
            'difficulties' => $difficulties,
            'languages' => [
                '' => 'Any language',
                'English' => 'English',
                'Kinyarwanda' => 'Kinyarwanda',
            ],
            'prices' => [
                '' => 'Free or paid',
                'free' => 'Free',
                'paid' => 'Paid',
            ],
        ];
    }
}
