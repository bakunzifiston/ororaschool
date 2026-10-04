<?php

namespace App\Support\DemoData;

use App\Models\Platform;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * The estate: four live tenants plus two inactive ones so Super Admin lists
 * can paginate. Definitions follow the product brief: OroraFarm is farm
 * management, Gemura is dairy/livestock, BuchaPro is livestock traceability,
 * FeedGrid is feed and nutrition.
 */
class Platforms
{
    /**
     * Course counts are derived from the catalogue rather than typed in, so a
     * stat card, a sidebar and a table can never disagree about how many
     * courses a platform has.
     */
    public static function all(): array
    {
        return array_map(function (array $platform) {
            $courses = Courses::forPlatform($platform['slug']);

            return array_merge($platform, [
                'courses' => count($courses),
                'published' => count(array_filter($courses, fn ($c) => $c['status'] === 'published')),
                'academies' => Academies::countFor($platform['slug']),
                'users' => $platform['learners'] + $platform['instructors'] + 1,
            ]);
        }, self::records());
    }

    private static function records(): array
    {
        return [
            [
                'slug' => 'ororafarm',
                'name' => 'OroraFarm',
                'discipline' => 'Farm management',
                'tagline' => 'Season planning, plot records and enterprise costing for smallholdings.',
                'steward' => 'Aline Mukamana',
                'region' => 'Nyagatare, Eastern Province',
                'learners' => 4218,
                'instructors' => 9,
                'status' => 'active',
                'joined' => '11 Apr 2023',
                'created' => '11 Apr 2023',
                'description' => 'Season planning, plot records and enterprise costing for smallholdings across the Eastern Province.',
                'completion_rate' => 68,
                'cover' => 'images/platforms/ororafarm.jpg',
            ],
            [
                'slug' => 'gemura',
                'name' => 'Gemura',
                'discipline' => 'Dairy and livestock',
                'tagline' => 'Milk hygiene, collection-centre practice and herd fertility.',
                'steward' => 'Jean-Baptiste Habimana',
                'region' => 'Musanze, Northern Province',
                'learners' => 2967,
                'instructors' => 7,
                'status' => 'active',
                'joined' => '02 Jul 2023',
                'created' => '02 Jul 2023',
                'description' => 'Milk hygiene, collection-centre practice and herd fertility for the Northern Province dairy belt.',
                'completion_rate' => 74,
                'cover' => 'images/platforms/gemura.jpg',
            ],
            [
                'slug' => 'buchapro',
                'name' => 'BuchaPro',
                'discipline' => 'Livestock traceability',
                'tagline' => 'Animal identification, movement records and outbreak traceback.',
                'steward' => 'Claudine Uwimana',
                'region' => 'Kigali, Gasabo',
                'learners' => 1341,
                'instructors' => 5,
                'status' => 'active',
                'joined' => '19 Feb 2024',
                'created' => '19 Feb 2024',
                'description' => 'Animal identification, movement records and outbreak traceback for the Kigali livestock corridor.',
                'completion_rate' => 51,
                'cover' => 'images/platforms/buchapro.jpg',
            ],
            [
                'slug' => 'feedgrid',
                'name' => 'FeedGrid',
                'discipline' => 'Feed and nutrition',
                'tagline' => 'Ration formulation, feed safety and on-farm storage.',
                'steward' => 'Emmanuel Nshimiyimana',
                'region' => 'Rwamagana, Eastern Province',
                'learners' => 1886,
                'instructors' => 6,
                'status' => 'active',
                'joined' => '30 Jun 2024',
                'created' => '30 Jun 2024',
                'description' => 'Ration formulation, feed safety and on-farm storage for mills and cooperatives.',
                'completion_rate' => 62,
                'cover' => 'images/platforms/feedgrid.jpg',
            ],
            [
                'slug' => 'ubworozi',
                'name' => 'Ubworozi',
                'discipline' => 'Poultry systems',
                'tagline' => 'Housing, vaccination calendars and egg-lot records for small flocks.',
                'steward' => 'Marie-Claire Uwase',
                'region' => 'Huye, Southern Province',
                'learners' => 412,
                'instructors' => 2,
                'status' => 'inactive',
                'joined' => '08 Jan 2025',
                'created' => '08 Jan 2025',
                'description' => 'Deactivated in June 2026 after the poultry academy moved under OroraFarm. Kept so historical certificates still resolve.',
                'completion_rate' => 41,
            ],
            [
                'slug' => 'ishyiga',
                'name' => 'Ishyiga',
                'discipline' => 'Apiculture',
                'tagline' => 'Hive records, harvest hygiene and cooperative honey lots.',
                'steward' => 'Patrick Habineza',
                'region' => 'Nyabihu, Western Province',
                'learners' => 0,
                'instructors' => 1,
                'status' => 'inactive',
                'joined' => '14 Mar 2026',
                'created' => '14 Mar 2026',
                'description' => 'Draft tenant. Not yet activated — waiting on an owner and a first academy.',
                'completion_rate' => 0,
            ],
        ];
    }

