<?php

namespace App\Support\DemoData;

use App\Models\Academy;
use App\Models\Course;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Builder;

/**
 * Public catalogue query. Reads published courses on active platforms from
 * MySQL. Syllabus previews still come from the curriculum fixture until
 * modules and lessons are persisted.
 */
class PublicCatalog
{
    public const PER_PAGE = 6;

    /**
     * @return list<array<string, mixed>>
     */
    public static function publishedCourses(): array
    {
        return self::publishedQuery()
            ->orderBy('id')
            ->get()
            ->map(fn (Course $course) => self::present($course))
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function present(Course $course): array
    {
        $platform = $course->platform;
        $academy = $course->academy;

        return [
            'id' => $course->id,
            'slug' => $course->slug,
            'platform' => $platform->slug,
            'title' => $course->title,
            'summary' => $course->summary,
            'description' => $course->description,
            'instructor' => $course->instructor,
            'instructors' => array_values(array_filter([$course->instructor])),
            'status' => $course->status,
            'modules' => $course->modules,
            'lessons' => $course->lessons,
            'duration' => $course->duration,
            'enrolled' => $course->enrolled,
            'difficulty' => $course->difficulty,
            'language' => $course->language,
            'paid' => $course->paid,
            'certificate_eligible' => $course->certificate_eligible,
            'enrollment_required' => $course->enrollment_required,
            'category' => $course->category ?? '',
            'academy' => $academy?->name ?? '',
            'academy_slug' => $academy?->slug ?? '',
            'cover' => $course->cover ?? $platform->cover,
            'platform_name' => $platform->name,
            'platform_discipline' => $platform->discipline,
            'platform_tagline' => $platform->tagline,
            'href' => route('catalog.courses.show', ['course' => $course->slug]),
        ];
    }

    /**
     * @param  array<string, string>  $filters
     * @return list<array<string, mixed>>
     */
    public static function filter(array $filters = []): array
    {
        $query = self::publishedQuery();

        $platform = (string) ($filters['platform'] ?? '');
        $academy = (string) ($filters['academy'] ?? '');
        $difficulty = (string) ($filters['difficulty'] ?? '');
        $language = (string) ($filters['language'] ?? '');
        $price = (string) ($filters['price'] ?? '');
        $search = mb_strtolower(trim((string) ($filters['q'] ?? '')));

        if ($platform !== '') {
            $query->whereHas('platform', fn (Builder $builder) => $builder->where('slug', $platform));
        }

        if ($academy !== '') {
            $query->whereHas('academy', fn (Builder $builder) => $builder->where('slug', $academy));
        }

        if ($difficulty !== '') {
            $query->where('difficulty', $difficulty);
        }

        if ($language !== '') {
            $query->where('language', 'like', '%'.self::escapeLike($language).'%');
        }

        if ($price === 'free') {
            $query->where('paid', false);
        }

        if ($price === 'paid') {
            $query->where('paid', true);
        }

        if ($search !== '') {
            $term = '%'.self::escapeLike($search).'%';
            $query->where(function (Builder $builder) use ($term) {
                $builder->where('title', 'like', $term)
                    ->orWhere('summary', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        return $query->orderBy('id')
            ->get()
            ->map(fn (Course $course) => self::present($course))
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function featured(int $limit = 4): array
    {
        return self::publishedQuery()
            ->orderByDesc('enrolled')
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->map(fn (Course $course) => self::present($course))
            ->all();
    }

    public static function findPublished(string $slug): ?array
    {
        $course = self::publishedQuery()
            ->where('slug', $slug)
            ->first();

        return $course ? self::present($course) : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function activePlatforms(): array
    {
        return Platform::query()
            ->active()
            ->with([
                'academies' => fn ($query) => $query->where('status', 'published')->orderBy('name'),
            ])
            ->withCount([
                'courses as public_courses' => fn (Builder $query) => $query->where('status', 'published'),
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Platform $platform) => self::presentPlatform($platform))
            ->all();
    }

    public static function findActivePlatform(string $slug): ?array
    {
        $platform = Platform::query()
            ->active()
            ->with([
                'academies' => fn ($query) => $query->where('status', 'published')->orderBy('name'),
            ])
            ->withCount([
                'courses as public_courses' => fn (Builder $query) => $query->where('status', 'published'),
            ])
            ->where('slug', $slug)
            ->first();

        return $platform ? self::presentPlatform($platform) : null;
    }

    /**
     * @return array{courses: int, platforms: int}
     */
    public static function stats(): array
    {
        return [
            'courses' => self::publishedQuery()->count(),
            'platforms' => Platform::query()->active()->count(),
        ];
    }

    /**
     * Module and lesson titles only. No body, no media.
     *
     * @return list<array<string, mixed>>
     */
    public static function previewSyllabus(string $slug): array
    {
        $modules = [];

        foreach (Curriculum::modulesFor($slug) as $module) {
            $lessons = [];

            foreach ($module['lessons'] as $lesson) {
                $lessons[] = [
                    'title' => $lesson['title'],
                    'is_preview' => (bool) ($lesson['is_preview'] ?? false),
                ];
            }

            $modules[] = [
                'title' => $module['title'],
                'lessons' => $lessons,
            ];
        }

        return $modules;
    }

    /**
     * @return array<string, mixed>
     */
    public static function filterOptions(?string $platform = null): array
    {
        $platform = $platform !== null && $platform !== '' ? $platform : null;

        $courses = self::publishedQuery()
            ->when($platform, fn (Builder $query) => $query->whereHas(
                'platform',
                fn (Builder $builder) => $builder->where('slug', $platform),
            ))
            ->with(['platform', 'academy'])
            ->orderBy('id')
            ->get();

        $platforms = ['' => 'All platforms'];
        foreach (self::activePlatforms() as $row) {
            $platforms[$row['slug']] = $row['name'];
        }

        $academies = ['' => 'All academies'];
        foreach ($courses as $course) {
            if ($course->academy) {
                $academies[$course->academy->slug] = $course->academy->name;
            }
        }

        $difficulties = ['' => 'Any difficulty'];
        foreach ($courses as $course) {
            $difficulties[$course->difficulty] = $course->difficulty;
        }

        return [
            'platforms' => $platforms,
            'academies' => $academies,
            'difficulties' => $difficulties,
            'languages' => [
                '' => 'Any language',
                'English' => 'English',
                'Kinyarwanda' => 'Kinyarwanda',
            ],
            'prices' => [
                '' => 'Free or paid',
                'free' => 'Free',
                'paid' => 'Paid',
            ],
        ];
    }

    /**
     * @return Builder<Course>
     */
    private static function publishedQuery(): Builder
    {
        return Course::query()
            ->with(['platform', 'academy'])
            ->published()
            ->onActivePlatform();
    }

    /**
     * @return array<string, mixed>
     */
    private static function presentPlatform(Platform $platform): array
    {
        return [
            'slug' => $platform->slug,
            'name' => $platform->name,
            'discipline' => $platform->discipline,
            'tagline' => $platform->tagline,
            'description' => $platform->description,
            'steward' => $platform->steward,
            'region' => $platform->region,
            'status' => $platform->status,
            'cover' => $platform->cover,
            'public_courses' => (int) $platform->public_courses,
            'public_academies' => $platform->academies
                ->map(fn (Academy $academy) => [
                    'slug' => $academy->slug,
                    'platform' => $platform->slug,
                    'name' => $academy->name,
                    'status' => $academy->status,
                ])
                ->values()
                ->all(),
            'href' => route('catalog.platforms.show', ['platform' => $platform->slug]),
        ];
    }

    private static function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
    }
}
