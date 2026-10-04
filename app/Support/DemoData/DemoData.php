<?php

namespace App\Support\DemoData;

use App\Models\User;
use App\UserRole;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * ---------------------------------------------------------------------------
 * This whole namespace stands in for authentication, authorisation and the
 * database. Nothing outside app/Support/DemoData knows where the data comes
 * from: views and controllers call these methods and would later call a
 * repository or Eloquent model with the same shape.
 *
 * When real auth lands:
 *   - currentUser()      -> auth()->user()
 *   - currentPlatform()  -> route-bound Platform model
 *   - accessiblePlatforms() -> $user->platforms (via user_platform_roles)
 *   - the rest of this namespace -> Eloquent models + seeders
 *
 * There is no permission check anywhere in here. Role values exist so chips and
 * role-gated nav can be demonstrated, not enforced.
 * ---------------------------------------------------------------------------
 */
class DemoData
{
    /**
     * One persona per experience, so every screen has a plausible "who am I".
     * `platforms` is what drives the workspace switcher's list.
     */
    public static function personas(): array
    {
        return [
            'super-admin' => [
                'name' => 'Gloriose Mukandayisenga',
                'title' => 'Academy Administrator',
                'role' => 'super-admin',
                'email' => 'g.mukandayisenga@ororaschool.rw',
                'district' => 'Kigali',
                'platforms' => ['ororafarm', 'gemura', 'buchapro', 'feedgrid'],
            ],
            'platform-workspace' => [
                'name' => 'Solange Nyirahabimana',
                'title' => 'Content Manager',
                'role' => 'content-manager',
                'email' => 's.nyirahabimana@gemura.rw',
                'district' => 'Burera',
                'platforms' => ['gemura', 'buchapro', 'feedgrid'],
            ],
            'learner' => [
                'name' => 'Placide Bizimana',
                'title' => 'Dairy farmer, Kabarore sector',
                'role' => 'learner',
                'email' => 'p.bizimana@umuhinzi.rw',
                'district' => 'Gatsibo',
                'phone' => '+250 788 441 209',
                'platforms' => ['gemura', 'buchapro', 'feedgrid'],
            ],
        ];
    }

    public static function currentUser(string $experience = 'super-admin'): array
    {
        $persona = self::personas()[$experience] ?? self::personas()['super-admin'];
        $user = auth()->user();

        if (! $user instanceof User) {
            return $persona;
        }

        $persona['name'] = $user->name;
        $persona['email'] = $user->email;
        $persona['district'] = $user->district ?: $persona['district'];

        if ($user->role === UserRole::SuperAdmin) {
            $persona['role'] = 'super-admin';
            $persona['title'] = 'Academy Administrator';
        } elseif ($user->role === UserRole::PlatformStaff) {
            $persona['role'] = 'platform-admin';
            $persona['title'] = 'Academy Admin';
        }

        return $persona;
    }

    /**
     * The platform currently being worked in. Resolved from the route parameter
     * so the workspace switcher is just navigation — no session state, no
     * access check.
     */
    public static function currentPlatform(?string $slug = null): array
    {
        if ($slug === null || $slug === '') {
            return Platforms::find('gemura') ?? Platforms::visible()[0];
        }

        return Platforms::require($slug);
    }

    /**
     * Platforms the signed-in staff member can reach. Super Admins keep the
     * fixture list so the workspace chrome stays the same while they can still
     * open every slug by URL.
     */
    public static function accessiblePlatforms(string $experience = 'platform-workspace'): array
    {
        $allowed = self::currentUser($experience)['platforms'];
        $user = auth()->user();

        if ($user instanceof User && $user->role === UserRole::SuperAdmin) {
            return Platforms::visible();
        }

        if ($user instanceof User && $user->role === UserRole::PlatformStaff) {
            $assigned = $user->platforms()->orderBy('sort_order')->pluck('slug')->all();

            if ($assigned !== []) {
                $allowed = $assigned;
            }
        }

        return array_values(array_filter(
            Platforms::visible(),
            function (array $platform) use ($allowed, $user): bool {
                if (! in_array($platform['slug'], $allowed, true)) {
                    return false;
                }

                if ($user instanceof User && $user->role === UserRole::PlatformStaff) {
                    return $platform['status'] === 'active';
                }

                return true;
            },
        ));
    }

    /**
     * Everything a layout needs in one payload.
     */
    public static function shell(string $experience, ?string $platformSlug = null): array
    {
        $user = self::currentUser($experience);

        return [
            'experience' => $experience,
            'user' => $user,
            'role' => Roles::find($user['role']),
            'platform' => self::currentPlatform($platformSlug),
            'platforms' => self::accessiblePlatforms($experience),
            'navigation' => Navigation::visibleFor($experience, $user),
        ];
    }
}
