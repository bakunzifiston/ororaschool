<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLearningResourceRequest;
use App\Models\LearningResource;
use App\Models\Platform;
use App\Support\DemoData\Pages\ResourcesPage;
use App\Support\DemoData\Resources;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        $page = ResourcesPage::form($platform);

        abort_unless($page !== null, 404);

        return view('workspace.resources.form', [
            'platformSlug' => $platform,
            'page' => $page,
        ]);
    }

    public function store(StoreLearningResourceRequest $request, string $platform): RedirectResponse
    {
        $record = $request->platform() ?? Platform::query()->where('slug', $platform)->firstOrFail();

        LearningResource::createOnPlatform($record, [
            ...$request->safe()->only(['title', 'type', 'attached_kind', 'attached_key', 'source_url']),
            'file' => $request->file('file'),
        ]);

        return redirect()->route('workspace.resources', ['platform' => $platform])
            ->with('status', 'Resource saved.');
    }

    public function show(string $platform, string $resource): View
    {
        $page = ResourcesPage::show($platform, $resource);

        abort_unless($page !== null, 404);

        return view('workspace.resources.show', [
            'platformSlug' => $platform,
            'page' => $page,
        ]);
    }

    public function file(Request $request, string $platform, string $resource): BinaryFileResponse|Response
    {
        $row = Resources::find($resource, $platform);

        abort_unless($row !== null && Resources::canStream($row), 404);

        return Resources::stream($row, $request->boolean('download'));
    }

    public function edit(string $platform, string $resource): View
    {
        $page = ResourcesPage::form($platform, $resource);

        abort_unless($page !== null, 404);

        return view('workspace.resources.form', [
            'platformSlug' => $platform,
            'page' => $page,
        ]);
    }

    public function update(StoreLearningResourceRequest $request, string $platform, string $resource): RedirectResponse
    {
        abort_unless(Resources::find($resource, $platform) !== null, 404);

        $record = $request->platform() ?? Platform::query()->where('slug', $platform)->firstOrFail();

        LearningResource::saveOnPlatform($record, $resource, [
            ...$request->safe()->only(['title', 'type', 'attached_kind', 'attached_key', 'source_url']),
            'file' => $request->file('file'),
        ]);

        return redirect()->route('workspace.resources.show', ['platform' => $platform, 'resource' => $resource])
            ->with('status', 'Resource saved.');
    }

    public function destroy(string $platform, string $resource): RedirectResponse
    {
        $row = Resources::find($resource, $platform);

        abort_unless($row !== null, 404);

        $record = Platform::query()->where('slug', $platform)->firstOrFail();

        LearningResource::removeFromPlatform($record, $resource);

        return redirect()->route('workspace.resources', ['platform' => $platform])
            ->with('status', $row['title'].' was removed.');
    }
}
