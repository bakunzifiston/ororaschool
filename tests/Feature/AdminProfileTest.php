<?php

namespace Tests\Feature;

use App\UserRole;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperAdmin();
    }

    public function test_super_admin_can_open_their_profile(): void
    {
        $admin = auth()->user();

        $this->get(route('admin.profile'))
            ->assertOk()
            ->assertSee('Profile', false)
            ->assertSee($admin->name, false)
            ->assertSee($admin->email, false)
            ->assertSee('Save profile', false);
    }

    public function test_super_admin_can_update_their_profile(): void
    {
        $admin = auth()->user();

        $this->from(route('admin.profile'))
            ->post(route('admin.profile.update'), [
                'name' => 'Updated Admin Name',
                'email' => 'updated.admin@ororaschool.rw',
                'district' => 'Huye',
            ])
            ->assertRedirect(route('admin.profile'))
            ->assertSessionHas('status', 'Your profile was saved.');

        $admin->refresh();

        $this->assertSame('Updated Admin Name', $admin->name);
        $this->assertSame('updated.admin@ororaschool.rw', $admin->email);
        $this->assertSame('Huye', $admin->district);
    }

    public function test_super_admin_can_change_their_password(): void
    {
        $admin = auth()->user();

        $this->from(route('admin.profile'))
            ->post(route('admin.profile.update'), [
                'name' => $admin->name,
                'email' => $admin->email,
                'district' => $admin->district ?: 'Kigali',
                'password' => 'new-password-12',
                'password_confirmation' => 'new-password-12',
            ])
            ->assertRedirect(route('admin.profile'))
            ->assertSessionHas('status');

        $admin->refresh();

        $this->assertTrue(Hash::check('new-password-12', $admin->password));
    }

    public function test_profile_update_requires_name_email_and_district(): void
    {
        $this->from(route('admin.profile'))
            ->post(route('admin.profile.update'), [])
            ->assertRedirect(route('admin.profile'))
            ->assertSessionHasErrors(['name', 'email', 'district']);
    }

    public function test_learners_cannot_open_the_admin_profile(): void
    {
        $this->actingAsRole(UserRole::Learner);

        $this->get(route('admin.profile'))->assertForbidden();
        $this->post(route('admin.profile.update'), [
            'name' => 'Nope',
            'email' => 'nope@example.com',
            'district' => 'Kigali',
        ])->assertForbidden();
    }

    public function test_account_menu_links_to_profile(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.profile'), false)
            ->assertSee('Profile', false);
    }
}
