<?php

namespace Tests\Feature;

use App\Models\Academy;
use App\Models\Lesson;
use Database\Seeders\CatalogSeeder;
use Tests\TestCase;

class WorkspaceResourceActionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CatalogSeeder::class);
        $this->actingAsPlatformStaff();
    }

    public function test_the_edit_form_opens_with_the_current_resource(): void
    {
        $this->get(route('workspace.resources.edit', ['platform' => 'gemura', 'resource' => 'cmt-field-sheet']))
            ->assertOk()
            ->assertSee('Edit resource', false)
            ->assertSee('CMT field sheet', false)
            ->assertSee('Reading a CMT paddle', false);
    }

    public function test_a_persisted_resource_can_be_updated(): void
    {
        $academy = Academy::query()
            ->whereHas('platform', fn ($query) => $query->where('slug', 'gemura'))
            ->where('name', 'Milk hygiene')
            ->firstOrFail();

        $this->post(route('workspace.resources.store', ['platform' => 'gemura']), [
            'title' => 'Milking stool checklist',
            'type' => 'manual',
            'attached_kind' => 'academy',
            'attached_key' => (string) $academy->id,
        ])->assertRedirect(route('workspace.resources', ['platform' => 'gemura']));

        $this->from(route('workspace.resources.edit', ['platform' => 'gemura', 'resource' => 'milking-stool-checklist']))
            ->post(route('workspace.resources.update', ['platform' => 'gemura', 'resource' => 'milking-stool-checklist']), [
                'title' => 'Evening stool checklist',
                'type' => 'guide',
                'attached_kind' => 'academy',
                'attached_key' => (string) $academy->id,
            ])
            ->assertRedirect(route('workspace.resources.show', ['platform' => 'gemura', 'resource' => 'milking-stool-checklist']))
            ->assertSessionHas('status', 'Resource saved.');

        $this->assertDatabaseHas('learning_resources', [
            'slug' => 'milking-stool-checklist',
            'title' => 'Evening stool checklist',
            'type' => 'guide',
        ]);

        $this->get(route('workspace.resources', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Evening stool checklist', false)
            ->assertDontSee('Milking stool checklist', false);
    }

    public function test_a_fixture_edit_is_written_and_replaces_the_fixture(): void
    {
        $lesson = Lesson::query()
            ->whereHas('course.platform', fn ($query) => $query->where('slug', 'gemura'))
            ->where('title', 'Reading a CMT paddle')
            ->firstOrFail();

        $this->post(route('workspace.resources.update', ['platform' => 'gemura', 'resource' => 'cmt-field-sheet']), [
            'title' => 'CMT paddle sheet',
            'type' => 'template',
            'attached_kind' => 'lesson',
            'attached_key' => (string) $lesson->id,
        ])->assertRedirect(route('workspace.resources.show', ['platform' => 'gemura', 'resource' => 'cmt-field-sheet']));

        $this->assertDatabaseHas('learning_resources', [
            'slug' => 'cmt-field-sheet',
            'title' => 'CMT paddle sheet',
        ]);

        $this->get(route('workspace.resources', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('CMT paddle sheet', false)
            ->assertDontSee('CMT field sheet', false);
    }

    public function test_a_persisted_resource_can_be_deleted(): void
    {
        $academy = Academy::query()
            ->whereHas('platform', fn ($query) => $query->where('slug', 'gemura'))
            ->where('name', 'Milk hygiene')
            ->firstOrFail();

        $this->post(route('workspace.resources.store', ['platform' => 'gemura']), [
            'title' => 'Milking stool checklist',
            'type' => 'manual',
            'attached_kind' => 'academy',
            'attached_key' => (string) $academy->id,
        ]);

        $this->from(route('workspace.resources', ['platform' => 'gemura']))
            ->delete(route('workspace.resources.destroy', ['platform' => 'gemura', 'resource' => 'milking-stool-checklist']))
            ->assertRedirect(route('workspace.resources', ['platform' => 'gemura']))
            ->assertSessionHas('status', 'Milking stool checklist was removed.');

        $this->assertDatabaseMissing('learning_resources', [
            'title' => 'Milking stool checklist',
        ]);

        $this->get(route('workspace.resources', ['platform' => 'gemura']));

        $this->get(route('workspace.resources', ['platform' => 'gemura']))
            ->assertOk()
            ->assertDontSee('Milking stool checklist', false);

        $this->get(route('catalog.resources'))
            ->assertOk()
            ->assertDontSee('Milking stool checklist', false);
    }

    public function test_deleting_a_fixture_hides_it_from_the_workspace_and_catalogue(): void
    {
        $this->delete(route('workspace.resources.destroy', ['platform' => 'gemura', 'resource' => 'colostrum-note']))
            ->assertRedirect(route('workspace.resources', ['platform' => 'gemura']));

        $this->assertDatabaseHas('removed_resources', [
            'platform' => 'gemura',
            'slug' => 'colostrum-note',
        ]);

        $this->get(route('workspace.resources', ['platform' => 'gemura']));

        $this->get(route('workspace.resources', ['platform' => 'gemura']))
            ->assertOk()
            ->assertDontSee('Colostrum timing note', false);

        $this->get(route('catalog.resources'))
            ->assertOk()
            ->assertDontSee('Colostrum timing note', false)
            ->assertSee('Smallholder milk hygiene manual', false);
    }

    public function test_a_resource_from_another_academy_is_not_found(): void
    {
        $this->get(route('workspace.resources.edit', ['platform' => 'buchapro', 'resource' => 'cmt-field-sheet']))
            ->assertNotFound();

        $this->post(route('workspace.resources.update', ['platform' => 'buchapro', 'resource' => 'cmt-field-sheet']), [
            'title' => 'Wrong academy sheet',
            'type' => 'template',
            'attached_kind' => 'lesson',
            'attached_key' => '1',
        ])->assertNotFound();

        $this->delete(route('workspace.resources.destroy', ['platform' => 'buchapro', 'resource' => 'cmt-field-sheet']))
            ->assertNotFound();

        $this->assertDatabaseCount('learning_resources', 0);
    }

    public function test_guests_are_sent_to_sign_in(): void
    {
        auth()->logout();

        $this->post(route('workspace.resources.update', ['platform' => 'gemura', 'resource' => 'cmt-field-sheet']), [
            'title' => 'Guest sheet',
            'type' => 'template',
            'attached_kind' => 'lesson',
            'attached_key' => '1',
        ])->assertRedirect(route('login'));

        $this->delete(route('workspace.resources.destroy', ['platform' => 'gemura', 'resource' => 'cmt-field-sheet']))
            ->assertRedirect(route('login'));
    }
}
