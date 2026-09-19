<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\InstructorsPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorController extends Controller
{
    public function index(Request $request, string $platform): View
    {
        return view('workspace.instructors.index', [
            'platformSlug' => $platform,
            'page' => InstructorsPage::index($platform, empty: $request->boolean('empty')),
        ]);
    }

    public function show(string $platform, int $instructor): View
    {
        $page = InstructorsPage::show($platform, $instructor);
        abort_unless($page, 404);

        return view('workspace.instructors.show', [
            'platformSlug' => $platform,
            'page' => $page,
        ]);
    }
}
