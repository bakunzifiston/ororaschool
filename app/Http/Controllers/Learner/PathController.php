<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\LearnerPathsPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PathController extends Controller
{
    public function index(Request $request): View
    {
        return view('learner.paths.index', [
            'page' => LearnerPathsPage::index(empty: $request->boolean('empty')),
        ]);
    }

    public function show(string $path): View
    {
        $page = LearnerPathsPage::show($path);
        abort_unless($page, 404);

        return view('learner.paths.show', [
            'page' => $page,
        ]);
    }
}
