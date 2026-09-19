<?php

namespace Tests\Feature;

use Tests\TestCase;

class LearnerPagesTest extends TestCase
{
    public function test_dashboard_shows_courses_from_three_platforms_on_one_record(): void
    {
        $this->get(route('learner.dashboard'))
            ->assertOk()
            ->assertSee('Welcome back, Placide Bizimana', false)
            ->assertSee('One learning record', false)
            ->assertSee('Gemura', false)
            ->assertSee('BuchaPro', false)
            ->assertSee('FeedGrid', false)
            ->assertSee('Mastitis Detection', false)
            ->assertSee('Movement Permits', false)
            ->assertSee('Aflatoxin Control', false)
            ->assertSee('Courses in progress', false)
            ->assertSee('Certificates earned', false)
            ->assertSee('Audio: a clean milking sequence', false)
            ->assertDontSee('three separate accounts', false)
            ->assertDontSee('OroraFarm', false);
    }

    public function test_my_courses_groups_by_platform_and_filters_progress(): void
    {
        $this->get(route('learner.courses'))
            ->assertOk()
            ->assertSee('Same record — not three logins', false)
            ->assertSee('Gemura', false)
            ->assertSee('BuchaPro', false)
            ->assertSee('FeedGrid', false)
            ->assertSee('Mastitis Detection', false)
            ->assertSee('Animal Identification', false)
            ->assertSee('Aflatoxin Control', false);

        $this->get(route('learner.courses', ['status' => 'completed']))
            ->assertOk()
            ->assertSee('Cold Chain Discipline', false)
            ->assertSee('Animal Identification', false)
            ->assertDontSee('Aflatoxin Control', false);

        $this->get(route('learner.courses', ['status' => 'active']))
            ->assertOk()
            ->assertSee('Mastitis Detection', false)
            ->assertSee('Aflatoxin Control', false)
            ->assertDontSee('Cold Chain Discipline', false);
    }

    public function test_syllabus_shows_enroll_when_required_and_start_when_not(): void
    {
        $this->get(route('learner.courses.show', ['course' => 'kraal-register-reconciliation']))
            ->assertOk()
            ->assertSee('Enrolment is required', false)
            ->assertSee('Enrol', false)
            ->assertDontSee('Enrolment is not required', false);

        $this->from(route('learner.courses.show', ['course' => 'kraal-register-reconciliation']))
            ->post(route('learner.courses.enroll', ['course' => 'kraal-register-reconciliation']))
            ->assertRedirect(route('learner.courses.show', ['course' => 'kraal-register-reconciliation']))
            ->assertSessionHas('status');

        $this->get(route('learner.courses.show', ['course' => 'evening-intake-lactometer']))
            ->assertOk()
            ->assertSee('Enrolment is not required', false)
            ->assertSee('Start', false)
            ->assertDontSee('Enrolment is required', false);

        $this->get(route('learner.courses.show', ['course' => 'mastitis-milk-hygiene']))
            ->assertOk()
            ->assertSee('Continue', false)
            ->assertSee('In progress', false)
            ->assertSee('Locked', false)
            ->assertDontSee('Enrolment is required', false);
    }

    public function test_lesson_viewer_renders_content_types_and_rejects_locked_lessons(): void
    {
        $this->get(route('learner.courses.lessons.show', ['course' => 'mastitis-milk-hygiene', 'lesson' => 'l-cmt-1']))
            ->assertOk()
            ->assertSee('Video placeholder', false)
            ->assertSee('Mark complete', false)
            ->assertSee('Next', false);

        $this->get(route('learner.courses.lessons.show', ['course' => 'mastitis-milk-hygiene', 'lesson' => 'l-cmt-2']))
            ->assertOk()
            ->assertSee('Trace is a slight slime', false);

        $this->get(route('learner.courses.lessons.show', ['course' => 'mastitis-milk-hygiene', 'lesson' => 'l-cmt-3']))
            ->assertOk()
            ->assertSee('PDF viewer placeholder', false);

        $this->get(route('learner.courses.lessons.show', ['course' => 'mastitis-milk-hygiene', 'lesson' => 'l-milk-2']))
            ->assertOk()
            ->assertSee('Audio player placeholder', false);

        $this->get(route('learner.courses.lessons.show', ['course' => 'animal-identification-eartags', 'lesson' => 'l-tag-6']))
            ->assertOk()
            ->assertSee('External resource', false)
            ->assertSee('Open the note', false);

        $this->get(route('learner.courses.lessons.show', ['course' => 'animal-identification-eartags', 'lesson' => 'l-tag-7']))
            ->assertOk()
            ->assertSee('Live session join placeholder', false)
            ->assertSee('Join session', false);

        $this->get(route('learner.courses.lessons.show', ['course' => 'mastitis-milk-hygiene', 'lesson' => 'l-milk-3']))
            ->assertNotFound();

        $this->from(route('learner.courses.lessons.show', ['course' => 'mastitis-milk-hygiene', 'lesson' => 'l-cmt-1']))
            ->post(route('learner.courses.lessons.complete', ['course' => 'mastitis-milk-hygiene', 'lesson' => 'l-cmt-1']))
            ->assertRedirect(route('learner.courses.lessons.show', ['course' => 'mastitis-milk-hygiene', 'lesson' => 'l-cmt-1']))
            ->assertSessionHas('status');
    }

