<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\QuizzesPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(Request $request, string $platform): View
    {
        return view('workspace.quizzes.index', [
            'platformSlug' => $platform,
            'page' => QuizzesPage::index(
                $platform,
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
                course: (string) $request->string('course'),
            ),
        ]);
    }

    public function show(string $platform, string $quiz): View
    {
        $page = QuizzesPage::show($platform, $quiz);
        abort_unless($page, 404);

        return view('workspace.quizzes.show', [
            'platformSlug' => $platform,
            'page' => $page,
        ]);
    }

    public function update(string $platform, string $quiz): RedirectResponse
    {
        abort_unless(QuizzesPage::show($platform, $quiz), 404);

        return redirect()->route('workspace.quizzes.show', ['platform' => $platform, 'quiz' => $quiz])
            ->with('status', 'Quiz saved. Nothing was written in this build.');
    }

    public function store(string $platform): RedirectResponse
    {
        return redirect()->route('workspace.quizzes', ['platform' => $platform])
            ->with('status', 'Quiz created. Nothing was saved in this build.');
    }
}
