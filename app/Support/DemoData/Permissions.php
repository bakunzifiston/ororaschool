<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * The permission catalog. Keys are dotted strings so a role can hold a set of
 * them as data — which is the whole point of the role editor in F3.
 *
 * @phpstan-type Permission array{key: string, label: string, hint: string}
 * @phpstan-type Group array{area: string, label: string, permissions: list<Permission>}
 */
class Permissions
{
    /**
     * @return list<Group>
     */
    public static function catalog(): array
    {
        return [
            [
                'area' => 'platforms',
                'label' => 'Platforms',
                'permissions' => [
                    ['key' => 'platforms.view', 'label' => 'View platforms', 'hint' => 'See the estate list and open a workspace.'],
                    ['key' => 'platforms.create', 'label' => 'Create platforms', 'hint' => 'Add a new tenant to Orora School.'],
                    ['key' => 'platforms.update', 'label' => 'Edit platforms', 'hint' => 'Change name, slug, logo and description.'],
                    ['key' => 'platforms.activate', 'label' => 'Activate or deactivate', 'hint' => 'Take a platform on or off the estate.'],
                ],
            ],
            [
                'area' => 'users',
                'label' => 'Users',
                'permissions' => [
                    ['key' => 'users.view', 'label' => 'View users', 'hint' => 'Open the directory and a person’s assignments.'],
                    ['key' => 'users.create', 'label' => 'Create users', 'hint' => 'Invite staff and learners onto the estate.'],
                    ['key' => 'users.update', 'label' => 'Edit users', 'hint' => 'Change name, email, district and account status.'],
                    ['key' => 'users.assign', 'label' => 'Assign to a platform', 'hint' => 'Attach a role on a platform (user_platform_roles).'],
                ],
            ],
            [
                'area' => 'roles',
                'label' => 'Roles and permissions',
                'permissions' => [
                    ['key' => 'roles.view', 'label' => 'View roles', 'hint' => 'See the catalogue and what each role can do.'],
                    ['key' => 'roles.create', 'label' => 'Create custom roles', 'hint' => 'System roles stay protected; custom ones can be added.'],
                    ['key' => 'roles.update', 'label' => 'Edit role permissions', 'hint' => 'Tick and untick keys on a custom role.'],
                ],
            ],
            [
                'area' => 'courses',
                'label' => 'Courses',
                'permissions' => [
                    ['key' => 'courses.view', 'label' => 'View courses', 'hint' => 'Browse the catalogue inside a workspace.'],
                    ['key' => 'courses.create', 'label' => 'Create courses', 'hint' => 'Open a draft in a platform workspace.'],
                    ['key' => 'courses.update', 'label' => 'Edit courses', 'hint' => 'Change structure, copy and settings.'],
                    ['key' => 'courses.publish', 'label' => 'Publish and archive', 'hint' => 'Move a course through the lifecycle.'],
                    ['key' => 'courses.review', 'label' => 'Submit and review', 'hint' => 'Send for review, or sign off a submission.'],
                ],
            ],
            [
                'area' => 'modules',
                'label' => 'Modules, lessons and quizzes',
                'permissions' => [
                    ['key' => 'modules.manage', 'label' => 'Manage modules', 'hint' => 'Add, reorder and retire modules.'],
                    ['key' => 'lessons.manage', 'label' => 'Manage lessons', 'hint' => 'Write lesson copy and attach media.'],
                    ['key' => 'quizzes.manage', 'label' => 'Manage quizzes', 'hint' => 'Author questions and passing scores.'],
                    ['key' => 'resources.manage', 'label' => 'Manage resources', 'hint' => 'Upload field sheets and handouts.'],
                ],
            ],
            [
                'area' => 'enrollments',
                'label' => 'Learners and enrolments',
                'permissions' => [
                    ['key' => 'enrollments.view', 'label' => 'View enrolments', 'hint' => 'See who is on a course and how far they have got.'],
                    ['key' => 'enrollments.manage', 'label' => 'Enrol and unenrol', 'hint' => 'Put a cohort on a course, or take them off.'],
                ],
            ],
            [
                'area' => 'certificates',
                'label' => 'Certificates',
                'permissions' => [
                    ['key' => 'certificates.view', 'label' => 'View certificates', 'hint' => 'See what has been issued, and to whom.'],
                    ['key' => 'certificates.issue', 'label' => 'Issue certificates', 'hint' => 'Mint a number against the global format.'],
                    ['key' => 'certificates.revoke', 'label' => 'Revoke certificates', 'hint' => 'Mark a certificate as no longer valid.'],
                ],
            ],
            [
                'area' => 'live_sessions',
                'label' => 'Live sessions',
                'permissions' => [
                    ['key' => 'live_sessions.view', 'label' => 'View live sessions', 'hint' => 'See the clinic calendar.'],
                    ['key' => 'live_sessions.manage', 'label' => 'Schedule live sessions', 'hint' => 'Create, edit and cancel clinics.'],
                ],
            ],
            [
                'area' => 'analytics',
                'label' => 'Analytics',
                'permissions' => [
                    ['key' => 'analytics.view', 'label' => 'View analytics', 'hint' => 'Open estate and platform reports.'],
                    ['key' => 'activity.view', 'label' => 'View activity logs', 'hint' => 'Read the audit trail.'],
                ],
            ],
            [
                'area' => 'settings',
                'label' => 'Settings',
                'permissions' => [
                    ['key' => 'settings.update', 'label' => 'Edit global settings', 'hint' => 'Certificate numbering, pagination, support contact.'],
                ],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        $keys = [];

        foreach (self::catalog() as $group) {
            foreach ($group['permissions'] as $permission) {
                $keys[] = $permission['key'];
            }
        }

        return $keys;
    }

    /**
     * @return list<string>
     */
    public static function forRole(string $role): array
    {
        $all = self::keys();

        return match ($role) {
            'super-admin' => $all,
            'platform-admin', 'platform-owner' => array_values(array_filter(
                $all,
                fn (string $key) => ! str_starts_with($key, 'platforms.create')
                    && $key !== 'platforms.activate'
                    && $key !== 'settings.update'
                    && $key !== 'roles.create',
            )),
            'content-manager', 'curriculum-lead' => [
                'courses.view', 'courses.create', 'courses.update', 'courses.publish', 'courses.review',
                'modules.manage', 'lessons.manage', 'quizzes.manage', 'resources.manage',
                'enrollments.view', 'certificates.view', 'live_sessions.view', 'live_sessions.manage',
                'analytics.view',
            ],
            'instructor' => [
                'courses.view', 'courses.update',
                'modules.manage', 'lessons.manage', 'quizzes.manage', 'resources.manage',
                'enrollments.view', 'live_sessions.view', 'live_sessions.manage',
            ],
            'reviewer' => [
                'courses.view', 'courses.review', 'enrollments.view', 'certificates.view',
            ],
            'field-coordinator' => [
                'courses.view', 'enrollments.view', 'enrollments.manage',
                'certificates.view', 'live_sessions.view',
            ],
            'certificate-officer' => [
                'courses.view', 'enrollments.view',
                'certificates.view', 'certificates.issue', 'certificates.revoke',
            ],
            'learner' => [],
            default => [],
        };
    }
}
