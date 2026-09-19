<?php

namespace Tests\Feature;

use App\Support\DemoData\DemoData;
use App\Support\DemoData\Navigation;
use App\Support\DemoData\Platforms;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Phase F1 shell checks.
 *
 * Walks every placeholder route in all three experiences and asserts the
 * required F1 behaviours: the layouts are reachable, the switcher is populated
 * from the fixture user, and the components render from data rather than
 * hardcoded markup.
 */
class ShellTest extends TestCase
{
    public function test_root_path_goes_to_the_sign_in_page(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_every_super_admin_destination_renders(): void
    {
        foreach (Navigation::superAdmin() as $item) {
            $this->get(route($item['route']))
                ->assertOk()
                ->assertSee('data-experience="super-admin"', false)
                ->assertSee($item['label'], false);
        }
    }

    public function test_every_workspace_destination_renders_for_every_reachable_platform(): void
    {
        foreach (DemoData::accessiblePlatforms() as $platform) {
            foreach (Navigation::platformWorkspace() as $item) {
                $this->get(route($item['route'], ['platform' => $platform['slug']]))
                    ->assertOk()
                    ->assertSee('data-experience="platform-workspace"', false)
                    ->assertSee($platform['name'], false);
            }
        }
    }

    public function test_every_learner_destination_renders(): void
    {
        foreach (Navigation::learner() as $item) {
            $this->get(route($item['route']))
                ->assertOk()
                ->assertSee('data-experience="learner"', false)
                ->assertSee($item['label'], false);
        }
    }

    public function test_an_unknown_platform_slug_is_not_found(): void
    {
        $this->get('/workspace/not-a-platform')->assertNotFound();
    }

    public function test_switcher_lists_only_the_platforms_the_fixture_user_works_on(): void
    {
        $response = $this->get(route('workspace.dashboard', ['platform' => 'gemura']));

        // Solange reaches three of the four platforms; OroraFarm is not hers.
        foreach (['Gemura', 'BuchaPro', 'FeedGrid'] as $name) {
            $response->assertSee($name, false);
        }

        $response->assertDontSee('OroraFarm', false);
    }

    public function test_each_switcher_entry_points_at_that_platforms_dashboard(): void
    {
        $response = $this->get(route('workspace.dashboard', ['platform' => 'gemura']));

        foreach (['gemura', 'buchapro', 'feedgrid'] as $slug) {
            $response->assertSee(route('workspace.dashboard', ['platform' => $slug]), false);
        }
    }

    public function test_workspace_content_is_scoped_to_the_platform_in_the_route(): void
    {
        $this->get(route('workspace.dashboard', ['platform' => 'gemura']))
            ->assertSee('Mastitis Detection', false)
            ->assertDontSee('Aflatoxin Control', false);

        $this->get(route('workspace.dashboard', ['platform' => 'feedgrid']))
            ->assertSee('Aflatoxin Control', false)
            ->assertDontSee('Mastitis Detection', false);
    }

    public function test_component_proof_renders_inside_each_of_the_three_layouts(): void
    {
        foreach (['super-admin', 'platform-workspace', 'learner'] as $experience) {
            $this->get(route('design.components', ['experience' => $experience]))
                ->assertOk()
                ->assertSee('data-experience="'.$experience.'"', false)
                ->assertSee('Component proof', false);
        }
    }

    public function test_badge_renders_every_status_it_claims_to_support(): void
    {
        $response = $this->get(route('design.components'));

        foreach (['Draft', 'Pending review', 'Approved', 'Published', 'Archived', 'Active', 'Completed'] as $label) {
            $response->assertSee($label, false);
        }

        // An unmapped value must fall back rather than blow up.
        $response->assertSee('Suspended', false);
    }

    public function test_course_card_renders_both_contexts_from_the_same_component(): void
    {
        $response = $this->get(route('design.components'));

        // Learner context: a progress value is present.
        $response->assertSee('aria-valuenow="38"', false);
        // Staff context: an instructor name instead.
        $response->assertSee('Fabrice Gasana', false);
    }

    public function test_data_table_falls_back_to_its_empty_state_with_no_rows(): void
    {
        $this->get(route('design.components'))
            ->assertSee('No learners on this cohort yet', false);
    }

    public function test_sign_out_placeholder_lands_on_the_sign_in_page_with_a_message(): void
    {
        $this->from(route('admin.dashboard'))
            ->post(route('sign-out'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->withSession(['status' => 'Signed out'])
            ->get(route('admin.dashboard'))
            ->assertSee('Signed out', false);
    }

    public function test_role_gated_nav_is_hidden_from_a_persona_without_the_role(): void
    {
        $asSuperAdmin = Navigation::visibleFor('super-admin', DemoData::currentUser('super-admin'));
        $asCurriculumLead = Navigation::visibleFor('super-admin', DemoData::currentUser('platform-workspace'));

        $this->assertContains('Permissions', array_column($asSuperAdmin, 'label'));
        $this->assertNotContains('Permissions', array_column($asCurriculumLead, 'label'));
    }

    public function test_every_nav_item_has_a_registered_route(): void
    {
        foreach (['super-admin', 'platform-workspace', 'learner'] as $experience) {
            foreach (Navigation::for($experience) as $item) {
                $this->assertTrue(Route::has($item['route']), "Missing route: {$item['route']}");
            }
        }
    }

    public function test_every_platform_in_the_fixture_set_has_a_workspace_that_renders(): void
    {
        foreach (Platforms::slugs() as $slug) {
            $this->get(route('workspace.dashboard', ['platform' => $slug]))->assertOk();
        }
    }
}
