<?php

namespace App\Support\DemoData;

use App\Models\RemovedRole;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Five system-protected roles plus example custom roles. Permissions are data
 * attached to a role — the role editor is where that becomes visible.
 */
class Roles
{
    public const SUPER_ADMIN = 'super-admin';

    public static function all(): array
    {
        return [
            [
                'key' => 'super-admin',
                'label' => 'Super Admin',
                'scope' => 'All academies',
                'elevated' => true,
                'system' => true,
                'description' => 'Estate-wide. Sees every academy, every user, every setting.',
                'holders' => 2,
            ],
            [
                'key' => 'platform-admin',
                'label' => 'Academy Admin',
                'scope' => 'Academy',
                'elevated' => true,
                'system' => true,
                'description' => 'Owns one academy: staff, catalogue and certificates.',
                'holders' => 4,
            ],
            [
                'key' => 'content-manager',
                'label' => 'Content Manager',
                'scope' => 'Academy',
                'elevated' => false,
                'system' => true,
                'description' => 'Builds and publishes the catalogue. Cannot activate an academy or edit global settings.',
                'holders' => 3,
            ],
            [
                'key' => 'instructor',
                'label' => 'Instructor',
                'scope' => 'Course',
                'elevated' => false,
                'system' => true,
                'description' => 'Teaches assigned courses, runs live clinics, cannot enrol whole cohorts.',
                'holders' => 6,
            ],
            [
                'key' => 'learner',
                'label' => 'Learner',
                'scope' => 'Self',
                'elevated' => false,
                'system' => true,
                'description' => 'Takes courses, sits quizzes, receives certificates. No staff surface.',
                'holders' => 10412,
            ],
            [
                'key' => 'reviewer',
                'label' => 'Reviewer',
                'scope' => 'Course',
                'elevated' => false,
                'system' => false,
                'description' => 'Signs off a course before it is published. Custom role used by Gemura and FeedGrid.',
                'holders' => 3,
            ],
            [
                'key' => 'field-coordinator',
                'label' => 'Field Coordinator',
                'scope' => 'District',
                'elevated' => false,
                'system' => false,
                'description' => 'Enrols a district cohort and books them onto clinics. Cannot author courses.',
                'holders' => 8,
            ],
            [
                'key' => 'certificate-officer',
                'label' => 'Certificate Officer',
                'scope' => 'Academy',
                'elevated' => false,
                'system' => false,
                'description' => 'Issues and revokes certificates against the global numbering format.',
                'holders' => 2,
            ],
        ];
    }

    public static function find(string $key): array
    {
        $key = match ($key) {
            'platform-owner' => 'platform-admin',
            'platform-staff' => 'platform-admin',
            'curriculum-lead' => 'content-manager',
            default => $key,
        };

        foreach (self::all() as $role) {
            if ($role['key'] === $key) {
                return $role;
            }
        }

        return ['key' => $key, 'label' => ucfirst(str_replace('-', ' ', $key)), 'scope' => 'Unknown', 'elevated' => false, 'system' => false, 'description' => '', 'holders' => 0];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function visible(): array
    {
        $removed = RemovedRole::keys();

        return array_values(array_filter(
            self::all(),
            fn (array $role) => ! in_array($role['key'], $removed, true),
        ));
    }

    public static function system(): array
    {
        return array_values(array_filter(self::visible(), fn (array $role) => $role['system']));
    }

    public static function custom(): array
    {
        return array_values(array_filter(self::visible(), fn (array $role) => ! $role['system']));
    }
}
