<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\LearnerQuizPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function show(string $quiz): View
    {
        $page = LearnerQuizPage::show($quiz, submitted: session()->has('quiz_result'));
        abort_unless($page, 404);

        if (session('quiz_result')) {
            $page['result'] = session('quiz_result');
        }

        return view('learner.quizzes.show', [
            'page' => $page,
        ]);
    }

    public function submit(string $quiz): RedirectResponse
    {
        $page = LearnerQuizPage::show($quiz, submitted: true);
        abort_unless($page, 404);

        return redirect()->route('learner.quizzes.show', ['quiz' => $quiz])
            ->with('quiz_result', $page['result'])
            ->with('status', $page['result']['passed']
                ? 'You passed. This score is a fixture — nothing was stored.'
                : 'Not a pass yet. This score is a fixture — nothing was stored.');
    }
}
