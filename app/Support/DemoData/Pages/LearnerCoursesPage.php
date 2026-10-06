<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\LearnerProgress;
use App\Support\DemoData\LiveSessions;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LearnerCoursesPage
{
    /**
     * @param  array{status?: string, platform?: string, category?: string}  $filters
     */
    public static function index(array $filters = [], bool $empty = false): array
    {
        $status = (string) ($filters['status'] ?? '');
        $platform = (string) ($filters['platform'] ?? '');
        $category = (string) ($filters['category'] ?? '');

        $enrolments = $empty ? [] : LearnerProgress::enrolments();
        $available = $empty ? [] : LearnerProgress::available();

        $options = self::filterOptions($enrolments, $available);

        $filteredEnrolments = self::filterEnrolments($enrolments, $status, $platform, $category);
        $filteredAvailable = self::filterAvailable($available, $platform, $category);

        $groups = self::groupByPlatform($filteredEnrolments);
        $sections = self::sections($groups);
        $filtersActive = $status !== '' || $platform !== '' || $category !== '';

        return [
            'header' => LearnerHeader::make(
                'My courses',
                'Every course you are on, grouped by academy. Same record — not three logins.',
                [
                    ['label' => 'My courses'],
                ],
            ),
            'filters' => [
                'status' => $status,
                'platform' => $platform,
                'category' => $category,
                'statuses' => [
                    '' => 'All courses',
                    'active' => 'In progress',
                    'completed' => 'Completed',
                ],
                'platforms' => $options['platforms'],
                'categories' => $options['categories'],
            ],
            'showFilters' => ! $empty && (count($enrolments) > 0 || count($available) > 0 || $filtersActive),
            'groups' => $groups,
            'sections' => $sections,
            'available' => $filteredAvailable,
            'emptyTitle' => $filtersActive ? 'No courses match this filter' : 'No courses on your record yet',
            'emptyMessage' => $filtersActive
                ? 'Try another academy or category, or clear the filters to see everything on your record.'
                : 'When a field coordinator enrols you, or you start an open course, it lands here with the others.',
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $enrolments
     * @param  list<array<string, mixed>>  $available
     * @return array{platforms: array<string, string>, categories: array<string, string>}
     */
    private static function filterOptions(array $enrolments, array $available): array
    {
        $platforms = ['' => 'All academies'];
        $categories = ['' => 'All categories'];

        foreach ($enrolments as $enrolment) {
            $slug = (string) ($enrolment['platform_slug'] ?? '');
            $name = (string) ($enrolment['platform_name'] ?? '');

            if ($slug !== '' && $name !== '') {
                $platforms[$slug] = $name;
            }

            $courseCategory = trim((string) ($enrolment['course_data']['category'] ?? ''));

            if ($courseCategory !== '') {
                $categories[$courseCategory] = $courseCategory;
            }
        }

        foreach ($available as $item) {
            $slug = (string) ($item['platform_slug'] ?? $item['course']['platform'] ?? '');
            $name = (string) ($item['platform_name'] ?? $item['course']['platform_name'] ?? '');

            if ($slug !== '' && $name !== '') {
                $platforms[$slug] = $name;
            }

            $courseCategory = trim((string) ($item['course']['category'] ?? ''));

            if ($courseCategory !== '') {
                $categories[$courseCategory] = $courseCategory;
            }
        }

        asort($platforms, SORT_NATURAL | SORT_FLAG_CASE);
        asort($categories, SORT_NATURAL | SORT_FLAG_CASE);

        return [
            'platforms' => ['' => 'All academies'] + array_diff_key($platforms, ['' => true]),
            'categories' => ['' => 'All categories'] + array_diff_key($categories, ['' => true]),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $enrolments
     * @return list<array<string, mixed>>
     */
    private static function filterEnrolments(array $enrolments, string $status, string $platform, string $category): array
    {
        return array_values(array_filter(
            $enrolments,
            function (array $enrolment) use ($status, $platform, $category): bool {
                if ($status !== '' && ($enrolment['status'] ?? '') !== $status) {
                    return false;
                }

                if ($platform !== '' && ($enrolment['platform_slug'] ?? '') !== $platform) {
                    return false;
                }

                if ($category !== '' && trim((string) ($enrolment['course_data']['category'] ?? '')) !== $category) {
                    return false;
                }

                return true;
            },
        ));
    }

    /**
     * @param  list<array<string, mixed>>  $available
     * @return list<array<string, mixed>>
     */
    private static function filterAvailable(array $available, string $platform, string $category): array
    {
        return array_values(array_filter(
            $available,
            function (array $item) use ($platform, $category): bool {
                $slug = (string) ($item['platform_slug'] ?? $item['course']['platform'] ?? '');
                $courseCategory = trim((string) ($item['course']['category'] ?? ''));

                if ($platform !== '' && $slug !== $platform) {
                    return false;
                }

                if ($category !== '' && $courseCategory !== $category) {
                    return false;
                }

                return true;
            },
        ));
    }

    /**
     * @param  list<array<string, mixed>>  $enrolments
     * @return list<array<string, mixed>>
     */
    private static function groupByPlatform(array $enrolments): array
    {
        $groups = [];

        foreach ($enrolments as $row) {
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
     * @param  list<array<string, mixed>>  $groups
     * @return list<array{key: string, title: string, courses: list<array<string, mixed>>}>
     */
    private static function sections(array $groups): array
    {
        $courses = [];

        foreach ($groups as $group) {
            foreach ($group['courses'] as $enrolment) {
                $courses[] = $enrolment;
            }
        }

        $buckets = [
            'in_progress' => ['key' => 'in_progress', 'title' => 'In progress', 'courses' => []],
            'not_started' => ['key' => 'not_started', 'title' => 'Not started', 'courses' => []],
            'completed' => ['key' => 'completed', 'title' => 'Completed', 'courses' => []],
        ];

        foreach ($courses as $enrolment) {
            if (($enrolment['status'] ?? '') === 'completed') {
                $buckets['completed']['courses'][] = $enrolment;

                continue;
            }

            if ((int) ($enrolment['progress'] ?? 0) === 0) {
                $buckets['not_started']['courses'][] = $enrolment;

                continue;
            }

            $buckets['in_progress']['courses'][] = $enrolment;
        }

        return array_values(array_filter(
            $buckets,
            fn (array $bucket): bool => $bucket['courses'] !== [],
        ));
    }

    public static function show(string $slug): ?array
    {
        $syllabus = LearnerProgress::syllabus($slug);

        if (! $syllabus) {
            return null;
        }

        $course = $syllabus['course'];
        $cta = $syllabus['enrolled']
            ? 'continue'
            : ($syllabus['requires_enrolment'] ? 'enroll' : 'start');

        return [
            'header' => LearnerHeader::make(
                $course['title'],
                ($syllabus['platform']['name'] ?? '').' · '.$course['instructor'],
                [
                    ['label' => 'My courses', 'route' => 'learner.courses'],
                    ['label' => $course['title']],
                ],
            ),
            'syllabus' => $syllabus,
            'cta' => $cta,
            'session' => self::upcomingSession($slug),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function upcomingSession(string $slug): ?array
    {
        foreach (LiveSessions::all() as $session) {
            if ($session['course_slug'] === $slug && in_array($session['status'], ['scheduled', 'live'], true)) {
                return $session;
            }
        }

        return null;
    }
}
