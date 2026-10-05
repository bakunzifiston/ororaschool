<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Categories;
use App\Support\DemoData\Pages\CategoriesPage;
use Illuminate\Http\RedirectResponse;
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

    public function store(Request $request, string $platform): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'academy' => ['required', 'string', 'max:80'],
            'parent' => ['nullable', 'string', 'max:80'],
        ]);

        $parent = $validated['parent'] ?? null;
        $node = Categories::add(
            $platform,
            $validated['academy'],
            $validated['name'],
            is_string($parent) && $parent !== '' ? $parent : null,
        );

        abort_unless($node !== null, 404);

        return redirect()->route('workspace.categories', ['platform' => $platform])
            ->with('status', $node['name'].' was added. Nothing was written in this build.');
    }

    public function destroy(string $platform, string $category): RedirectResponse
    {
        $node = Categories::remove($platform, $category);

        abort_unless($node !== null, 404);

        return redirect()->route('workspace.categories', ['platform' => $platform])
            ->with('status', $node['name'].' was removed. Nothing was written in this build.');
    }
}
