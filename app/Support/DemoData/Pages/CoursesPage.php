<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Courses;
use App\Support\DemoData\Paging;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class CoursesPage
{
    public static function steps(): array
    {
        return ['draft', 'pending_review', 'approved', 'published', 'archived'];
    }

    /**
     * @return list<array{to: string, label: string, variant: string}>
     */
    public static function transitions(string $status): array
    {
        return match ($status) {
            'draft' => [['to' => 'pending_review', 'label' => 'Submit for review', 'variant' => 'primary']],
            'pending_review' => [
                ['to' => 'approved', 'label' => 'Approve', 'variant' => 'primary'],
                ['to' => 'draft', 'label' => 'Return to draft', 'variant' => 'secondary'],
            ],
            'approved' => [
                ['to' => 'published', 'label' => 'Publish', 'variant' => 'primary'],
                ['to' => 'draft', 'label' => 'Return to draft', 'variant' => 'secondary'],
            ],
            'published' => [['to' => 'archived', 'label' => 'Archive', 'variant' => 'danger']],
            'archived' => [['to' => 'draft', 'label' => 'Restore to draft', 'variant' => 'secondary']],
            default => [],
        };
    }

    public static function index(string $platform, bool $empty = false, int $page = 1, string $status = '', string $academy = ''): array
    {
        $current = Platforms::find($platform) ?? Platforms::find('gemura');
        $rows = $empty ? [] : self::filtered($platform, $status, $academy);
        $academies = ['' => 'Any academy'] + array_unique(array_filter(array_column(Courses::forPlatform($platform), 'academy', 'academy_slug')));
        $query = array_filter([
            'empty' => $empty ? 1 : null,
            'status' => $status !== '' ? $status : null,
            'academy' => $academy !== '' ? $academy : null,
        ]);
        $paged = Paging::paginate($rows, $page, 5, '/workspace/'.$platform.'/courses', $query);

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Courses',
                $current['name'].' catalogue — '.$current['discipline'].'. Filter by lifecycle status or academy.',
            ),
            'filters' => [
                'status' => $status,
                'academy' => $academy,
                'statuses' => [
                    '' => 'Any status',
                    'draft' => 'Draft',
                    'pending_review' => 'Pending review',
                    'approved' => 'Approved',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ],
                'academies' => $academies,
            ],
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => $status !== '' || $academy !== ''
                ? 'No courses match these filters'
                : 'No courses in '.$current['name'].' yet',
            'emptyMessage' => 'Open a draft in this workspace. It stays off the learner catalogue until it is published.',
        ];
    }

    public static function form(string $platform, ?string $slug = null): ?array
    {
        $current = Platforms::find($platform);
        $course = $slug ? Courses::findForPlatform($slug, $platform) : null;

        if ($slug && ! $course) {
            return null;
        }

        $isEdit = (bool) $course;
        $instructors = array_column(People::instructorsOn($platform), 'name', 'name');
        $academies = array_unique(array_filter(array_column(Courses::forPlatform($platform), 'academy', 'academy_slug')));

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                $isEdit ? 'Edit '.$course['title'] : 'New course',
                $isEdit
                    ? 'Catalogue fields for this '.$current['name'].' course. Publishing lives on the course page.'
                    : 'Starts as a draft, visible only to '.$current['name'].' staff.',
                [
                    ['label' => 'Courses', 'route' => 'workspace.courses', 'params' => ['platform' => $platform]],
                    ['label' => $isEdit ? 'Edit '.$course['title'] : 'New course'],
                ],
            ),
            'course' => $course ?? [
                'slug' => '',
                'title' => '',
                'description' => '',
                'summary' => '',
                'academy_slug' => '',
                'academy' => '',
                'category' => '',
                'instructor' => '',
                'instructors' => [],
                'difficulty' => 'Foundation',
                'duration' => 60,
                'language' => 'English',
                'paid' => false,
                'certificate_eligible' => true,
                'enrollment_required' => true,
                'status' => 'draft',
            ],
            'isEdit' => $isEdit,
            'academies' => $academies,
            'instructors' => $instructors,
            'difficulties' => ['Foundation' => 'Foundation', 'Intermediate' => 'Intermediate', 'Advanced' => 'Advanced'],
            'languages' => ['English' => 'English', 'Kinyarwanda' => 'Kinyarwanda', 'English / Kinyarwanda' => 'English / Kinyarwanda', 'French' => 'French'],
        ];
    }

    public static function show(string $platform, string $slug): ?array
    {
        $current = Platforms::find($platform);
        $course = Courses::findForPlatform($slug, $platform);

        if (! $course) {
            return null;
        }

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                $course['title'],
                $course['academy'].' · '.$course['category'].' · '.$current['discipline'],
                [
                    ['label' => 'Courses', 'route' => 'workspace.courses', 'params' => ['platform' => $platform]],
                    ['label' => $course['title']],
                ],
            ),
            'course' => $course,
            'steps' => self::steps(),
            'transitions' => self::transitions($course['status']),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function filtered(string $platform, string $status, string $academy): array
    {
        $rows = Courses::forPlatform($platform);

        if ($status !== '') {
            $rows = array_values(array_filter($rows, fn (array $row) => $row['status'] === $status));
        }

        if ($academy !== '') {
            $rows = array_values(array_filter($rows, fn (array $row) => ($row['academy_slug'] ?? '') === $academy));
        }

        return $rows;
    }
}
