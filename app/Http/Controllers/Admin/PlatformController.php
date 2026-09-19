<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\PlatformsPage;
use App\Support\DemoData\Platforms;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.platforms.index', [
            'page' => PlatformsPage::index(
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
            ),
        ]);
    }

    public function create(): View
    {
        return view('admin.platforms.form', [
            'page' => PlatformsPage::form(),
        ]);
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('admin.platforms')
            ->with('status', 'Platform created as inactive. Nothing was saved in this build.');
    }

    public function edit(string $platform): View
    {
        abort_unless(Platforms::find($platform), 404);

        return view('admin.platforms.form', [
            'page' => PlatformsPage::form($platform),
        ]);
    }

    public function update(string $platform): RedirectResponse
    {
        abort_unless(Platforms::find($platform), 404);

        return redirect()->route('admin.platforms')
            ->with('status', 'Platform saved. Nothing was written in this build.');
    }

    public function toggle(string $platform): RedirectResponse
    {
        $current = Platforms::find($platform);
        abort_unless($current, 404);

        $next = $current['status'] === 'active' ? 'deactivated' : 'activated';

        return redirect()->route('admin.platforms')
            ->with('status', $current['name'].' '.$next.'. Nothing was written in this build.');
    }
}
