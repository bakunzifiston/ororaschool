<?php

namespace Tests\Feature;

use App\Support\DemoData\Pages\AuthPages;
use App\Support\DemoData\Platforms;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Phase F2 checks for the unauthenticated pages.
 *
 * Nothing authenticates, so these assert the shape instead: every page renders in
 * the guest layout with no sidebar, every form posts somewhere real, and the
 * temporary preview control does what it claims and is labelled as temporary.
 */
class AuthPagesTest extends TestCase
{
    /** @return array<string, array{0: string}> */
    public static function guestRoutes(): array
    {
        return [
            'login' => ['login'],
            'register' => ['register'],
            'forgot password' => ['password.request'],
            'verification notice' => ['verification.notice'],
            'verified confirmation' => ['verification.verified'],
        ];
    }

    #[DataProvider('guestRoutes')]
    public function test_each_guest_page_renders_in_the_guest_layout(string $name): void
    {
        $this->get(route($name))
            ->assertOk()
            ->assertSee('data-experience="guest"', false)
            // No sidebar anywhere in the unauthenticated experience.
            ->assertDontSee('<aside', false)
            // Nor any of the signed-in chrome.
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
            ->assertSee(route('register'), false);
    }

    public function test_the_preview_control_is_marked_temporary_and_offers_three_shells(): void
    {
        $response = $this->get(route('login'));

        $response->assertSee('Temporary — no authentication in this build', false)
            ->assertSee('name="preview_as"', false);

        foreach (['Super Admin', 'Platform Admin', 'Learner'] as $label) {
            $response->assertSee($label, false);
        }
    }

    public function test_the_preview_control_opens_the_shell_it_names(): void
    {
        $this->post(route('login.attempt'), ['preview_as' => 'super-admin'])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('status');

        $this->post(route('login.attempt'), ['preview_as' => 'learner'])
            ->assertRedirect(route('learner.dashboard'));

        $this->post(route('login.attempt'), ['preview_as' => 'platform-workspace'])
            ->assertRedirect(route('workspace.dashboard', ['platform' => 'gemura']));
    }

    public function test_an_unknown_preview_value_falls_back_to_the_learner_shell(): void
    {
        $this->post(route('login.attempt'), ['preview_as' => 'nonsense'])
            ->assertRedirect(route('learner.dashboard'));

        $this->post(route('login.attempt'))
            ->assertRedirect(route('learner.dashboard'));
    }

    public function test_the_chosen_preview_carries_through_to_the_verified_page(): void
    {
        $this->post(route('login.attempt'), ['preview_as' => 'super-admin']);

        $this->get(route('verification.verified'))
            ->assertOk()
            ->assertSee(route('admin.dashboard'), false)
            ->assertSee('Super Admin', false);
    }

    public function test_registration_leads_with_platform_linking(): void
    {
        $response = $this->get(route('register'));

        foreach (Platforms::active() as $platform) {
            $response->assertSee('Continue with '.$platform['name'], false);
        }

        $response->assertSee(route('register.link'), false);
    }

    public function test_linking_a_platform_reports_back_without_pretending_to_work(): void
    {
        $this->from(route('register'))
            ->post(route('register.link'), ['platform' => 'gemura'])
            ->assertRedirect(route('register'))
            ->assertSessionHas('status', fn (string $status) => str_contains($status, 'Gemura'));
    }

    public function test_direct_registration_sends_people_to_confirm_their_email(): void
    {
        $this->post(route('register.store'))
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status');
    }

    public function test_reset_link_copy_does_not_disclose_whether_an_account_exists(): void
    {
        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'nobody@example.rw'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', fn (string $status) => str_starts_with($status, 'If that address'));
    }

    public function test_saving_a_new_password_returns_to_sign_in(): void
    {
        $this->post(route('password.update'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');
    }

    public function test_resending_verification_names_the_address_it_went_to(): void
    {
        $this->from(route('verification.notice'))
            ->post(route('verification.send'))
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', fn (string $status) => str_contains($status, AuthPages::pendingEmail()));
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
}
