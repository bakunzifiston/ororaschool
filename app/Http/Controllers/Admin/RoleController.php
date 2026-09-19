<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\RolesPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.roles.index', [
            'page' => RolesPage::index(
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
            ),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.edit', [
            'page' => RolesPage::create(),
        ]);
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('admin.roles')
            ->with('status', 'Custom role created. Nothing was saved in this build.');
    }

    public function edit(string $role): View
    {
        $page = RolesPage::edit($role);
        abort_unless($page, 404);

        return view('admin.roles.edit', ['page' => $page]);
    }

    public function update(string $role): RedirectResponse
    {
        abort_unless(RolesPage::edit($role), 404);

        return redirect()->route('admin.roles.edit', $role)
            ->with('status', 'Permissions saved on this role. Nothing was written in this build.');
    }
}
