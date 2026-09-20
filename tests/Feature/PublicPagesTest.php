<?php

namespace Tests\Feature;

use App\Support\DemoData\Courses;
use App\Support\DemoData\PublicCatalog;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_the_catalog_query_excludes_unpublished_and_inactive_platform_courses(): void
    {
        $allTitles = array_column(Courses::all(), 'title');
        $publicTitles = array_column(PublicCatalog::publishedCourses(), 'title');

        $this->assertContains('Layer Vaccination Calendars for Small Flocks', $allTitles);
        $this->assertContains('Mineral Supplementation for Lactating Cows', $allTitles);
        $this->assertContains('Costing a Season: Inputs, Labour and Margin', $allTitles);
        $this->assertContains('Cooperative Membership and Share Records', $allTitles);
        $this->assertContains('Mastitis Detection and Milk Hygiene in Smallholder Herds', $allTitles);

        $this->assertCount(10, $publicTitles);
        $this->assertContains('Mastitis Detection and Milk Hygiene in Smallholder Herds', $publicTitles);
        $this->assertNotContains('Layer Vaccination Calendars for Small Flocks', $publicTitles);
        $this->assertNotContains('Mineral Supplementation for Lactating Cows', $publicTitles);
        $this->assertNotContains('Costing a Season: Inputs, Labour and Margin', $publicTitles);
        $this->assertNotContains('Cooperative Membership and Share Records', $publicTitles);
        $this->assertNotContains('Heat Detection and AI Timing for Ankole–Friesian Crosses', $publicTitles);
        $this->assertNotContains('Silage from Maize Stover and Napier Grass', $publicTitles);
    }

    public function test_homepage_lists_published_courses_on_active_platforms_only(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-experience="public"', false)
            ->assertDontSee('<aside', false)
            ->assertSee('Get started', false)
            ->assertSee('Sign in', false)
            ->assertDontSee('Log in', false)
            ->assertSee('Four platforms. One school.', false)
            ->assertSee('Farm management', false)
            ->assertSee('Dairy and livestock', false)
            ->assertSee('Published courses', false)
            ->assertSee('Explore courses', false)
            ->assertSee('OroraFarm', false)
            ->assertSee('Gemura', false)
            ->assertSee('BuchaPro', false)
            ->assertSee('FeedGrid', false)
            ->assertSee('Mastitis Detection', false)
            ->assertSee('Farm Record Keeping', false)
            ->assertDontSee('Ubworozi', false)
            ->assertDontSee('Ishyiga', false)
            ->assertDontSee('Layer Vaccination', false)
            ->assertDontSee('Mineral Supplementation', false)
            ->assertDontSee('Costing a Season', false)
            ->assertDontSee('Cooperative Membership', false)
            ->assertDontSee('Heat Detection', false);
    }

    public function test_explore_courses_is_filterable_and_hides_unpublished_rows(): void
    {
        $this->get(route('catalog.courses'))
            ->assertOk()
            ->assertSee('Explore courses', false)
            ->assertSee('Farm Record Keeping', false)
            ->assertSee('Mastitis Detection', false)
            ->assertSee('Free', false)
            ->assertDontSee('Layer Vaccination', false)
            ->assertDontSee('Mineral Supplementation', false)
            ->assertDontSee('Silage from Maize', false)
            ->assertDontSee('Colostrum Management', false)
            ->assertDontSee('Market Gate Checks', false);

        $this->get(route('catalog.courses', ['page' => 2]))
            ->assertOk()
            ->assertSee('Aflatoxin Control', false)
            ->assertSee('Least-Cost Ration', false);

        $this->get(route('catalog.courses', ['platform' => 'feedgrid']))
            ->assertOk()
            ->assertSee('Aflatoxin Control', false)
            ->assertSee('Least-Cost Ration', false)
            ->assertDontSee('Mastitis Detection', false)
            ->assertDontSee('Farm Record Keeping', false);

        $this->get(route('catalog.courses', ['q' => 'mastitis']))
            ->assertOk()
            ->assertSee('Mastitis Detection', false)
            ->assertDontSee('Aflatoxin Control', false);

        $this->get(route('catalog.courses', [
            'platform' => 'feedgrid',
            'language' => 'Kinyarwanda',
            'price' => 'paid',
        ]))
            ->assertOk()
            ->assertSee('Nothing matches those filters', false)
            ->assertSee('Clear a filter', false)
            ->assertDontSee('Least-Cost Ration', false);
    }

    public function test_platform_pages_are_scoped_and_inactive_platforms_are_not_found(): void
    {
        $this->get(route('catalog.platforms'))
            ->assertOk()
            ->assertSee('OroraFarm', false)
            ->assertSee('Gemura', false)
            ->assertDontSee('Ubworozi', false);

        $this->get(route('catalog.platforms.show', ['platform' => 'gemura']))
            ->assertOk()
            ->assertSee('Gemura', false)
            ->assertSee('Milk hygiene', false)
            ->assertSee('Mastitis Detection', false)
            ->assertSee('Cold Chain Discipline', false)
            ->assertDontSee('Farm Record Keeping', false)
            ->assertDontSee('Aflatoxin Control', false)
            ->assertDontSee('Silage from Maize', false)
            ->assertDontSee('Layer Vaccination', false);

        $this->get(route('catalog.platforms.show', ['platform' => 'ubworozi']))
            ->assertNotFound();

        $this->get(route('catalog.platforms.show', ['platform' => 'ishyiga']))
            ->assertNotFound();
    }

    public function test_course_landing_shows_a_title_only_syllabus_and_a_sign_in_cta(): void
    {
        $this->get(route('catalog.courses.show', ['course' => 'mastitis-milk-hygiene']))
            ->assertOk()
            ->assertSee('Mastitis Detection', false)
            ->assertSee('Solange Nyirahabimana', false)
            ->assertSee('Certificate eligible', false)
            ->assertSee('Free', false)
            ->assertSee('Preview available', false)
            ->assertSee('Reading a CMT paddle', false)
            ->assertSee('Sign in to enrol', false)
            ->assertSee('Enrolment is required', false)
            ->assertSee('Instructor', false)
            ->assertDontSee('Hold the paddle level', false)
            ->assertDontSee('/learn/courses/mastitis-milk-hygiene/lessons', false);

        $this->get(route('catalog.courses.show', ['course' => 'evening-intake-lactometer']))
            ->assertOk()
            ->assertSee('Sign in to start learning', false)
            ->assertDontSee('Sign in to enrol', false);

        $this->get(route('catalog.courses.show', ['course' => 'silage-maize-stover-napier']))
            ->assertNotFound();

        $this->get(route('catalog.courses.show', ['course' => 'layer-vaccination-calendars']))
            ->assertNotFound();

        $this->get(route('catalog.courses.show', ['course' => 'mineral-supplementation-lactating']))
            ->assertNotFound();
    }

    public function test_certificate_verification_is_public_and_exposes_only_the_named_fields(): void
    {
        $this->get(route('certificates.lookup'))
            ->assertOk()
            ->assertSee('Certificate verification', false)
            ->assertSee('No login is required', false);

        $this->get(route('certificates.lookup', ['code' => 'OS-GEM-2026-1847']))
            ->assertRedirect(route('certificates.verify', ['code' => 'OS-GEM-2026-1847']));

        $this->get(route('certificates.verify', ['code' => 'OS-GEM-2026-1847']))
            ->assertOk()
            ->assertSee('data-experience="public"', false)
            ->assertSee('Certificate verified', false)
            ->assertSee('Dative Nyiranzeyimana', false)
            ->assertSee('Gemura', false)
            ->assertSee('14 Sep 2026', false);

        $this->get(route('certificates.verify', ['code' => 'OS-GEM-2026-1810']))
            ->assertOk()
            ->assertSee('Certificate revoked', false);

        $this->get(route('certificates.verify', ['code' => 'NOT-A-CODE']))
            ->assertOk()
            ->assertSee('Not a valid certificate', false);

        $this->get('/verify/OS-GEM-2026-1847')
            ->assertRedirect(route('certificates.verify', ['code' => 'OS-GEM-2026-1847']));
    }
}
