<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\LiveSessionsPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveSessionController extends Controller
{
    public function index(Request $request, string $platform): View
    {
        return view('workspace.sessions.index', [
            'platformSlug' => $platform,
            'page' => LiveSessionsPage::index(
                $platform,
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
            ),
        ]);
    }

    public function create(string $platform): View
    {
        return view('workspace.sessions.form', [
            'platformSlug' => $platform,
            'page' => LiveSessionsPage::form($platform),
        ]);
    }

    public function store(string $platform): RedirectResponse
    {
        return redirect()->route('workspace.sessions', ['platform' => $platform])
            ->with('status', 'Live session scheduled. Nothing was saved in this build.');
    }

    public function edit(string $platform, int $session): View
    {
        $page = LiveSessionsPage::form($platform, $session);
        abort_unless($page && $page['isEdit'], 404);

        return view('workspace.sessions.form', [
            'platformSlug' => $platform,
            'page' => $page,
        ]);
    }

    public function update(string $platform, int $session): RedirectResponse
    {
        abort_unless(LiveSessionsPage::form($platform, $session), 404);

        return redirect()->route('workspace.sessions', ['platform' => $platform])
            ->with('status', 'Live session saved. Nothing was written in this build.');
    }
}
