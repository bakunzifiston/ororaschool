<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\CurriculumPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function storeModule(string $platform): RedirectResponse
    {
        return redirect()->route('workspace.modules', ['platform' => $platform])
            ->with('status', 'Module added. Nothing was saved in this build.');
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
        $course = (string) $request->string('course');

        return redirect()->route('workspace.modules', array_filter(['platform' => $platform, 'course' => $course ?: null]))
            ->with('status', 'Lesson added. Nothing was saved in this build.');
    }
}
