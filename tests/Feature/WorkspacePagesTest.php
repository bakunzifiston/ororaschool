<?php

namespace Tests\Feature;

use App\Support\DemoData\People;
use Tests\TestCase;

class WorkspacePagesTest extends TestCase
{
    public function test_dashboard_shows_platform_scoped_stats_activity_and_upcoming_sessions(): void
    {
        $this->get(route('workspace.dashboard', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Gemura workspace', false)
            ->assertSee('Dairy and livestock', false)
            ->assertSee('Courses', false)
            ->assertSee('Learners', false)
            ->assertSee('Instructors', false)
            ->assertSee('Certificates issued', false)
            ->assertSee('Completion rate', false)
            ->assertSee('74%', false)
            ->assertSee('Mastitis Detection', false)
            ->assertSee('Reading CMT paddles together', false)
            ->assertDontSee('Aflatoxin Control', false)
            ->assertDontSee('Ear-Tag Registration', false);

        $this->get(route('workspace.dashboard', ['platform' => 'buchapro']))
            ->assertOk()
            ->assertSee('BuchaPro workspace', false)
            ->assertSee('Livestock traceability', false)
            ->assertSee('51%', false)
            ->assertSee('Ear-Tag Registration', false)
            ->assertSee('Tagging a batch at Rubengera', false)
            ->assertDontSee('Mastitis Detection', false)
            ->assertDontSee('CMT paddles', false);
    }

    public function test_courses_list_and_switcher_change_with_the_platform(): void
    {
        $gemura = $this->get(route('workspace.courses', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Mastitis Detection and Milk Hygiene', false)
            ->assertSee('Evening Intake and Lactometer Checks', false)
            ->assertSee('Solange Nyirahabimana', false)
            ->assertSee('Milk hygiene', false)
            ->assertSee(route('workspace.courses', ['platform' => 'buchapro']), false);

        $gemura->assertDontSee('Animal Identification and Ear-Tag Registration', false);
        $gemura->assertDontSee('Kraal Register Reconciliation', false);

        $this->get(route('workspace.courses', ['platform' => 'buchapro']))
            ->assertOk()
            ->assertSee('Animal Identification and Ear-Tag Registration', false)
            ->assertSee('Kraal Register Reconciliation', false)
            ->assertSee('Olivier Mugisha', false)
            ->assertSee('Animal identification', false)
            ->assertSee(route('workspace.courses', ['platform' => 'gemura']), false)
            ->assertDontSee('Mastitis Detection and Milk Hygiene', false)
            ->assertDontSee('Lactometer Checks', false);
    }

    public function test_course_form_covers_catalogue_fields_and_create_flashes(): void
    {
        $this->get(route('workspace.courses.create', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('New course', false)
            ->assertSee('Title', false)
            ->assertSee('Description', false)
            ->assertSee('Thumbnail', false)
            ->assertSee('Academy', false)
            ->assertSee('Category', false)
            ->assertSee('Instructor(s)', false)
            ->assertSee('Difficulty', false)
            ->assertSee('Duration (minutes)', false)
            ->assertSee('Language', false)
            ->assertSee('Paid course', false)
            ->assertSee('Certificate-eligible', false)
            ->assertSee('Enrolment required', false);

        $this->from(route('workspace.courses.create', ['platform' => 'gemura']))
            ->post(route('workspace.courses.store', ['platform' => 'gemura']))
            ->assertRedirect(route('workspace.courses', ['platform' => 'gemura']))
            ->assertSessionHas('status');
    }

    public function test_course_detail_shows_publishing_trail_and_status_transitions(): void
    {
        $this->get(route('workspace.courses.show', ['platform' => 'gemura', 'course' => 'silage-maize-stover-napier']))
            ->assertOk()
            ->assertSee('Publishing workflow', false)
            ->assertSee('Draft', false)
            ->assertSee('Pending review', false)
            ->assertSee('Approved', false)
            ->assertSee('Published', false)
            ->assertSee('Archived', false)
            ->assertSee('Submit for review', false);

        $this->from(route('workspace.courses.show', ['platform' => 'gemura', 'course' => 'silage-maize-stover-napier']))
            ->post(route('workspace.courses.transition', ['platform' => 'gemura', 'course' => 'silage-maize-stover-napier']), ['to' => 'pending_review'])
            ->assertRedirect(route('workspace.courses.show', ['platform' => 'gemura', 'course' => 'silage-maize-stover-napier']))
            ->assertSessionHas('status');
    }

    public function test_a_course_from_another_platform_is_not_found(): void
    {
        $this->get(route('workspace.courses.show', ['platform' => 'buchapro', 'course' => 'mastitis-milk-hygiene']))
            ->assertNotFound();
    }

    public function test_categories_tree_is_nested_and_differs_per_platform(): void
    {
        $this->get(route('workspace.categories', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Milk hygiene', false)
            ->assertSee('CMT scoring', false)
            ->assertSee('Collection centres', false)
            ->assertSee('Academy', false)
            ->assertSee('Sub-category', false)
            ->assertDontSee('Ear-tag application', false)
            ->assertDontSee('Outbreak traceback', false);

        $this->get(route('workspace.categories', ['platform' => 'buchapro']))
            ->assertOk()
            ->assertSee('Animal identification', false)
            ->assertSee('Ear-tag application', false)
            ->assertSee('Outbreak traceback', false)
            ->assertDontSee('CMT scoring', false)
            ->assertDontSee('Milk hygiene', false);
    }

    public function test_modules_builder_nests_lessons_with_content_types(): void
    {
        $this->get(route('workspace.modules', ['platform' => 'gemura', 'course' => 'mastitis-milk-hygiene']))
            ->assertOk()
            ->assertSee('Why somatic cell counts move', false)
            ->assertSee('Reading a CMT paddle', false)
            ->assertSee('Video', false)
            ->assertSee('Text', false)
            ->assertSee('PDF', false)
            ->assertSee('Audio', false)
            ->assertSee('External', false)
            ->assertSee('Live session', false)
            ->assertSee('Add module', false)
            ->assertSee('Add lesson', false)
            ->assertDontSee('Placing an ear tag without tearing', false);

        $this->get(route('workspace.modules', ['platform' => 'buchapro', 'course' => 'animal-identification-eartags']))
            ->assertOk()
            ->assertSee('The tag and the pliers', false)
            ->assertSee('Placing an ear tag without tearing', false)
            ->assertDontSee('Reading a CMT paddle', false);
    }

    public function test_quizzes_and_builder_are_scoped_to_the_platform(): void
    {
        $this->get(route('workspace.quizzes', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('CMT paddle reading', false)
            ->assertSee('Lactometer at evening intake', false)
            ->assertDontSee('Ear-tag placement', false);

        $this->get(route('workspace.quizzes.show', ['platform' => 'gemura', 'quiz' => 'cmt-paddle-reading']))
            ->assertOk()
            ->assertSee('Passing score', false)
            ->assertSee('Attempt limit', false)
            ->assertSee('A trace reaction on the CMT paddle', false)
            ->assertSee('Correct', false)
            ->assertSee('Add question', false);

        $this->get(route('workspace.quizzes.show', ['platform' => 'buchapro', 'quiz' => 'cmt-paddle-reading']))
            ->assertNotFound();

        $this->get(route('workspace.quizzes', ['platform' => 'buchapro']))
            ->assertOk()
            ->assertSee('Ear-tag placement', false)
            ->assertSee('Movement permit fields', false)
            ->assertDontSee('CMT paddle reading', false);
    }

    public function test_resources_filter_by_type_and_show_attachments(): void
    {
        $this->get(route('workspace.resources', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('CMT field sheet', false)
            ->assertSee('Lesson · Reading a CMT paddle', false)
            ->assertDontSee('Ear-tag application checklist', false);

        $this->get(route('workspace.resources', ['platform' => 'gemura', 'type' => 'template']))
            ->assertOk()
            ->assertSee('CMT field sheet', false)
            ->assertDontSee('Paddle scoring demonstration', false);

        $this->get(route('workspace.resources.create', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Upload a resource', false)
            ->assertSee('Attach to', false);
    }

    public function test_instructors_and_learners_lists_are_scoped_to_the_platform(): void
    {
        $this->get(route('workspace.instructors', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Solange Nyirahabimana', false)
            ->assertSee('Chantal Ingabire', false)
            ->assertDontSee('Olivier Mugisha', false);

        $this->get(route('workspace.instructors', ['platform' => 'buchapro']))
            ->assertOk()
            ->assertSee('Olivier Mugisha', false)
            ->assertSee('Fabrice Gasana', false)
            ->assertDontSee('Chantal Ingabire', false);

        $olivier = collect(People::directory())->firstWhere('name', 'Olivier Mugisha');

        $this->get(route('workspace.instructors.show', ['platform' => 'buchapro', 'instructor' => $olivier['id']]))
            ->assertOk()
            ->assertSee('Animal Identification and Ear-Tag Registration', false);

        $this->get(route('workspace.instructors.show', ['platform' => 'gemura', 'instructor' => $olivier['id']]))
            ->assertNotFound();

        $this->get(route('workspace.learners', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Célestin Ndayisaba', false)
            ->assertSee('Kinigi collection centre', false)
            ->assertDontSee('Alphonsine Mukarugwiza', false);

        $this->get(route('workspace.learners', ['platform' => 'buchapro']))
            ->assertOk()
            ->assertSee('Alphonsine Mukarugwiza', false)
            ->assertSee('Nyabugogo market agents', false)
            ->assertDontSee('Célestin Ndayisaba', false);

        $this->get(route('workspace.learners.show', ['platform' => 'gemura', 'learner' => 101]))
            ->assertOk()
            ->assertSee('Placide Bizimana', false)
            ->assertSee('Mastitis Detection', false);

        $this->get(route('workspace.learners.show', ['platform' => 'buchapro', 'learner' => 103]))
            ->assertNotFound();
    }

    public function test_certificates_list_revoke_and_public_verification(): void
    {
        $this->get(route('workspace.certificates', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('OS-GEM-2026-1847', false)
            ->assertSee('Dative Nyiranzeyimana', false)
            ->assertSee('Verify', false)
            ->assertSee('Revoke', false)
            ->assertDontSee('OS-BCH-2026-0521', false);

        $this->get(route('workspace.certificates', ['platform' => 'buchapro']))
            ->assertOk()
            ->assertSee('OS-BCH-2026-0521', false)
            ->assertSee('Alphonsine Mukarugwiza', false)
            ->assertDontSee('OS-GEM-2026-1847', false);

        $this->from(route('workspace.certificates', ['platform' => 'gemura']))
            ->post(route('workspace.certificates.revoke', ['platform' => 'gemura', 'certificate' => 'OS-GEM-2026-1847']))
            ->assertRedirect(route('workspace.certificates', ['platform' => 'gemura']))
            ->assertSessionHas('status');

        $this->post(route('workspace.certificates.revoke', ['platform' => 'buchapro', 'certificate' => 'OS-GEM-2026-1847']))
            ->assertNotFound();

        $this->get(route('certificates.verify', ['code' => 'OS-GEM-2026-1847']))
            ->assertOk()
            ->assertSee('Certificate verified', false)
            ->assertSee('Dative Nyiranzeyimana', false)
            ->assertSee('Gemura', false);

        $this->get(route('certificates.verify', ['code' => 'OS-GEM-2026-1810']))
            ->assertOk()
            ->assertSee('Certificate revoked', false)
            ->assertSee('Célestin Ndayisaba', false);

        $this->get(route('certificates.verify', ['code' => 'NOT-A-CODE']))
            ->assertOk()
            ->assertSee('Not a valid certificate', false);
    }

    public function test_live_sessions_and_analytics_change_with_the_platform(): void
    {
        $this->get(route('workspace.sessions', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Reading CMT paddles together', false)
            ->assertDontSee('Tagging a batch at Rubengera', false);

        $this->get(route('workspace.sessions.create', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Meeting URL', false)
            ->assertSee('Duration (minutes)', false);

        $this->get(route('workspace.sessions.edit', ['platform' => 'gemura', 'session' => 3]))
            ->assertOk()
            ->assertSee('Recording URL', false)
            ->assertSee('Cold chain at Kinigi centre', false);

        $this->get(route('workspace.sessions.edit', ['platform' => 'buchapro', 'session' => 3]))
            ->assertNotFound();

        $this->get(route('workspace.analytics', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Enrolments over time', false)
            ->assertSee('Completion rate', false)
            ->assertSee('Quiz score distribution', false)
            ->assertSee('Top courses by enrolment', false)
            ->assertSee('Mastitis Detection', false)
            ->assertSee('74%', false)
            ->assertDontSee('Ear-Tag Registration', false);

        $this->get(route('workspace.analytics', ['platform' => 'buchapro']))
            ->assertOk()
            ->assertSee('Ear-Tag Registration', false)
            ->assertSee('51%', false)
            ->assertDontSee('Mastitis Detection', false);
    }

    public function test_empty_fixture_preview_hides_platform_rows(): void
    {
        $this->get(route('workspace.courses', ['platform' => 'gemura', 'empty' => 1]))
            ->assertOk()
            ->assertSee('No courses in Gemura yet', false)
            ->assertDontSee('Mastitis Detection', false);

        $this->get(route('workspace.categories', ['platform' => 'gemura', 'empty' => 1]))
            ->assertOk()
            ->assertSee('No taxonomy on Gemura yet', false)
            ->assertDontSee('CMT scoring', false);
    }
}