    public function test_quiz_is_single_page_and_shows_a_mock_result(): void
    {
        $this->get(route('learner.quizzes.show', ['quiz' => 'cmt-paddle-reading']))
            ->assertOk()
            ->assertSee('Single-page quiz', false)
            ->assertSee('A trace reaction on the CMT paddle', false)
            ->assertSee('Submit quiz', false);

        $this->from(route('learner.quizzes.show', ['quiz' => 'cmt-paddle-reading']))
            ->post(route('learner.quizzes.submit', ['quiz' => 'cmt-paddle-reading']))
            ->assertRedirect(route('learner.quizzes.show', ['quiz' => 'cmt-paddle-reading']))
            ->assertSessionHas('quiz_result');

        $this->withSession(['quiz_result' => ['score' => 83, 'pass' => true, 'passed' => true, 'label' => 'Passed']])
            ->get(route('learner.quizzes.show', ['quiz' => 'cmt-paddle-reading']))
            ->assertOk()
            ->assertSee('Passed', false)
            ->assertSee('83%', false);

        $this->withSession(['quiz_result' => ['score' => 40, 'pass' => false, 'passed' => false, 'label' => 'Not yet']])
            ->get(route('learner.quizzes.show', ['quiz' => 'cmt-paddle-reading']))
            ->assertOk()
            ->assertSee('Not yet', false)
            ->assertSee('40%', false);
    }

    public function test_learning_paths_distinguish_single_and_cross_platform(): void
    {
        $this->get(route('learner.paths'))
            ->assertOk()
            ->assertSee('Milk hygiene for collection-centre work', false)
            ->assertSee('1 platform', false)
            ->assertSee('From kraal to ration', false)
            ->assertSee('3 platforms', false);

        $this->get(route('learner.paths.show', ['path' => 'kraal-to-ration']))
            ->assertOk()
            ->assertSee('Gemura', false)
            ->assertSee('BuchaPro', false)
            ->assertSee('FeedGrid', false)
            ->assertSee('Animal Identification', false)
            ->assertSee('Aflatoxin Control', false);
    }

    public function test_certificates_sessions_resources_and_profile_span_platforms(): void
    {
        $this->get(route('learner.certificates'))
            ->assertOk()
            ->assertSee('OS-GEM-2026-1841', false)
            ->assertSee('OS-BCH-2026-0498', false)
            ->assertSee('View / Share', false)
            ->assertSee(route('certificates.verify', ['code' => 'OS-GEM-2026-1841']), false);

        $this->get(route('learner.sessions'))
            ->assertOk()
            ->assertSee('Reading CMT paddles together', false)
            ->assertSee('Join', false)
            ->assertSee('Recording', false);

        $this->from(route('learner.sessions'))
            ->post(route('learner.sessions.join', ['session' => 1]))
            ->assertRedirect(route('learner.sessions'))
            ->assertSessionHas('status');

        $this->get(route('learner.resources'))
            ->assertOk()
            ->assertSee('CMT field sheet', false)
            ->assertSee('Download', false);

        $this->get(route('learner.profile'))
            ->assertOk()
            ->assertSee('Placide Bizimana', false)
            ->assertSee('Learning history', false)
            ->assertSee('Gemura', false)
            ->assertSee('BuchaPro', false)
            ->assertSee('FeedGrid', false)
            ->assertSee('OS-BCH-2026-0498', false);

        $this->from(route('learner.profile'))
            ->post(route('learner.profile.update'))
            ->assertRedirect(route('learner.profile'))
            ->assertSessionHas('status');
    }

    public function test_empty_previews_hide_fixture_rows(): void
    {
        $this->get(route('learner.courses', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No courses on your record yet', false)
            ->assertDontSee('Mastitis Detection', false);

        $this->get(route('learner.paths', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No learning path yet', false);

        $this->get(route('learner.certificates', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No certificates yet', false)
            ->assertDontSee('OS-GEM-2026-1841', false);

        $this->get(route('learner.sessions', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No clinics on your courses', false);

        $this->get(route('learner.resources', ['empty' => 1]))
            ->assertOk()
            ->assertSee('No resources on your courses yet', false)
            ->assertDontSee('CMT field sheet', false);
    }
}
