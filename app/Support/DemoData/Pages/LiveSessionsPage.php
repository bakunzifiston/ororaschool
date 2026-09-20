<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Courses;
use App\Support\DemoData\LiveSessions;
use App\Support\DemoData\Paging;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class LiveSessionsPage
{
    public static function index(string $platform, bool $empty = false, int $page = 1): array
    {
        $current = Platforms::find($platform) ?? Platforms::find('gemura');
        $rows = $empty ? [] : LiveSessions::forPlatform($platform);
        $paged = Paging::paginate($rows, $page, 10, '/workspace/'.$platform.'/live-sessions', array_filter(['empty' => $empty ? 1 : null]));

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Live sessions',
                'Clinics on '.$current['name'].'. Field officers book up to two weeks ahead.',
            ),
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'Nothing scheduled on '.$current['name'].' yet',
            'emptyMessage' => 'Schedule a clinic with a meeting URL. Past sessions keep a recording link.',
        ];
    }

    public static function form(string $platform, ?int $id = null): ?array
    {
        $current = Platforms::find($platform);
        $session = $id ? LiveSessions::findForPlatform($id, $platform) : null;

        if ($id && ! $session) {
            return null;
        }

        $isEdit = (bool) $session;

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                $isEdit ? 'Edit '.$session['title'] : 'Schedule a live session',
                $isEdit
                    ? 'Past sessions keep a recording URL. Upcoming ones keep the meeting link.'
                    : 'Creates a clinic on '.$current['name'].'. Nothing is saved in this build.',
                [
                    ['label' => 'Live sessions', 'route' => 'workspace.sessions', 'params' => ['platform' => $platform]],
                    ['label' => $isEdit ? 'Edit '.$session['title'] : 'Schedule a live session'],
                ],
            ),
            'session' => $session ?? [
                'id' => null,
                'title' => '',
                'instructor' => '',
                'course_slug' => '',
                'starts' => '',
                'duration' => 45,
                'status' => 'scheduled',
                'url' => '',
                'recording' => '',
            ],
            'isEdit' => $isEdit,
            'instructors' => array_column(People::instructorsOn($platform), 'name', 'name'),
            'courses' => array_column(Courses::forPlatform($platform), 'title', 'slug'),
            'statuses' => [
                'scheduled' => 'Scheduled',
                'live' => 'Live',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ],
        ];
    }
}
