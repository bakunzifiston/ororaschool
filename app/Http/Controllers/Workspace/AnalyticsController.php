<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\WorkspaceAnalyticsPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request, string $platform): View
    {
        return view('workspace.analytics.index', [
            'platformSlug' => $platform,
            'page' => WorkspaceAnalyticsPage::data($platform, empty: $request->boolean('empty')),
        ]);
    }
}
