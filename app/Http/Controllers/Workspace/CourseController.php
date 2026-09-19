<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Courses;
use App\Support\DemoData\Pages\CoursesPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request, string $platform): View
    {
        return view('workspace.courses.index', [
            'platformSlug' => $platform,
            'page' => CoursesPage::index(
                $platform,
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
                status: (string) $request->string('status'),
                academy: (string) $request->string('academy'),
            ),
        ]);
    }

    public function create(string $platform): View
    {
        return view('workspace.courses.form', [
            'platformSlug' => $platform,
            'page' => CoursesPage::form($platform),
        ]);
    }

    public function store(string $platform): RedirectResponse
    {
        return redirect()->route('workspace.courses', ['platform' => $platform])
            ->with('status', 'Course created as a draft. Nothing was saved in this build.');
    }

    public function show(string $platform, string $course): View
    {
        $page = CoursesPage::show($platform, $course);
        abort_unless($page, 404);

        return view('workspace.courses.show', [
            'platformSlug' => $platform,
            'page' => $page,
        ]);
    }

    public function edit(string $platform, string $course): View
    {
        $page = CoursesPage::form($platform, $course);
        abort_unless($page && $page['isEdit'], 404);

        return view('workspace.courses.form', [
            'platformSlug' => $platform,
            'page' => $page,
        ]);
    }

    public function update(string $platform, string $course): RedirectResponse
    {
        abort_unless(Courses::findForPlatform($course, $platform), 404);

        return redirect()->route('workspace.courses.show', ['platform' => $platform, 'course' => $course])
            ->with('status', 'Course saved. Nothing was written in this build.');
    }

    public function transition(Request $request, string $platform, string $course): RedirectResponse
    {
        $record = Courses::findForPlatform($course, $platform);
        abort_unless($record, 404);

        $to = (string) $request->string('to');

        return redirect()->route('workspace.courses.show', ['platform' => $platform, 'course' => $course])
            ->with('status', $record['title'].' would move to '.$to.'. Nothing was written in this build.');
    }
}
