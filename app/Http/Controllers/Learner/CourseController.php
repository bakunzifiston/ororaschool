<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrolment;
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
                filters: [
                    'status' => (string) $request->string('status'),
                    'platform' => (string) $request->string('platform'),
                    'category' => (string) $request->string('category'),
                ],
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

    public function enroll(Request $request, Course $course): RedirectResponse
    {
        abort_unless($course->status === 'published', 404);

        Enrolment::enrol($request->user(), $course);

        return redirect()->route('learner.courses.show', ['course' => $course])
            ->with('status', 'Enrolled. The course is on your learning record.');
    }
}
