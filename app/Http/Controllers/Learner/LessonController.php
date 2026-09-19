<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\LearnerLessonPage;
use Illuminate\Http\RedirectResponse;
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

    public function complete(string $course, string $lesson): RedirectResponse
    {
        abort_unless(LearnerLessonPage::show($course, $lesson), 404);

        return redirect()->route('learner.courses.lessons.show', ['course' => $course, 'lesson' => $lesson])
            ->with('status', 'Marked complete. Nothing was written in this build.');
    }
}
