<?php

namespace Tests\Feature;

use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;
use Tests\TestCase;

class SuperAdminPagesTest extends TestCase
{
    public function test_dashboard_renders_the_required_estate_stats(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Platforms', false)
            ->assertSee('Users', false)
            ->assertSee('Overall completion', false)
            ->assertSee('Recent activity', false)
            ->assertSee('Platforms at a glance', false)
            ->assertSee('Dashboard', false)
            ->assertSee('Add a platform', false);
    }

    public function test_dashboard_activity_empty_state_renders_when_the_fixture_is_empty(): void
    {
        $this->get(route('admin.dashboard', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No activity on the estate yet', false);
    }

    public function test_platforms_list_paginates_and_opens_the_edit_form(): void
    {
        $this->get(route('admin.platforms'))
            ->assertOk()
            ->assertSee('OroraFarm', false)
            ->assertSee('Page', false)
            ->assertSee('of', false)
            ->assertSee('Next', false);

        $this->get(route('admin.platforms', ['page' => 2]))
            ->assertOk()
            ->assertSee('Ishyiga', false);

        $this->get(route('admin.platforms.edit', 'gemura'))
            ->assertOk()
            ->assertSee('Edit Gemura', false)
            ->assertSee('Platform is active', false);
    }

    public function test_platforms_list_uses_the_empty_state_when_the_fixture_is_empty(): void
    {
        $this->get(route('admin.platforms', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No platforms on the estate yet', false)
            ->assertDontSee('Ubworozi', false)
            ->assertDontSee('Ishyiga', false);
    }

    public function test_activate_confirm_posts_and_flashes(): void
    {
        $this->from(route('admin.platforms'))
            ->post(route('admin.platforms.toggle', 'ubworozi'))
            ->assertRedirect(route('admin.platforms'))
            ->assertSessionHas('status');
    }

    public function test_users_list_filters_by_platform_and_paginates(): void
    {
        $this->get(route('admin.users'))
            ->assertOk()
            ->assertSee('Jean-Baptiste Habimana', false)
            ->assertSee('Next', false);

        $this->get(route('admin.users', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Jean-Baptiste Habimana', false)
            ->assertDontSee('Aline Mukamana', false);
    }

    public function test_users_list_uses_the_empty_state_when_the_fixture_is_empty(): void
    {
        $this->get(route('admin.users', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No users on the estate yet', false);
    }

    public function test_user_detail_shows_per_platform_role_assignments(): void
    {
        $jean = collect(People::directory())->firstWhere('name', 'Jean-Baptiste Habimana');

        $this->get(route('admin.users.show', $jean['id']))
            ->assertOk()
            ->assertSee('Gemura', false)
            ->assertSee('Platform Admin', false)
            ->assertSee('OroraFarm', false)
            ->assertSee('Instructor', false);
    }

    public function test_claudine_mirrors_the_multi_platform_assignment_pattern(): void
    {
        $claudine = collect(People::directory())->firstWhere('name', 'Claudine Uwimana');

        $this->get(route('admin.users.show', $claudine['id']))
            ->assertOk()
            ->assertSee('BuchaPro', false)
            ->assertSee('Platform Admin', false)
            ->assertSee('Content Manager', false);
    }

    public function test_roles_list_marks_system_roles_paginates_and_opens_the_permission_editor(): void
    {
        $this->get(route('admin.roles'))
            ->assertOk()
            ->assertSee('System-protected', false)
            ->assertSee('Super Admin', false)
            ->assertSee('Next', false);

        $this->get(route('admin.roles', ['page' => 2]))
            ->assertOk()
            ->assertSee('Certificate Officer', false)
            ->assertSee('Custom', false);

        $this->get(route('admin.roles.edit', 'content-manager'))
            ->assertOk()
            ->assertSee('courses.publish', false)
            ->assertSee('platforms.*', false)
            ->assertSee('System-protected — permissions are fixed', false);
    }

    public function test_roles_list_uses_the_empty_state_when_the_fixture_is_empty(): void
    {
        $this->get(route('admin.roles', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No roles defined', false);
    }

    public function test_permissions_page_lists_the_catalog_by_group(): void
    {
        $this->get(route('admin.permissions'))
            ->assertOk()
            ->assertSee('platforms.view', false)
            ->assertSee('courses.publish', false)
            ->assertSee('certificates.issue', false);
    }

    public function test_permissions_page_uses_the_empty_state_when_the_fixture_is_empty(): void
    {
        $this->get(route('admin.permissions', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No permissions in the catalogue', false)
            ->assertDontSee('platforms.view', false);
    }

    public function test_global_content_links_into_a_workspace_and_paginates(): void
    {
        $this->get(route('admin.content'))
            ->assertOk()
            ->assertSee('Mastitis Detection', false)
            ->assertSee(route('workspace.courses', ['platform' => 'gemura']), false)
            ->assertSee('Next', false);

        $this->get(route('admin.content', ['empty' => 1]))
            ->assertOk()
            ->assertSee('Nothing published on any platform yet', false);
    }

    public function test_analytics_renders_charts_and_the_platform_comparison(): void
    {
        $this->get(route('admin.analytics'))
            ->assertOk()
            ->assertSee('Users over time', false)
            ->assertSee('Enrolments over time', false)
            ->assertSee('Completion rate trend', false)
            ->assertSee('Certificates issued', false)
            ->assertSee('Platform comparison', false)
            ->assertSee('Gemura', false);
    }

    public function test_activity_logs_filter_and_empty_state(): void
    {
        $this->get(route('admin.activity'))
            ->assertOk()
            ->assertSee('published a course', false)
            ->assertSee('assigned Content Manager', false)
            ->assertSee('created a platform', false)
            ->assertSee('Next', false);

        $this->get(route('admin.activity', ['action' => 'role.assigned']))
            ->assertOk()
            ->assertSee('assigned', false)
            ->assertDontSee('published a course', false);

        $this->get(route('admin.activity', ['empty' => 1]))
            ->assertOk()
            ->assertSee('The trail is empty', false);
    }

    public function test_settings_form_posts_and_flashes(): void
    {
        $this->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Certificate numbering format', false)
            ->assertSee('Default pagination size', false)
            ->assertSee('help@ororaschool.rw', false);

        $this->from(route('admin.settings'))
            ->post(route('admin.settings.update'))
            ->assertRedirect(route('admin.settings'))
            ->assertSessionHas('status');
    }

    public function test_unknown_platform_and_user_are_not_found(): void
    {
        $this->get(route('admin.platforms.edit', 'missing'))->assertNotFound();
        $this->get(route('admin.users.show', 9999))->assertNotFound();
        $this->get(route('admin.roles.edit', 'not-a-role'))->assertNotFound();
    }

    public function test_every_current_platform_still_has_a_workspace(): void
    {
        foreach (Platforms::slugs() as $slug) {
            $this->get(route('workspace.dashboard', ['platform' => $slug]))->assertOk();
        }
    }
}
