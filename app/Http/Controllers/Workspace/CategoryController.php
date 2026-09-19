<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\CategoriesPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request, string $platform): View
    {
        return view('workspace.categories.index', [
            'platformSlug' => $platform,
            'page' => CategoriesPage::index($platform, empty: $request->boolean('empty')),
        ]);
    }
}
