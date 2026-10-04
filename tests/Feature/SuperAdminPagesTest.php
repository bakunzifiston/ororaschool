<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;
use App\UserRole;
use Tests\TestCase;

class SuperAdminPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperAdmin();
    }

    public function test_dashboard_renders_the_required_estate_stats(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Real-time overview of the FarmSchool learning ecosystem.', false)
            ->assertSee('Total Academies', false)
            ->assertSee('Total Users', false)
            ->assertSee('Active Learners', false)
            ->assertSee('Completion Rate', false)
            ->assertSee('Recent activity', false)
            ->assertSee('Academy overview', false)
            ->assertSee('Needs attention', false)
            ->assertSee('Course completion', false)
            ->assertSee('Quick actions', false)
            ->assertSee('Dashboard', false)
            ->assertSee('Add academy', false);
    }

    public function test_dashboard_activity_empty_state_renders_when_the_fixture_is_empty(): void
    {
        $this->get(route('admin.dashboard', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No activity on the estate yet', false);
    }

    public function test_dashboard_surfaces_items_that_need_attention(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('awaiting review', false)
            ->assertSee('inactive', false)
            ->assertSee('waiting to be activated', false)
            ->assertDontSee('Everything is up to date.', false);
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
            ->assertSee('Academy is active', false);
    }

    public function test_platforms_list_uses_the_empty_state_when_the_fixture_is_empty(): void
    {
        $this->get(route('admin.platforms', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No academies on the estate yet', false)
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
            ->assertSee('View', false)
            ->assertSee('Edit', false)
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

    public function test_create_user_form_lists_platform_dashboards(): void
    {
        $this->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Create a user', false)
            ->assertSee('Academy dashboards', false)
            ->assertSee('OroraFarm', false)
            ->assertSee('Gemura', false)
            ->assertSee('Create user', false);
    }

    public function test_super_admin_creates_staff_with_selected_platform_dashboards(): void
    {
        $response = $this->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => 'Josiane Kayitesi',
                'email' => 'josiane.kayitesi@ororaschool.rw',
                'password' => 'password12',
                'password_confirmation' => 'password12',
                'district' => 'Gicumbi',
                'role' => UserRole::PlatformStaff->value,
                'status' => 'active',
                'platforms' => ['gemura', 'feedgrid'],
            ]);

        $user = User::query()->where('email', 'josiane.kayitesi@ororaschool.rw')->first();

        $this->assertNotNull($user);
        $response->assertRedirect(route('admin.accounts.show', $user))
            ->assertSessionHas('status');
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertSame(UserRole::PlatformStaff, $user->role);
        $this->assertSame(
            ['feedgrid', 'gemura'],
            $user->platforms()->orderBy('slug')->pluck('slug')->all(),
        );
        $this->assertTrue($user->canAccessWorkspace('gemura'));
        $this->assertFalse($user->canAccessWorkspace('ororafarm'));
    }

    public function test_platform_staff_must_be_given_at_least_one_dashboard(): void
    {
        $this->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => 'No Platform Staff',
                'email' => 'no.platform@ororaschool.rw',
                'password' => 'password12',
                'password_confirmation' => 'password12',
                'district' => 'Kigali',
                'role' => UserRole::PlatformStaff->value,
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrors('platforms');

        $this->assertDatabaseMissing('users', ['email' => 'no.platform@ororaschool.rw']);
    }

    public function test_created_staff_can_open_only_assigned_workspaces(): void
    {
        $this->post(route('admin.users.store'), [
            'name' => 'Diane Iradukunda',
            'email' => 'diane.workspace@ororaschool.rw',
            'password' => 'password12',
            'password_confirmation' => 'password12',
            'district' => 'Kamonyi',
            'role' => UserRole::PlatformStaff->value,
            'status' => 'active',
            'platforms' => ['feedgrid'],
        ]);

        $staff = User::query()->where('email', 'diane.workspace@ororaschool.rw')->first();

        $this->assertNotNull($staff);

        $this->actingAs($staff)
            ->get(route('workspace.dashboard', ['platform' => 'feedgrid']))
            ->assertOk()
            ->assertSee(route('workspace.dashboard', ['platform' => 'feedgrid']), false)
            ->assertDontSee(route('workspace.dashboard', ['platform' => 'gemura']), false);

        $this->actingAs($staff)
            ->get(route('workspace.dashboard', ['platform' => 'gemura']))
            ->assertNotFound();
    }

    public function test_created_user_can_be_viewed_edited_and_deleted(): void
    {
        $staff = User::factory()->platformStaff()->create([
            'name' => 'Account Actions Staff',
            'email' => 'account.actions@ororaschool.rw',
            'district' => 'Huye',
        ]);

        $this->get(route('admin.users'))
            ->assertOk()
            ->assertSee('Account Actions Staff', false)
            ->assertSee(route('admin.accounts.show', $staff), false)
            ->assertSee(route('admin.accounts.edit', $staff), false)
            ->assertSee('Delete', false);

        $this->get(route('admin.accounts.show', $staff))
            ->assertOk()
            ->assertSee('Account Actions Staff', false)
            ->assertSee('account.actions@ororaschool.rw', false)
            ->assertSee('Delete', false);

        $this->get(route('admin.accounts.edit', $staff))
            ->assertOk()
            ->assertSee('Edit Account Actions Staff', false)
            ->assertSee('Academy dashboards', false);

        $this->from(route('admin.accounts.edit', $staff))
            ->post(route('admin.accounts.update', $staff), [
                'name' => 'Account Actions Updated',
                'email' => 'account.actions@ororaschool.rw',
                'district' => 'Huye',
                'role' => UserRole::PlatformStaff->value,
                'status' => 'active',
                'platforms' => ['gemura'],
            ])
            ->assertRedirect(route('admin.accounts.show', $staff))
            ->assertSessionHas('status');

        $staff->refresh();

        $this->assertSame('Account Actions Updated', $staff->name);
        $this->assertSame(['gemura'], $staff->platforms()->orderBy('slug')->pluck('slug')->all());

        $this->from(route('admin.accounts.show', $staff))
            ->delete(route('admin.accounts.destroy', $staff))
            ->assertRedirect(route('admin.users'))
            ->assertSessionHas('status');

        $this->assertModelMissing($staff);
    }

    public function test_a_super_admin_cannot_delete_their_own_account(): void
    {
        $admin = auth()->user();

        $this->delete(route('admin.accounts.destroy', $admin))
            ->assertForbidden();

        $this->assertModelExists($admin);
    }

    public function test_user_detail_shows_per_platform_role_assignments(): void
    {
        $jean = collect(People::directory())->firstWhere('name', 'Jean-Baptiste Habimana');

        $this->get(route('admin.users.show', $jean['id']))
            ->assertOk()
            ->assertSee('Gemura', false)
            ->assertSee('Academy Admin', false)
            ->assertSee('OroraFarm', false)
            ->assertSee('Instructor', false);
    }

    public function test_claudine_mirrors_the_multi_platform_assignment_pattern(): void
    {
        $claudine = collect(People::directory())->firstWhere('name', 'Claudine Uwimana');

        $this->get(route('admin.users.show', $claudine['id']))
            ->assertOk()
            ->assertSee('BuchaPro', false)
            ->assertSee('Academy Admin', false)
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
            ->assertSee('Nothing published on any academy yet', false);
    }

    public function test_analytics_renders_charts_and_the_platform_comparison(): void
    {
        $this->get(route('admin.analytics'))
            ->assertOk()
            ->assertSee('Users over time', false)
            ->assertSee('Enrolments over time', false)
            ->assertSee('Completion rate trend', false)
            ->assertSee('Certificates issued', false)
            ->assertSee('Academy comparison', false)
            ->assertSee('Gemura', false);
    }

    public function test_activity_logs_filter_and_empty_state(): void
    {
        $this->get(route('admin.activity'))
            ->assertOk()
            ->assertSee('published a course', false)
            ->assertSee('assigned Content Manager', false)
            ->assertSee('created an academy', false)
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
