<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrolment;
use App\Models\Lesson;
use App\Support\DemoData\Pages\LearnerLessonPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

    public function file(Request $request, string $course, string $lesson): BinaryFileResponse
    {
        abort_unless(LearnerLessonPage::show($course, $lesson) !== null, 404);

        $row = Lesson::query()
            ->where('slug', $lesson)
            ->whereHas('course', fn ($query) => $query->where('slug', $course))
            ->first();

        abort_unless($row !== null && filled($row->path), 404);

        return $row->stream($request->boolean('download'));
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
