<?php

namespace App\Http\Controllers;

use App\Support\DemoData\Navigation;
use App\Support\DemoData\Pages\WorkspaceDashboard;
use App\Support\DemoData\Platforms;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    /**
     * The platform comes from the route, which is the whole of the workspace
     * switcher's behaviour: pick a platform, land on its dashboard. No access
     * check — that belongs to the backend phases.
     */
    public function dashboard(string $platform): View
    {
        return view('workspace.dashboard', [
            'platformSlug' => $platform,
            'page' => WorkspaceDashboard::for($platform),
        ]);
    }

    public function placeholder(Request $request, string $platform): View
    {
        $label = Navigation::labelForRoute($request->route()->getName());
        $current = Platforms::find($platform);

        return view('workspace.placeholder', [
            'platformSlug' => $platform,
            'platform' => $current,
            'label' => $label,
            'breadcrumb' => [
                ['label' => $current['name'], 'route' => 'workspace.dashboard', 'params' => ['platform' => $platform]],
                ['label' => $label],
            ],
        ]);
    }
}
