<?php

namespace Tests\Feature;

use App\Models\Academy;
use App\Models\Course;
use App\Models\Lesson;
use Database\Seeders\CatalogSeeder;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class WorkspaceResourceUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CatalogSeeder::class);
        $this->actingAsPlatformStaff();
    }

    public function test_upload_form_lists_types_and_attachment_targets(): void
    {
        $this->get(route('workspace.resources.create', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Choose a type', false)
            ->assertSee('Choose where it sits', false)
            ->assertSee('Milk hygiene', false)
            ->assertSee('Mastitis Detection', false)
            ->assertDontSee('Any type', false);
    }

    public function test_an_academy_manual_appears_on_the_public_resources_page(): void
    {
        $academy = Academy::query()
            ->whereHas('platform', fn ($query) => $query->where('slug', 'gemura'))
            ->where('name', 'Milk hygiene')
            ->firstOrFail();

        $this->from(route('workspace.resources.create', ['platform' => 'gemura']))
            ->post(route('workspace.resources.store', ['platform' => 'gemura']), [
                'title' => 'Milking stool checklist',
                'type' => 'manual',
                'attached_kind' => 'academy',
                'attached_key' => (string) $academy->id,
            ])
            ->assertRedirect(route('workspace.resources', ['platform' => 'gemura']))
            ->assertSessionHas('status', 'Resource saved.');

        $this->assertDatabaseHas('learning_resources', [
            'title' => 'Milking stool checklist',
            'type' => 'manual',
            'attached_kind' => 'academy',
        ]);

        $this->get(route('workspace.resources', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Milking stool checklist', false)
            ->assertSee('Academy · Milk hygiene', false);

        $this->get(route('catalog.resources'))
            ->assertOk()
            ->assertSee('Milking stool checklist', false);

        $this->get(route('catalog.platforms.show', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Milking stool checklist', false);
    }

    public function test_a_lesson_template_stays_off_the_public_catalogue(): void
    {
        $lesson = Lesson::query()
            ->whereHas('course.platform', fn ($query) => $query->where('slug', 'gemura'))
            ->firstOrFail();

        $this->post(route('workspace.resources.store', ['platform' => 'gemura']), [
            'title' => 'Paddle scoring sheet',
            'type' => 'template',
            'attached_kind' => 'lesson',
            'attached_key' => (string) $lesson->id,
        ])->assertRedirect(route('workspace.resources', ['platform' => 'gemura']));

        $this->get(route('workspace.resources', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Paddle scoring sheet', false);

        $this->get(route('catalog.resources'))
            ->assertOk()
            ->assertDontSee('Paddle scoring sheet', false);
    }

    public function test_a_target_from_another_academy_is_rejected(): void
    {
        $feedgrid = Academy::query()
            ->whereHas('platform', fn ($query) => $query->where('slug', 'feedgrid'))
            ->firstOrFail();

        $this->from(route('workspace.resources.create', ['platform' => 'gemura']))
            ->post(route('workspace.resources.store', ['platform' => 'gemura']), [
                'title' => 'Wrong academy sheet',
                'type' => 'guide',
                'attached_kind' => 'academy',
                'attached_key' => (string) $feedgrid->id,
            ])
            ->assertRedirect(route('workspace.resources.create', ['platform' => 'gemura']))
            ->assertSessionHasErrors('attached_key');

        $this->assertDatabaseMissing('learning_resources', [
            'title' => 'Wrong academy sheet',
        ]);
    }

    public function test_a_missing_type_is_rejected(): void
    {
        $this->from(route('workspace.resources.create', ['platform' => 'gemura']))
            ->post(route('workspace.resources.store', ['platform' => 'gemura']), [
                'title' => 'Unlabelled sheet',
                'attached_kind' => 'academy',
                'attached_key' => '1',
            ])
            ->assertRedirect(route('workspace.resources.create', ['platform' => 'gemura']))
            ->assertSessionHasErrors('type');
    }

    public function test_a_video_rejects_a_pdf_file(): void
    {
        $course = Course::query()
            ->whereHas('platform', fn ($query) => $query->where('slug', 'gemura'))
            ->firstOrFail();

        $this->from(route('workspace.resources.create', ['platform' => 'gemura']))
            ->post(route('workspace.resources.store', ['platform' => 'gemura']), [
                'title' => 'Evening intake clip',
                'type' => 'video',
                'attached_kind' => 'course',
                'attached_key' => (string) $course->id,
                'file' => UploadedFile::fake()->create('evening-intake.pdf', 120, 'application/pdf'),
            ])
            ->assertRedirect(route('workspace.resources.create', ['platform' => 'gemura']))
            ->assertSessionHasErrors('file');
    }

    public function test_guests_are_sent_to_sign_in(): void
    {
        auth()->logout();

        $this->post(route('workspace.resources.store', ['platform' => 'gemura']), [
            'title' => 'Guest sheet',
            'type' => 'manual',
            'attached_kind' => 'academy',
            'attached_key' => '1',
        ])->assertRedirect(route('login'));
    }
}