    /**
     * Fixture rows with status overwritten when a persisted academy exists.
     *
     * @return list<array<string, mixed>>
     */
    public static function withPersistedStatus(): array
    {
        $records = Platform::query()->get()->keyBy('slug');

        $rows = array_map(function (array $platform) use ($records): array {
            $record = $records->get($platform['slug']);

            return $record instanceof Platform
                ? self::overlayRecord($platform, $record)
                : $platform;
        }, self::all());

        foreach ($records as $slug => $record) {
            if (self::fixtureExists($slug)) {
                continue;
            }

            $rows[] = self::presentRecord($record);
        }

        return $rows;
    }

    public static function find(string $slug): ?array
    {
        foreach (self::withPersistedStatus() as $platform) {
            if ($platform['slug'] === $slug) {
                return $platform;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function require(string $slug): array
    {
        $platform = self::find($slug);

        abort_unless($platform !== null && ($platform['status'] ?? '') !== 'deleted', 404);

        return $platform;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findWithPersistedStatus(string $slug): ?array
    {
        return self::find($slug);
    }

    public static function slugs(): array
    {
        $persisted = Platform::query()
            ->where('status', '!=', 'deleted')
            ->pluck('slug')
            ->all();

        return array_values(array_unique([
            ...array_column(self::all(), 'slug'),
            ...$persisted,
        ]));
    }

    /**
     * Live tenants only — guest linking and the workspace switcher should not
     * offer a deactivated academy.
     */
    /**
     * @return list<array<string, mixed>>
     */
    public static function visible(): array
    {
        return array_values(array_filter(
            self::withPersistedStatus(),
            fn (array $platform) => $platform['status'] !== 'deleted',
        ));
    }

    public static function active(): array
    {
        return array_values(array_filter(
            self::visible(),
            fn (array $platform) => $platform['status'] === 'active',
        ));
    }

    private static function fixtureExists(string $slug): bool
    {
        foreach (self::all() as $platform) {
            if ($platform['slug'] === $slug) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $platform
     * @return array<string, mixed>
     */
    private static function overlayRecord(array $platform, Platform $record): array
    {
        $platform['status'] = $record->status;
        $platform['name'] = $record->name;
        $platform['description'] = $record->description;
        $platform['discipline'] = $record->discipline;
        $platform['tagline'] = $record->tagline;
        $platform['steward'] = $record->steward;
        $platform['region'] = $record->region;

        return $platform;
    }

    /**
     * @return array<string, mixed>
     */
    private static function presentRecord(Platform $record): array
    {
        $courses = Courses::forPlatform($record->slug);

        return [
            'slug' => $record->slug,
            'name' => $record->name,
            'discipline' => $record->discipline,
            'tagline' => $record->tagline,
            'steward' => $record->steward,
            'region' => $record->region,
            'learners' => $record->learner_count,
            'instructors' => $record->instructor_count,
            'status' => $record->status,
            'joined' => $record->joined_at?->format('d M Y') ?? '',
            'created' => $record->created_at?->format('d M Y') ?? now()->format('d M Y'),
            'description' => $record->description,
            'completion_rate' => $record->completion_rate,
            'cover' => $record->cover,
            'courses' => count($courses),
            'published' => count(array_filter($courses, fn (array $course): bool => $course['status'] === 'published')),
            'academies' => Academies::countFor($record->slug),
            'users' => $record->users()->count() + $record->learner_count + $record->instructor_count,
        ];
    }
}
