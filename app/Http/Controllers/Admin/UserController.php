<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\UsersPage;
use App\Support\DemoData\People;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.users.index', [
            'page' => UsersPage::index(
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
                q: (string) $request->string('q'),
                status: (string) $request->string('status'),
                platform: (string) $request->string('platform'),
            ),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'page' => UsersPage::form(),
        ]);
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('admin.users')
            ->with('status', 'Invite sent. Nothing was saved in this build.');
    }

    public function show(int $user): View
    {
        $page = UsersPage::show($user);
        abort_unless($page, 404);

        return view('admin.users.show', ['page' => $page]);
    }

    public function edit(int $user): View
    {
        $page = UsersPage::form($user);
        abort_unless($page['isEdit'], 404);

        return view('admin.users.form', ['page' => $page]);
    }

    public function update(int $user): RedirectResponse
    {
        abort_unless(People::find($user), 404);

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'Account saved. Nothing was written in this build.');
    }

    public function assign(Request $request, int $user): RedirectResponse
    {
        $person = People::find($user);
        abort_unless($person, 404);

        $role = (string) $request->string('role');
        $platform = (string) $request->string('platform');

        return redirect()->route('admin.users.show', $user)
            ->with('status', $person['name'].' would be assigned '.$role.' on '.$platform.'. Nothing was written in this build.');
    }
}
