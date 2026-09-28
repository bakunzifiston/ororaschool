<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrolment;
use App\Support\DemoData\Pages\LearnerLessonPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function show(string $course, string $lesson): View
    {
        $page = LearnerLessonPage::show($course, $lesson);
        abort_unless($page, 404);

        return view('learner.lessons.show', [
            'page' => $page,
        ]);
    }

    public function complete(Request $request, string $course, string $lesson): RedirectResponse
    {
        $page = LearnerLessonPage::show($course, $lesson);
        abort_unless($page, 404);

        $courseModel = Course::query()->published()->where('slug', $course)->firstOrFail();
        $enrolment = Enrolment::enrol($request->user(), $courseModel);

        if (($page['lesson']['state'] ?? '') === 'in_progress') {
            $enrolment->load('course');
            $enrolment->completeCurrentLesson();
        }

        return redirect()->route('learner.courses.lessons.show', ['course' => $course, 'lesson' => $lesson])
            ->with('status', 'Marked complete.');
    }
}
