<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\Models\Platform;
use App\Models\User;
use App\Support\DemoData\Pages\UsersPage;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;
use App\UserRole;
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

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'password', 'district', 'role', 'status']);

        $user = User::query()->create($data);
        $user->markEmailAsVerified();

        $this->syncPlatforms($user, $request->validated('platforms') ?? []);

        $names = $user->platforms()->orderBy('sort_order')->pluck('name')->implode(', ');

        return redirect()->route('admin.accounts.show', $user)
            ->with('status', $names === ''
                ? $user->name.' can now sign in.'
                : $user->name.' can now sign in and open '.$names.'.');
    }

    public function show(int $user): View
    {
        $page = UsersPage::show($user);
        abort_unless($page, 404);

        return view('admin.users.show', ['page' => $page]);
    }

    public function showAccount(User $user): View
    {
        return view('admin.users.show', [
            'page' => UsersPage::showAccount($user),
        ]);
    }

    public function edit(int $user): View
    {
        $page = UsersPage::form($user);
        abort_unless($page['isEdit'], 404);

        return view('admin.users.form', ['page' => $page]);
    }

    public function editAccount(User $user): View
    {
        return view('admin.users.form', [
            'page' => UsersPage::formAccount($user),
        ]);
    }

    public function update(int $user): RedirectResponse
    {
        abort_unless(People::find($user), 404);

        return redirect()->route('admin.users.show', $user)
            ->with('status', 'Account saved. Nothing was written in this build.');
    }

    public function updateAccount(UpdateAdminUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'district', 'role', 'status']);

        if (filled($request->validated('password'))) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);
        $this->syncPlatforms($user, $request->validated('platforms') ?? []);

        return redirect()->route('admin.accounts.show', $user)
            ->with('status', $user->name.' was saved.');
    }

    public function destroyAccount(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 403);

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')
            ->with('status', $name.' was removed.');
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

    /**
     * @param  list<string>  $slugs
     */
    private function syncPlatforms(User $user, array $slugs): void
    {
        if ($user->role === UserRole::SuperAdmin && $slugs === []) {
            $slugs = Platforms::slugs();
        }

        $user->platforms()->sync(
            collect($slugs)->map(fn (string $slug): int => Platform::firstOrCreateFromSlug($slug)->id)->all(),
        );
    }
}
