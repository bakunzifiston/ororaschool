<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Courses;
use App\Support\DemoData\Curriculum;
use App\Support\DemoData\Paging;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class CurriculumPage
{
    public static function types(): array
    {
        return [
            'video' => 'Video',
            'text' => 'Text',
            'pdf' => 'PDF',
            'audio' => 'Audio',
            'external' => 'External',
            'live_session' => 'Live session',
        ];
    }

    public static function typeIcon(string $type): string
    {
        return match ($type) {
            'video' => 'play',
            'text' => 'file',
            'pdf' => 'file',
            'audio' => 'volume',
            'external' => 'link',
            'live_session' => 'video',
            default => 'file',
        };
    }

    public static function modules(string $platform, ?string $courseSlug = null, bool $empty = false): ?array
    {
        $current = Platforms::find($platform) ?? Platforms::find('gemura');
        $courses = Courses::forPlatform($platform);
        $course = $courseSlug
            ? Courses::findForPlatform($courseSlug, $platform)
            : ($courses[0] ?? null);

        if ($courseSlug && ! $course) {
            return null;
        }

        $modules = ($empty || ! $course) ? [] : Curriculum::modulesFor($course['slug']);

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Modules',
                $course
                    ? 'Builder for '.$course['title'].' on '.$current['name'].'. Reordering is visual only.'
                    : 'Pick a '.$current['name'].' course to open its builder.',
            ),
            'courses' => array_column($courses, 'title', 'slug'),
            'course' => $course,
            'modules' => $modules,
            'types' => self::types(),
            'emptyTitle' => 'No modules in this course yet',
            'emptyMessage' => 'Add a module, then nest lessons under it. Each lesson carries a content type.',
        ];
    }

    public static function lessons(string $platform, bool $empty = false, int $page = 1): array
    {
        $current = Platforms::find($platform) ?? Platforms::find('gemura');
        $rows = $empty ? [] : Curriculum::lessonsOn($platform);
        $paged = Paging::paginate($rows, $page, 10, '/workspace/'.$platform.'/lessons', array_filter(['empty' => $empty ? 1 : null]));

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Lessons',
                'Every lesson on '.$current['name'].'. Open a row to land in that course’s builder.',
            ),
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'types' => self::types(),
            'emptyTitle' => 'No lessons on '.$current['name'].' yet',
            'emptyMessage' => 'Lessons are authored inside a course module. Add a module first, then nest lessons under it.',
        ];
    }
}
