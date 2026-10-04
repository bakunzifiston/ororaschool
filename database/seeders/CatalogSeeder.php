<?php

namespace Database\Seeders;

use App\Models\Academy;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Platform;
use App\Support\DemoData\Academies;
use App\Support\DemoData\Courses;
use App\Support\DemoData\IssuedCertificates;
use App\Support\DemoData\Platforms;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Platforms::all() as $index => $row) {
            Platform::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'discipline' => $row['discipline'],
                    'tagline' => $row['tagline'],
                    'description' => $row['description'],
                    'steward' => $row['steward'],
                    'region' => $row['region'],
                    'learner_count' => $row['learners'],
                    'instructor_count' => $row['instructors'],
                    'status' => $row['status'],
                    'sort_order' => $index + 1,
                    'joined_at' => Carbon::parse($row['joined']),
                    'completion_rate' => $row['completion_rate'],
                    'cover' => $row['cover'] ?? null,
                ],
            );
        }

        foreach (Academies::all() as $row) {
            $platform = Platform::query()->where('slug', $row['platform'])->firstOrFail();

            Academy::query()->create([
                'platform_id' => $platform->id,
                'slug' => $row['slug'],
                'name' => $row['name'],
                'status' => $row['status'],
            ]);
        }

        foreach (Courses::all() as $row) {
            $platform = Platform::query()->where('slug', $row['platform'])->firstOrFail();
            $academy = null;

            if (($row['academy_slug'] ?? '') !== '') {
                $academy = Academy::query()
                    ->where('platform_id', $platform->id)
                    ->where('slug', $row['academy_slug'])
                    ->first();
            }

            Course::query()->create([
                'platform_id' => $platform->id,
                'academy_id' => $academy?->id,
                'slug' => $row['slug'],
                'title' => $row['title'],
                'summary' => $row['summary'],
                'description' => $row['description'] ?: $row['summary'],
                'instructor' => $row['instructor'],
                'status' => $row['status'],
                'difficulty' => $row['difficulty'],
                'language' => $row['language'],
                'category' => $row['category'] ?: null,
                'modules' => $row['modules'],
                'lessons' => $row['lessons'],
                'duration' => $row['duration'],
                'enrolled' => $row['enrolled'],
                'paid' => (bool) $row['paid'],
                'certificate_eligible' => (bool) $row['certificate_eligible'],
                'enrollment_required' => (bool) $row['enrollment_required'],
                'cover' => $row['cover'] ?? null,
                'content_updated_at' => isset($row['updated']) ? Carbon::parse($row['updated']) : null,
            ]);
        }

        $this->call(CurriculumSeeder::class);
        $this->call(QuizSeeder::class);

        foreach (IssuedCertificates::all() as $row) {
            $platform = Platform::query()->where('slug', $row['platform'])->firstOrFail();
            $course = Course::query()->where('slug', $row['course_slug'])->firstOrFail();

            Certificate::query()->create([
                'code' => $row['code'],
                'platform_id' => $platform->id,
                'course_id' => $course->id,
                'learner_name' => $row['learner'],
                'learner_id' => $row['learner_id'],
                'issued_at' => Carbon::parse($row['issued']),
                'status' => $row['status'],
            ]);
        }
    }
}
