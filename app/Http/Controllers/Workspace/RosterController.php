<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\LearnersPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RosterController extends Controller
{
    public function index(Request $request, string $platform): View
    {
        return view('workspace.learners.index', [
            'platformSlug' => $platform,
            'page' => LearnersPage::index(
                $platform,
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
            ),
        ]);
    }

    public function show(string $platform, int $learner): View
    {
        $page = LearnersPage::show($platform, $learner);
        abort_unless($page, 404);

        return view('workspace.learners.show', [
            'platformSlug' => $platform,
            'page' => $page,
        ]);
    }
}
