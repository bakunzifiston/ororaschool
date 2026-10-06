<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Curriculum;
use App\Support\DemoData\Pages\CurriculumPage;
use App\Support\DemoData\Resources;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
        $validated = $this->validatedLesson($request);

        $lesson = Curriculum::addLesson(
            $platform,
            $validated['course'],
            $validated['module'],
            array_merge($validated, [
                'file' => $validated['type'] === 'pdf' ? $request->file('file') : null,
            ]),
        );
        abort_unless($lesson !== null, 404);

        return redirect()->route('workspace.modules', ['platform' => $platform, 'course' => $validated['course']])
            ->with('status', $lesson['title'].' was added. Nothing was written in this build.');
    }

    public function updateModule(Request $request, string $platform, string $module): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'course' => ['required', 'string', 'max:80'],
        ]);

        $row = Curriculum::updateModule($platform, $validated['course'], $module, $validated['title']);
        abort_unless($row !== null, 404);

        return redirect()->route('workspace.modules', ['platform' => $platform, 'course' => $validated['course']])
            ->with('status', $row['title'].' was saved. Nothing was written in this build.');
    }

    public function destroyModule(Request $request, string $platform, string $module): RedirectResponse
    {
        $validated = $request->validate([
            'course' => ['required', 'string', 'max:80'],
        ]);

        $row = Curriculum::removeModule($platform, $validated['course'], $module);
        abort_unless($row !== null, 404);

        return redirect()->route('workspace.modules', ['platform' => $platform, 'course' => $validated['course']])
            ->with('status', $row['title'].' was removed. Nothing was written in this build.');
    }

    public function updateLesson(Request $request, string $platform, string $lesson): RedirectResponse
    {
        $validated = $this->validatedLesson($request, requireModule: false);

        $row = Curriculum::updateLesson(
            $platform,
            $validated['course'],
            $lesson,
            array_merge($validated, [
                'file' => $validated['type'] === 'pdf' ? $request->file('file') : null,
            ]),
        );
        abort_unless($row !== null, 404);

        $redirect = $request->string('return')->toString() === 'lessons'
            ? route('workspace.lessons', ['platform' => $platform])
            : route('workspace.modules', ['platform' => $platform, 'course' => $validated['course']]);

        return redirect($redirect)
            ->with('status', $row['title'].' was saved. Nothing was written in this build.');
    }

    public function destroyLesson(Request $request, string $platform, string $lesson): RedirectResponse
    {
        $validated = $request->validate([
            'course' => ['required', 'string', 'max:80'],
        ]);

        $row = Curriculum::removeLesson($platform, $validated['course'], $lesson);
        abort_unless($row !== null, 404);

        $redirect = $request->string('return')->toString() === 'lessons'
            ? route('workspace.lessons', ['platform' => $platform])
            : route('workspace.modules', ['platform' => $platform, 'course' => $validated['course']]);

        return redirect($redirect)
            ->with('status', $row['title'].' was removed. Nothing was written in this build.');
    }

    /**
     * @return array{title: string, course: string, module?: string, type: string, duration: int, body: ?string, source_url: ?string}
     */
    private function validatedLesson(Request $request, bool $requireModule = true): array
    {
        $type = (string) $request->input('type');

        $rules = [
            'title' => ['required', 'string', 'max:80'],
            'course' => ['required', 'string', 'max:80'],
            'type' => ['required', 'string', Rule::in(array_keys(CurriculumPage::types()))],
            'duration' => ['nullable', 'integer', 'min:1', 'max:600'],
            'body' => ['nullable', 'string', 'max:5000'],
            'source_url' => ['nullable', 'string', 'max:500', 'url'],
            'file' => [
                'nullable',
                'file',
                'max:51200',
                Rule::when($type === 'pdf', ['mimes:pdf']),
            ],
        ];

        if ($requireModule) {
            $rules['module'] = ['required', 'string', 'max:80'];
        }

        $validated = $request->validate($rules);

        $type = $validated['type'];
        $sourceUrl = trim((string) ($validated['source_url'] ?? ''));

        if ($sourceUrl === '') {
            $sourceUrl = null;
        } elseif (in_array($type, ['video', 'external', 'pdf', 'audio'], true)) {
            if ($type === 'video' && $this->looksLikeYoutubeHost($sourceUrl) && Resources::youtubeId($sourceUrl) === null) {
                throw ValidationException::withMessages([
                    'source_url' => 'Use a YouTube link.',
                ]);
            }
        } else {
            $sourceUrl = null;
        }

        if (! in_array($type, ['text', 'video', 'audio'], true)) {
            $validated['body'] = null;
        }

        $validated['source_url'] = $sourceUrl;
        $validated['duration'] = (int) ($validated['duration'] ?? 8);
        $validated['body'] = isset($validated['body']) ? trim((string) $validated['body']) : null;

        if ($validated['body'] === '') {
            $validated['body'] = null;
        }

        return $validated;
    }

    private function looksLikeYoutubeHost(string $url): bool
    {
        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?? ''));

        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        if (str_starts_with($host, 'm.')) {
            $host = substr($host, 2);
        }

        return in_array($host, ['youtube.com', 'youtu.be', 'youtube-nocookie.com', 'music.youtube.com'], true);
    }
}
