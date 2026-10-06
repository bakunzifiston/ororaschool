<?php

namespace Tests\Feature;

use App\Models\Platform;
use App\Models\User;
use App\Support\DemoData\Platforms;
use App\UserRole;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthPagesTest extends TestCase
{
    /** @return array<string, array{0: string}> */
    public static function guestRoutes(): array
    {
        return [
            'login' => ['login'],
            'register' => ['register'],
            'forgot password' => ['password.request'],
        ];
    }

    #[DataProvider('guestRoutes')]
    public function test_each_guest_page_renders_in_the_guest_layout(string $name): void
    {
        $this->get(route($name))
            ->assertOk()
            ->assertSee('data-experience="guest"', false)
            ->assertDontSee('<aside', false)
            ->assertDontSee('Sign out</span>', false);
    }

    public function test_reset_password_page_renders_with_a_token(): void
    {
        $this->get(route('password.reset', ['token' => 'fixture-token-9f2c']))
            ->assertOk()
            ->assertSee('data-experience="guest"', false)
            ->assertSee('fixture-token-9f2c', false)
            ->assertSee('Step 2 of 2', false);
    }

    public function test_recovery_is_presented_as_a_two_step_flow(): void
    {
        $this->get(route('password.request'))->assertSee('Step 1 of 2', false);
        $this->get(route('password.reset', ['token' => 'abc']))->assertSee('Step 2 of 2', false);
    }

    public function test_login_page_carries_the_fields_and_links_it_needs(): void
    {
        $response = $this->get(route('login'));

        $response->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('autocomplete="current-password"', false)
            ->assertSee(route('password.request'), false)
            ->assertSee(route('register'), false)
            ->assertSee('Create an account', false)
            ->assertDontSee('See how to get access', false)
            ->assertDontSee('FarmSchool is the training and certification arm of the Orora academies', false)
            ->assertDontSee('Use the account your academy coordinator set up for you', false)
            ->assertDontSee('name="preview_as"', false)
            ->assertDontSee('Temporary — no authentication in this build', false);
    }

    public function test_a_verified_learner_is_signed_in_and_sent_to_their_dashboard(): void
    {
        $user = User::factory()->learner()->create([
            'password' => 'password12',
        ]);

        $this->from(route('login'))
            ->post(route('login.attempt'), [
                'email' => $user->email,
                'password' => 'password12',
            ])
            ->assertRedirect(route('learner.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_verified_super_admin_is_sent_to_the_estate_dashboard(): void
    {
        $user = User::factory()->superAdmin()->create([
            'password' => 'password12',
        ]);

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password12',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_platform_staff_are_sent_to_their_assigned_workspace(): void
    {
        $user = User::factory()->platformStaff()->create([
            'password' => 'password12',
        ]);
        $user->platforms()->sync([
            Platform::firstOrCreateFromSlug('buchapro')->id,
        ]);

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password12',
        ])->assertRedirect(route('workspace.dashboard', ['platform' => 'buchapro']));

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_credentials_are_rejected_without_signing_anyone_in(): void
    {
        $user = User::factory()->create([
            'password' => 'password12',
        ]);

        $this->from(route('login'))
            ->post(route('login.attempt'), [
                'email' => $user->email,
                'password' => 'not-the-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_an_inactive_account_is_rejected_with_the_same_error_as_wrong_credentials(): void
    {
        $user = User::factory()->inactive()->create([
            'password' => 'password12',
        ]);

        $this->from(route('login'))
            ->post(route('login.attempt'), [
                'email' => $user->email,
                'password' => 'password12',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_an_empty_login_is_rejected(): void
    {
        $this->from(route('login'))
            ->post(route('login.attempt'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_guests_are_sent_to_sign_in_from_the_dashboards(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('workspace.dashboard', ['platform' => 'gemura']))->assertRedirect(route('login'));
        $this->get(route('learner.dashboard'))->assertRedirect(route('login'));
    }

    public function test_a_learner_cannot_open_staff_shells(): void
    {
        $this->actingAsLearner();

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('workspace.dashboard', ['platform' => 'gemura']))->assertForbidden();
    }

    public function test_platform_staff_cannot_open_the_estate_or_learner_shells(): void
    {
        $this->actingAsPlatformStaff();

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('learner.dashboard'))->assertForbidden();
    }

    public function test_a_super_admin_can_open_a_platform_workspace(): void
    {
        $this->actingAsSuperAdmin()
            ->get(route('workspace.dashboard', ['platform' => 'gemura']))
            ->assertOk();
    }

    public function test_platform_staff_cannot_open_a_workspace_they_were_not_given(): void
    {
        $this->actingAsPlatformStaff()
            ->get(route('workspace.dashboard', ['platform' => 'ororafarm']))
            ->assertNotFound();
    }

    public function test_platform_staff_cannot_create_estate_users(): void
    {
        $this->actingAsPlatformStaff()
            ->post(route('admin.users.store'), [
                'name' => 'Blocked Staff',
                'email' => 'blocked.staff@ororaschool.rw',
                'password' => 'password12',
                'password_confirmation' => 'password12',
                'district' => 'Kigali',
                'role' => UserRole::PlatformStaff->value,
                'status' => 'active',
                'platforms' => ['gemura'],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'blocked.staff@ororaschool.rw']);
    }

    public function test_platform_staff_cannot_open_or_delete_estate_accounts(): void
    {
        $account = User::factory()->platformStaff()->create([
            'email' => 'protected.account@ororaschool.rw',
        ]);

        $this->actingAsPlatformStaff();

        $this->get(route('admin.accounts.show', $account))->assertForbidden();
        $this->get(route('admin.accounts.edit', $account))->assertForbidden();
        $this->post(route('admin.accounts.update', $account), [
            'name' => $account->name,
            'email' => $account->email,
            'district' => $account->district,
            'role' => UserRole::PlatformStaff->value,
            'status' => 'active',
            'platforms' => ['gemura'],
        ])->assertForbidden();
        $this->delete(route('admin.accounts.destroy', $account))->assertForbidden();

        $this->assertModelExists($account);
    }

    public function test_platform_staff_cannot_delete_an_academy(): void
    {
        $this->actingAsPlatformStaff()
            ->delete(route('admin.platforms.destroy', 'gemura'))
            ->assertForbidden();

        $this->assertDatabaseMissing('platforms', [
            'slug' => 'gemura',
            'status' => 'deleted',
        ]);
    }

    public function test_platform_staff_cannot_view_or_delete_a_role(): void
    {
        $this->actingAsPlatformStaff();

        $this->get(route('admin.roles.show', 'reviewer'))->assertForbidden();
        $this->delete(route('admin.roles.destroy', 'reviewer'))->assertForbidden();

        $this->assertDatabaseMissing('removed_roles', [
            'key' => 'reviewer',
        ]);
    }

    public function test_an_unverified_learner_is_sent_to_confirm_their_email(): void
    {
        $user = User::factory()->unverified()->learner()->create();

        $this->actingAs($user)
            ->get(route('learner.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_registration_does_not_offer_academy_linking(): void
    {
        $response = $this->get(route('register'));

        foreach (Platforms::active() as $platform) {
            $response->assertDontSee('Continue with '.$platform['name'], false);
        }

        $response->assertSee(route('register.store'), false)
            ->assertSee('First name', false)
            ->assertSee('Last name', false)
            ->assertSee('District', false)
            ->assertSee('Sector', false)
            ->assertDontSee('you work in', false)
            ->assertSee('Bugesera', false)
            ->assertSee('Nyarugenge', false)
            ->assertSee('Kabarore', false)
            ->assertDontSee('Full name', false)
            ->assertDontSee('Not on any of those yet?', false);
    }

    public function test_direct_registration_creates_a_learner_and_sends_them_to_the_courses_dashboard(): void
    {
        Notification::fake();
        $this->seed(CatalogSeeder::class);

        $this->post(route('register.store'), [
            'first_name' => 'Placide',
            'last_name' => 'Bizimana',
            'district' => 'Gatsibo',
            'sector' => 'Kabarore',
            'email' => 'new.learner@umuhinzi.rw',
            'password' => 'password12',
        ])
            ->assertRedirect(route('learner.courses'))
            ->assertSessionMissing('status');

        $user = User::query()->where('email', 'new.learner@umuhinzi.rw')->first();

        $this->assertNotNull($user);
        $this->assertSame('Placide Bizimana', $user->name);
        $this->assertSame('Gatsibo', $user->district);
        $this->assertSame('Kabarore', $user->sector);
        $this->assertSame(UserRole::Learner, $user->role);
        $this->assertSame('active', $user->status);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertAuthenticatedAs($user);

        Notification::assertNothingSent();

        $this->get(route('learner.courses'))
            ->assertOk()
            ->assertDontSee('Confirm your email address', false)
            ->assertDontSee('Send the link again', false)
            ->assertSee('Evening Intake and Lactometer Checks', false)
            ->assertSee('Kraal Register Reconciliation', false);
    }

    public function test_direct_registration_rejects_an_empty_form(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'))
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['first_name', 'last_name', 'district', 'sector', 'email', 'password']);

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_direct_registration_rejects_a_sector_that_is_not_in_the_chosen_district(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'first_name' => 'Placide',
                'last_name' => 'Bizimana',
                'district' => 'Gatsibo',
                'sector' => 'Kinigi',
                'email' => 'wrong.sector@umuhinzi.rw',
                'password' => 'password12',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('sector');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'wrong.sector@umuhinzi.rw']);
    }

    public function test_direct_registration_rejects_a_password_shorter_than_ten_characters(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'first_name' => 'Placide',
                'last_name' => 'Bizimana',
                'district' => 'Gatsibo',
                'sector' => 'Kabarore',
                'email' => 'short.password@umuhinzi.rw',
                'password' => 'password1',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'short.password@umuhinzi.rw']);
    }

    public function test_reset_link_copy_does_not_disclose_whether_an_account_exists(): void
    {
        Notification::fake();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'nobody@example.rw'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', fn (string $status) => str_starts_with($status, 'If that address'));

        Notification::assertNothingSent();
    }

    public function test_a_known_address_is_sent_a_reset_link_without_a_different_response(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', fn (string $status) => str_starts_with($status, 'If that address'));

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_saving_a_new_password_signs_the_learner_in(): void
    {
        $user = User::factory()->learner()->create([
            'password' => 'password12',
        ]);
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword12',
            'password_confirmation' => 'newpassword12',
        ])->assertRedirect(route('learner.dashboard'));

        $this->assertAuthenticatedAs($user->fresh());
        $this->assertTrue(Hash::check('newpassword12', $user->fresh()->password));
    }

    public function test_an_invalid_reset_token_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->from(route('password.reset', ['token' => 'not-a-token', 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => 'not-a-token',
                'email' => $user->email,
                'password' => 'newpassword12',
                'password_confirmation' => 'newpassword12',
            ])
            ->assertRedirect(route('password.reset', ['token' => 'not-a-token', 'email' => $user->email]))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_guests_are_sent_to_sign_in_from_the_verification_notice(): void
    {
        $this->get(route('verification.notice'))
            ->assertRedirect(route('login'));
    }

    public function test_the_verification_notice_names_the_signed_in_address(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('data-experience="guest"', false)
            ->assertSee($user->email, false)
            ->assertSee('Sign out', false);
    }

    public function test_resending_verification_names_the_address_it_went_to(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->from(route('verification.notice'))
            ->post(route('verification.send'))
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', fn (string $status) => str_contains($status, $user->email));

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_a_signed_verification_link_confirms_the_address(): void
    {
        $user = User::factory()->unverified()->learner()->create();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect(route('verification.verified'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_guest_pages_show_flashed_messages(): void
    {
        $this->withSession(['status' => 'A reset link is on its way.'])
            ->get(route('login'))
            ->assertSee('A reset link is on its way.', false);
    }

    public function test_the_guest_pages_name_the_platforms_from_the_fixture(): void
    {
        $response = $this->get(route('login'));

        foreach (Platforms::active() as $platform) {
            $response->assertSee($platform['name'], false);
        }
    }

    public function test_every_password_field_offers_a_reveal_control(): void
    {
        foreach ([route('login'), route('password.reset', ['token' => 'abc']), route('register')] as $url) {
            $this->get($url)->assertSee('Show password', false);
        }
    }

    public function test_the_user_seeder_creates_the_three_demo_accounts(): void
    {
        $this->seed(CatalogSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertTrue(Auth::attempt([
            'email' => 'g.mukandayisenga@ororaschool.rw',
            'password' => 'password12',
        ]));
        Auth::logout();

        $this->assertTrue(Auth::attempt([
            'email' => 's.nyirahabimana@gemura.rw',
            'password' => 'password12',
        ]));
        Auth::logout();

        $this->assertTrue(Auth::attempt([
            'email' => 'p.bizimana@umuhinzi.rw',
            'password' => 'password12',
        ]));
    }
}
