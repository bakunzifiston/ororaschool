<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\LearnerCoursesPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        return view('learner.courses.index', [
            'page' => LearnerCoursesPage::index(
                status: (string) $request->string('status'),
                empty: $request->boolean('empty'),
            ),
        ]);
    }

    public function show(string $course): View
    {
        $page = LearnerCoursesPage::show($course);
        abort_unless($page, 404);

        return view('learner.courses.show', [
            'page' => $page,
        ]);
    }

    public function enroll(string $course): RedirectResponse
    {
        abort_unless(LearnerCoursesPage::show($course), 404);

        return redirect()->route('learner.courses.show', ['course' => $course])
            ->with('status', 'Enrolled. Nothing was written in this build — the course would open on your record.');
    }
}
