<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavePlatformRequest;
use App\Models\Platform;
use App\Models\User;
use App\Support\DemoData\Pages\PlatformsPage;
use App\Support\DemoData\Platforms;
use App\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.platforms.index', [
            'page' => PlatformsPage::index(
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
            ),
        ]);
    }

    public function create(): View
    {
        return view('admin.platforms.form', [
            'page' => PlatformsPage::form(),
        ]);
    }

    public function store(SavePlatformRequest $request): RedirectResponse
    {
        $platform = Platform::createOnEstate([
            'name' => $request->validated('name'),
            'slug' => $request->validated('slug'),
            'description' => $request->validated('description'),
            'active' => $request->boolean('active'),
        ]);

        $admin = $this->provisionAdmin($platform, $request);

        return redirect()->route('admin.platforms')
            ->with('status', $admin instanceof User
                ? $platform->name.' created. '.$admin->name.' can sign in to its workspace.'
                : $platform->name.' created as '.$platform->status.'.');
    }

    public function edit(string $platform): View
    {
        abort_unless($this->visibleFixture($platform), 404);

        return view('admin.platforms.form', [
            'page' => PlatformsPage::form($platform),
        ]);
    }

    public function update(SavePlatformRequest $request, string $platform): RedirectResponse
    {
        abort_unless($this->visibleFixture($platform), 404);

        $record = Platform::query()->where('slug', $platform)->first()
            ?? Platform::firstOrCreateFromSlug($platform);
        abort_if($record->isRemoved(), 404);

        $record->updateOnEstate([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'active' => $request->boolean('active'),
        ]);

        $admin = $this->provisionAdmin($record, $request);

        return redirect()->route('admin.platforms')
            ->with('status', $admin instanceof User
                ? $record->name.' saved. '.$admin->name.' can open its workspace.'
                : $record->name.' saved.');
    }

    public function toggle(string $platform): RedirectResponse
    {
        abort_unless($this->visibleFixture($platform), 404);

        $record = Platform::firstOrCreateFromSlug($platform);
        abort_if($record->isRemoved(), 404);
        $record->toggleActive();

        $verb = $record->status === 'active' ? 'activated' : 'deactivated';

        return redirect()->route('admin.platforms')
            ->with('status', $record->name.' '.$verb.'.');
    }

    public function destroy(string $platform): RedirectResponse
    {
        abort_unless($this->visibleFixture($platform), 404);

        $record = Platform::firstOrCreateFromSlug($platform);
        abort_if($record->isRemoved(), 404);

        $name = $record->name;
        $record->removeFromEstate();

        return redirect()->route('admin.platforms')
            ->with('status', $name.' was removed.');
    }

    private function provisionAdmin(Platform $platform, SavePlatformRequest $request): ?User
    {
        $email = $request->validated('admin_email');

        if (! filled($email)) {
            return null;
        }

        $existing = User::query()->where('email', $email)->first();

        if ($existing instanceof User) {
            $existing->platforms()->syncWithoutDetaching([$platform->id]);

            return $existing;
        }

        $user = User::query()->create([
            'name' => $request->validated('admin_name'),
            'email' => $email,
            'password' => $request->validated('admin_password'),
            'district' => $request->validated('admin_district'),
            'role' => UserRole::PlatformStaff,
            'status' => 'active',
        ]);
        $user->markEmailAsVerified();
        $user->platforms()->sync([$platform->id]);

        return $user;
    }

    private function visibleFixture(string $slug): bool
    {
        $fixture = Platforms::findWithPersistedStatus($slug);

        return $fixture !== null && ($fixture['status'] ?? '') !== 'deleted';
    }
}
