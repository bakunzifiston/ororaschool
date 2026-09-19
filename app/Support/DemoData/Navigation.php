<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Single source of truth for navigation. routes/web.php registers placeholder
 * routes straight from these definitions, so a nav item and its route cannot
 * drift apart.
 *
 * `roles` is the hook for role-gated nav in a later phase. It is declarative
 * only — visibleFor() filters on the fixture persona and performs no
 * authorisation whatsoever.
 */
class Navigation
{
    /** Super Admin: whole-estate oversight. */
    public static function superAdmin(): array
    {
        return [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'path' => '/admin', 'icon' => 'gauge'],
            ['label' => 'Platforms', 'route' => 'admin.platforms', 'path' => '/admin/platforms', 'icon' => 'layers'],
            ['label' => 'Users', 'route' => 'admin.users', 'path' => '/admin/users', 'icon' => 'users'],
            ['label' => 'Roles', 'route' => 'admin.roles', 'path' => '/admin/roles', 'icon' => 'shield'],
            ['label' => 'Permissions', 'route' => 'admin.permissions', 'path' => '/admin/permissions', 'icon' => 'key', 'roles' => ['super-admin']],
            ['label' => 'Global Content', 'route' => 'admin.content', 'path' => '/admin/content', 'icon' => 'globe'],
            ['label' => 'Analytics', 'route' => 'admin.analytics', 'path' => '/admin/analytics', 'icon' => 'chart'],
            ['label' => 'Activity Logs', 'route' => 'admin.activity', 'path' => '/admin/activity', 'icon' => 'history'],
            ['label' => 'Settings', 'route' => 'admin.settings', 'path' => '/admin/settings', 'icon' => 'cog'],
        ];
    }

    /** Platform Workspace: one platform at a time. Paths carry {platform}. */
    public static function platformWorkspace(): array
    {
        return [
            ['label' => 'Dashboard', 'route' => 'workspace.dashboard', 'path' => '/workspace/{platform}', 'icon' => 'gauge'],
            ['label' => 'Courses', 'route' => 'workspace.courses', 'path' => '/workspace/{platform}/courses', 'icon' => 'book'],
            ['label' => 'Categories', 'route' => 'workspace.categories', 'path' => '/workspace/{platform}/categories', 'icon' => 'folder'],
            ['label' => 'Modules', 'route' => 'workspace.modules', 'path' => '/workspace/{platform}/modules', 'icon' => 'layers'],
            ['label' => 'Lessons', 'route' => 'workspace.lessons', 'path' => '/workspace/{platform}/lessons', 'icon' => 'play'],
            ['label' => 'Quizzes', 'route' => 'workspace.quizzes', 'path' => '/workspace/{platform}/quizzes', 'icon' => 'quiz'],
            ['label' => 'Resources', 'route' => 'workspace.resources', 'path' => '/workspace/{platform}/resources', 'icon' => 'file'],
            ['label' => 'Instructors', 'route' => 'workspace.instructors', 'path' => '/workspace/{platform}/instructors', 'icon' => 'teacher'],
            ['label' => 'Learners', 'route' => 'workspace.learners', 'path' => '/workspace/{platform}/learners', 'icon' => 'users'],
            ['label' => 'Certificates', 'route' => 'workspace.certificates', 'path' => '/workspace/{platform}/certificates', 'icon' => 'award'],
            ['label' => 'Live Sessions', 'route' => 'workspace.sessions', 'path' => '/workspace/{platform}/live-sessions', 'icon' => 'video'],
            ['label' => 'Analytics', 'route' => 'workspace.analytics', 'path' => '/workspace/{platform}/analytics', 'icon' => 'chart'],
        ];
    }

    /** Learner: top nav, no sidebar. */
    public static function learner(): array
    {
        return [
            ['label' => 'Dashboard', 'route' => 'learner.dashboard', 'path' => '/learn', 'icon' => 'gauge'],
            ['label' => 'My Courses', 'route' => 'learner.courses', 'path' => '/learn/courses', 'icon' => 'book'],
            ['label' => 'Learning Paths', 'route' => 'learner.paths', 'path' => '/learn/paths', 'icon' => 'path'],
            ['label' => 'Certificates', 'route' => 'learner.certificates', 'path' => '/learn/certificates', 'icon' => 'award'],
            ['label' => 'Live Sessions', 'route' => 'learner.sessions', 'path' => '/learn/live-sessions', 'icon' => 'video'],
            ['label' => 'Resources', 'route' => 'learner.resources', 'path' => '/learn/resources', 'icon' => 'file'],
            ['label' => 'Profile', 'route' => 'learner.profile', 'path' => '/learn/profile', 'icon' => 'user'],
        ];
    }

    public static function for(string $experience): array
    {
        return match ($experience) {
            'super-admin' => self::superAdmin(),
            'platform-workspace' => self::platformWorkspace(),
            'learner' => self::learner(),
            default => [],
        };
    }

    /**
     * Filters nav by the fixture persona's role. Presentation-only: this is not
     * an authorisation check and must not be mistaken for one.
     */
    public static function visibleFor(string $experience, array $user): array
    {
        return array_values(array_filter(
            self::for($experience),
            fn (array $item) => ! isset($item['roles']) || in_array($user['role'], $item['roles'], true),
        ));
    }

    /**
     * Label for a route name, used by the placeholder pages and breadcrumbs.
     */
    public static function labelForRoute(string $routeName): string
    {
        foreach (['super-admin', 'platform-workspace', 'learner'] as $experience) {
            foreach (self::for($experience) as $item) {
                if ($item['route'] === $routeName) {
                    return $item['label'];
                }
            }
        }

        return 'Page';
    }

    /**
     * When the workspace switcher is used on a nested screen (course edit,
     * quiz builder), drop back to that section's index so the other platform
     * is not asked for a record that does not exist there.
     */
    public static function workspaceSectionRoute(?string $routeName): string
    {
        $routeName ??= 'workspace.dashboard';

        foreach (self::platformWorkspace() as $item) {
            if ($item['route'] === $routeName || str_starts_with($routeName, $item['route'].'.')) {
                return $item['route'];
            }
        }

        return 'workspace.dashboard';
    }
}
