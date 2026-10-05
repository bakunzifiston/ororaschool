<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Curriculum;
use App\Support\DemoData\Pages\CurriculumPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CurriculumController extends Controller
{
    public function modules(Request $request, string $platform): View
    {
        $page = CurriculumPage::modules(
            $platform,
            $request->string('course')->toString() ?: null,
            empty: $request->boolean('empty'),
        );
        abort_unless($page, 404);

        return view('workspace.curriculum.modules', [
            'platformSlug' => $platform,
            'page' => $page,
        ]);
    }

    public function storeModule(Request $request, string $platform): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'course' => ['required', 'string', 'max:80'],
        ]);

        $module = Curriculum::addModule($platform, $validated['course'], $validated['title']);
        abort_unless($module !== null, 404);

        return redirect()->route('workspace.modules', ['platform' => $platform, 'course' => $validated['course']])
            ->with('status', $module['title'].' was added. Nothing was written in this build.');
    }

    public function lessons(Request $request, string $platform): View
    {
        return view('workspace.curriculum.lessons', [
            'platformSlug' => $platform,
            'page' => CurriculumPage::lessons(
                $platform,
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
            ),
        ]);
    }

    public function storeLesson(Request $request, string $platform): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'course' => ['required', 'string', 'max:80'],
            'module' => ['required', 'string', 'max:80'],
            'type' => ['required', 'string', Rule::in(array_keys(CurriculumPage::types()))],
        ]);

        $lesson = Curriculum::addLesson(
            $platform,
            $validated['course'],
            $validated['module'],
            $validated['title'],
            $validated['type'],
        );
        abort_unless($lesson !== null, 404);

        return redirect()->route('workspace.modules', ['platform' => $platform, 'course' => $validated['course']])
            ->with('status', $lesson['title'].' was added. Nothing was written in this build.');
    }
}
