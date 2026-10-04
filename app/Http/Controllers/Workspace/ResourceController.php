<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLearningResourceRequest;
use App\Models\LearningResource;
use App\Models\Platform;
use App\Support\DemoData\Pages\ResourcesPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request, string $platform): View
    {
        return view('workspace.resources.index', [
            'platformSlug' => $platform,
            'page' => ResourcesPage::index(
                $platform,
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
                type: (string) $request->string('type'),
            ),
        ]);
    }

    public function create(string $platform): View
    {
        return view('workspace.resources.form', [
            'platformSlug' => $platform,
            'page' => ResourcesPage::form($platform),
        ]);
    }

    public function store(StoreLearningResourceRequest $request, string $platform): RedirectResponse
    {
        $record = $request->platform() ?? Platform::query()->where('slug', $platform)->firstOrFail();

        LearningResource::createOnPlatform($record, [
            ...$request->safe()->only(['title', 'type', 'attached_kind', 'attached_key']),
            'file' => $request->file('file'),
        ]);

        return redirect()->route('workspace.resources', ['platform' => $platform])
            ->with('status', 'Resource saved.');
    }
}
