<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\User;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;
use App\UserRole;
use Database\Seeders\CatalogSeeder;
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
            ->assertDontSee('Course completion', false)
            ->assertDontSee('Academy distribution', false)
            ->assertSee('Catalogue status', false)
            ->assertSee('Users by academy', false)
            ->assertSee('Quick actions', false)
            ->assertSee('Dashboard', false)
            ->assertSee('Add academy', false)
            ->assertSee('View', false)
            ->assertSee('Edit', false)
            ->assertSee(route('workspace.dashboard', ['platform' => 'gemura']), false)
            ->assertSee(route('admin.platforms.edit', 'gemura'), false);
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
            ->assertSee('View', false)
            ->assertSee('Edit', false)
            ->assertSee('Delete', false)
            ->assertSee('Delete OroraFarm?', false)
            ->assertSee(route('workspace.dashboard', ['platform' => 'ororafarm']), false)
            ->assertSee(route('admin.platforms.edit', 'ororafarm'), false)
            ->assertSee(route('admin.platforms.destroy', 'ororafarm'), false)
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

        $this->get(route('admin.platforms.create'))
            ->assertOk()
            ->assertSee('Add an academy', false)
            ->assertSee('Academy admin', false)
            ->assertSee('name="admin_email"', false);
    }

    public function test_super_admin_creates_an_academy_and_its_admin(): void
    {
        $this->from(route('admin.platforms.create'))
            ->post(route('admin.platforms.store'), [
                'name' => 'Kivu Tea',
                'slug' => 'kivu-tea',
                'description' => 'Tea husbandry for the Western Province.',
                'active' => '1',
                'admin_name' => 'Diane Uwamahoro',
                'admin_email' => 'diane.uwamahoro@ororaschool.rw',
                'admin_password' => 'password12',
                'admin_password_confirmation' => 'password12',
                'admin_district' => 'Nyabihu',
            ])
            ->assertRedirect(route('admin.platforms'))
            ->assertSessionHas('status');

        $platform = Platform::query()->where('slug', 'kivu-tea')->first();
        $admin = User::query()->where('email', 'diane.uwamahoro@ororaschool.rw')->first();

        $this->assertNotNull($platform);
        $this->assertSame('Kivu Tea', $platform->name);
        $this->assertSame('active', $platform->status);
        $this->assertNotNull($admin);
        $this->assertSame(UserRole::PlatformStaff, $admin->role);
        $this->assertSame(['kivu-tea'], $admin->platforms()->pluck('slug')->all());
        $this->assertTrue($admin->canAccessWorkspace('kivu-tea'));
        $this->assertFalse($admin->canAccessWorkspace('gemura'));

        $this->get(route('admin.platforms', ['page' => 2]))
            ->assertOk()
            ->assertSee('Kivu Tea', false);

        $this->get(route('workspace.dashboard', ['platform' => 'kivu-tea']))
            ->assertOk()
            ->assertSee('Kivu Tea', false)
            ->assertDontSee('Mastitis Detection', false);

        $this->actingAs($admin);

        $this->get(route('workspace.dashboard', ['platform' => 'kivu-tea']))
            ->assertOk()
            ->assertSee('Kivu Tea', false)
            ->assertDontSee('Mastitis Detection', false);

        $this->get(route('workspace.courses', ['platform' => 'kivu-tea']))
            ->assertOk()
            ->assertDontSee('Mastitis Detection', false);

        $this->get(route('workspace.dashboard', ['platform' => 'gemura']))
            ->assertNotFound();

        $this->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_saving_an_academy_writes_the_record(): void
    {
        $this->from(route('admin.platforms.edit', 'ishyiga'))
            ->post(route('admin.platforms.update', 'ishyiga'), [
                'name' => 'Ishyiga',
                'description' => 'Hive records for the Western Province.',
                'active' => '1',
            ])
            ->assertRedirect(route('admin.platforms'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('platforms', [
            'slug' => 'ishyiga',
            'name' => 'Ishyiga',
            'description' => 'Hive records for the Western Province.',
            'status' => 'active',
        ]);
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
            ->assertSessionHas('status', 'Ubworozi activated.');

        $this->assertDatabaseHas('platforms', [
            'slug' => 'ubworozi',
            'status' => 'active',
        ]);

        $this->get(route('admin.platforms'))
            ->assertOk()
            ->assertSee('Deactivate Ubworozi?', false);
    }

    public function test_deactivating_an_academy_hides_it_from_the_public_catalogue(): void
    {
        $this->seed(CatalogSeeder::class);

        $this->from(route('admin.platforms'))
            ->post(route('admin.platforms.toggle', 'gemura'))
            ->assertRedirect(route('admin.platforms'))
            ->assertSessionHas('status', 'Gemura deactivated.');

        $this->assertDatabaseHas('platforms', [
            'slug' => 'gemura',
            'status' => 'inactive',
        ]);

        $this->get(route('admin.platforms'))
            ->assertOk()
            ->assertSee('Activate Gemura?', false);

        $this->get(route('catalog.platforms'))
            ->assertOk()
            ->assertDontSee('Gemura', false);

        $this->get(route('catalog.platforms.show', ['platform' => 'gemura']))
            ->assertNotFound();
    }

    public function test_deleting_an_academy_hides_it_and_leaves_certificates_resolvable(): void
    {
        $this->seed(CatalogSeeder::class);

        $this->from(route('admin.platforms'))
            ->delete(route('admin.platforms.destroy', 'gemura'))
            ->assertRedirect(route('admin.platforms'))
            ->assertSessionHas('status', 'Gemura was removed.');

        $this->assertDatabaseHas('platforms', [
            'slug' => 'gemura',
            'status' => 'deleted',
        ]);

        $this->get(route('admin.platforms'))
            ->assertOk()
            ->assertDontSee('Delete Gemura?', false)
            ->assertDontSee(route('admin.platforms.destroy', 'gemura'), false);

        $this->get(route('admin.platforms.edit', 'gemura'))->assertNotFound();
        $this->post(route('admin.platforms.toggle', 'gemura'))->assertNotFound();
        $this->delete(route('admin.platforms.destroy', 'gemura'))->assertNotFound();
        $this->get(route('workspace.dashboard', ['platform' => 'gemura']))->assertNotFound();

        $this->get(route('catalog.platforms'))
            ->assertOk()
            ->assertDontSee('Gemura', false);

        $this->get(route('catalog.platforms.show', ['platform' => 'gemura']))
            ->assertNotFound();

        $this->get(route('certificates.verify', ['code' => 'OS-GEM-2026-1847']))
            ->assertOk()
            ->assertSee('Certificate verified', false)
            ->assertSee('Gemura', false);
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
            ->assertSee('View', false)
            ->assertSee('Edit', false)
            ->assertSee(route('admin.roles.show', 'super-admin'), false)
            ->assertSee(route('admin.roles.edit', 'super-admin'), false)
            ->assertSee('Delete', false)
            ->assertSee('Delete Super Admin?', false)
            ->assertSee('Next', false);

        $this->get(route('admin.roles', ['page' => 2]))
            ->assertOk()
            ->assertSee('Certificate Officer', false)
            ->assertSee('Custom', false)
            ->assertSee('Delete', false)
            ->assertSee('Delete Certificate Officer?', false);

        $this->get(route('admin.roles.edit', 'content-manager'))
            ->assertOk()
            ->assertSee('courses.publish', false)
            ->assertSee('platforms.*', false)
            ->assertSee('System-protected — permissions are fixed', false);
    }

    public function test_role_view_shows_granted_permissions(): void
    {
        $this->get(route('admin.roles.show', 'content-manager'))
            ->assertOk()
            ->assertSee('Content Manager', false)
            ->assertSee('Granted permissions', false)
            ->assertSee('courses.publish', false)
            ->assertDontSee('platforms.view', false);
    }

    public function test_deleting_a_custom_role_hides_it(): void
    {
        $this->from(route('admin.roles', ['page' => 2]))
            ->delete(route('admin.roles.destroy', 'reviewer'))
            ->assertRedirect(route('admin.roles'))
            ->assertSessionHas('status', 'Reviewer was removed.');

        $this->assertDatabaseHas('removed_roles', [
            'key' => 'reviewer',
        ]);

        $this->get(route('admin.roles', ['page' => 2]))
            ->assertOk()
            ->assertDontSee('Delete Reviewer?', false)
            ->assertDontSee(route('admin.roles.destroy', 'reviewer'), false);

        $this->get(route('admin.roles.show', 'reviewer'))->assertNotFound();
        $this->get(route('admin.roles.edit', 'reviewer'))->assertNotFound();
        $this->delete(route('admin.roles.destroy', 'reviewer'))->assertNotFound();
    }

    public function test_deleting_a_system_role_hides_it(): void
    {
        $this->from(route('admin.roles'))
            ->delete(route('admin.roles.destroy', 'instructor'))
            ->assertRedirect(route('admin.roles'))
            ->assertSessionHas('status', 'Instructor was removed.');

        $this->assertDatabaseHas('removed_roles', [
            'key' => 'instructor',
        ]);

        $this->get(route('admin.roles'))
            ->assertOk()
            ->assertDontSee('Delete Instructor?', false);

        $this->get(route('admin.roles.show', 'instructor'))->assertNotFound();
        $this->get(route('admin.roles.edit', 'instructor'))->assertNotFound();
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
            ->assertSee(route('workspace.courses.show', ['platform' => 'gemura', 'course' => 'mastitis-milk-hygiene']), false)
            ->assertSee(route('workspace.courses.edit', ['platform' => 'gemura', 'course' => 'mastitis-milk-hygiene']), false)
            ->assertSee('View', false)
            ->assertSee('Edit', false)
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
        $this->post(route('admin.platforms.toggle', 'missing'))->assertNotFound();
        $this->delete(route('admin.platforms.destroy', 'missing'))->assertNotFound();
        $this->get(route('admin.users.show', 9999))->assertNotFound();
        $this->get(route('admin.roles.show', 'not-a-role'))->assertNotFound();
        $this->get(route('admin.roles.edit', 'not-a-role'))->assertNotFound();
        $this->delete(route('admin.roles.destroy', 'not-a-role'))->assertNotFound();
    }

    public function test_every_current_platform_still_has_a_workspace(): void
    {
        foreach (Platforms::slugs() as $slug) {
            $this->get(route('workspace.dashboard', ['platform' => $slug]))->assertOk();
        }
    }
}
