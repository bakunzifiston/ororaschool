<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Course catalogue. Content follows the brief's platform definitions, so
 * OroraFarm courses are farm-management subjects and BuchaPro courses are
 * traceability subjects. `duration` is minutes. `status` is a lifecycle state,
 * never a colour — the badge component owns the colour mapping.
 */
class Courses
{
    public static function all(): array
    {
        return array_map([self::class, 'hydrate'], self::records());
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function records(): array
    {
        return [
            // ---- OroraFarm: farm management ---------------------------------
            [
                'slug' => 'farm-record-keeping',
                'platform' => 'ororafarm',
                'title' => 'Farm Record Keeping for Smallholder Enterprises',
                'summary' => 'Keep a plot book that survives an audit: planting dates, input receipts, labour days and harvest weights recorded so a season can actually be costed.',
                'instructor' => 'Thierry Munyaneza',
                'status' => 'published',
                'modules' => 5, 'lessons' => 17, 'duration' => 245, 'enrolled' => 731,
                'level' => 'Foundation', 'updated' => '02 Sep 2026',
            ],
            [
                'slug' => 'season-planning-terraces',
                'platform' => 'ororafarm',
                'title' => 'Season Planning and Plot Mapping on Terraced Land',
                'summary' => 'Sketch and number terraced plots, rotate crops across them, and plan a season against the two rainfall windows.',
                'instructor' => 'Aline Mukamana',
                'status' => 'published',
                'modules' => 4, 'lessons' => 13, 'duration' => 190, 'enrolled' => 512,
                'level' => 'Foundation', 'updated' => '21 Aug 2026',
            ],
            [
                'slug' => 'costing-a-season',
                'platform' => 'ororafarm',
                'title' => 'Costing a Season: Inputs, Labour and Margin',
                'summary' => 'Work out what a season actually cost, including unpaid family labour, and decide which enterprise to expand next year.',
                'instructor' => 'Thierry Munyaneza',
                'status' => 'pending_review',
                'modules' => 6, 'lessons' => 19, 'duration' => 300, 'enrolled' => 0,
                'level' => 'Intermediate', 'updated' => '15 Sep 2026',
            ],
            [
                'slug' => 'cooperative-share-records',
                'platform' => 'ororafarm',
                'title' => 'Cooperative Membership and Share Records',
                'summary' => 'Maintain a member register, track share contributions and reconcile the books before the annual general meeting.',
                'instructor' => 'Aline Mukamana',
                'status' => 'draft',
                'modules' => 3, 'lessons' => 8, 'duration' => 120, 'enrolled' => 0,
                'level' => 'Intermediate', 'updated' => '18 Sep 2026',
            ],

            // ---- Gemura: dairy and livestock -------------------------------
            [
                'slug' => 'mastitis-milk-hygiene',
                'platform' => 'gemura',
                'title' => 'Mastitis Detection and Milk Hygiene in Smallholder Herds',
                'summary' => 'Catch subclinical mastitis with the California Mastitis Test and build a milking routine that keeps somatic cell counts inside collection-centre limits.',
                'instructor' => 'Solange Nyirahabimana',
                'status' => 'published',
                'modules' => 5, 'lessons' => 18, 'duration' => 260, 'enrolled' => 612,
                'level' => 'Intermediate', 'updated' => '30 Aug 2026',
            ],
            [
                'slug' => 'cold-chain-collection-centres',
                'platform' => 'gemura',
                'title' => 'Cold Chain Discipline at Milk Collection Centres',
                'summary' => 'Temperature logging, lactometer checks and rejection protocols for centre operators handling evening and morning intake.',
                'instructor' => 'Josiane Kayitesi',
                'status' => 'published',
                'modules' => 4, 'lessons' => 12, 'duration' => 175, 'enrolled' => 438,
                'level' => 'Foundation', 'updated' => '04 Sep 2026',
            ],
            [
                'slug' => 'heat-detection-ai-timing',
                'platform' => 'gemura',
                'title' => 'Heat Detection and AI Timing for Ankole–Friesian Crosses',
                'summary' => 'Read standing heat correctly, apply the morning–evening rule and record service dates so conception rates stop slipping.',
                'instructor' => 'Jean-Baptiste Habimana',
                'status' => 'approved',
                'modules' => 6, 'lessons' => 21, 'duration' => 320, 'enrolled' => 0,
                'level' => 'Advanced', 'updated' => '14 Sep 2026',
            ],
            [
                'slug' => 'silage-maize-stover-napier',
                'platform' => 'gemura',
                'title' => 'Silage from Maize Stover and Napier Grass',
                'summary' => 'Chop length, wilting, compaction and sealing for pit and tube silage through the long dry season.',
                'instructor' => 'Solange Nyirahabimana',
                'status' => 'draft',
                'modules' => 3, 'lessons' => 7, 'duration' => 110, 'enrolled' => 0,
                'level' => 'Foundation', 'updated' => '17 Sep 2026',
            ],
            [
                'slug' => 'evening-intake-lactometer',
                'platform' => 'gemura',
                'title' => 'Evening Intake and Lactometer Checks',
                'summary' => 'Run the evening collection without losing the cold chain: lactometer reading, rejection log and the hand-over to the tanker.',
                'instructor' => 'Chantal Ingabire',
                'status' => 'published',
                'modules' => 3, 'lessons' => 9, 'duration' => 140, 'enrolled' => 286,
                'level' => 'Foundation', 'updated' => '11 Sep 2026',
            ],
            [
                'slug' => 'colostrum-calf-rearing',
                'platform' => 'gemura',
                'title' => 'Colostrum Management and Calf Rearing',
                'summary' => 'Time the first feed, store surplus colostrum and keep the calf hutch dry through the rains.',
                'instructor' => 'Jean-Baptiste Habimana',
                'status' => 'pending_review',
                'modules' => 4, 'lessons' => 11, 'duration' => 165, 'enrolled' => 0,
                'level' => 'Intermediate', 'updated' => '18 Sep 2026',
            ],

            // ---- BuchaPro: livestock traceability --------------------------
            [
                'slug' => 'animal-identification-eartags',
                'platform' => 'buchapro',
                'title' => 'Animal Identification and Ear-Tag Registration',
                'summary' => 'Apply and register ear tags correctly, handle replacements for lost tags, and keep the herd register matching what is standing in the kraal.',
                'instructor' => 'Olivier Mugisha',
                'status' => 'published',
                'modules' => 4, 'lessons' => 14, 'duration' => 205, 'enrolled' => 294,
                'level' => 'Foundation', 'updated' => '28 Aug 2026',
            ],
            [
                'slug' => 'movement-permits-transport',
                'platform' => 'buchapro',
                'title' => 'Movement Permits and Livestock Transport Records',
                'summary' => 'Complete a movement permit that will pass a roadblock check, and log arrivals and departures against the district register.',
                'instructor' => 'Claudine Uwimana',
                'status' => 'published',
                'modules' => 5, 'lessons' => 16, 'duration' => 240, 'enrolled' => 187,
                'level' => 'Intermediate', 'updated' => '09 Sep 2026',
            ],
            [
                'slug' => 'traceable-chain-kraal-abattoir',
                'platform' => 'buchapro',
                'title' => 'Building a Traceable Chain from Kraal to Abattoir',
                'summary' => 'Link animal, owner, transporter and slaughter batch so a carcass can be traced back to the farm of origin within a day.',
                'instructor' => 'Fabrice Gasana',
                'status' => 'pending_review',
                'modules' => 7, 'lessons' => 23, 'duration' => 395, 'enrolled' => 0,
                'level' => 'Advanced', 'updated' => '16 Sep 2026',
            ],
            [
                'slug' => 'outbreak-traceback-drills',
                'platform' => 'buchapro',
                'title' => 'Disease Outbreak Traceback Drills',
                'summary' => 'Practise a foot-and-mouth traceback against the register: find every contact animal and every movement in the exposure window.',
                'instructor' => 'Olivier Mugisha',
                'status' => 'draft',
                'modules' => 4, 'lessons' => 10, 'duration' => 165, 'enrolled' => 0,
                'level' => 'Advanced', 'updated' => '19 Sep 2026',
            ],
            [
                'slug' => 'kraal-register-reconciliation',
                'platform' => 'buchapro',
                'title' => 'Kraal Register Reconciliation',
                'summary' => 'Walk the kraal against the paper register, mark missing tags and write the discrepancy report the district officer will accept.',
                'instructor' => 'Claudine Uwimana',
                'status' => 'published',
                'modules' => 3, 'lessons' => 8, 'duration' => 125, 'enrolled' => 156,
                'level' => 'Foundation', 'updated' => '05 Sep 2026',
            ],
            [
                'slug' => 'market-gate-checks',
                'platform' => 'buchapro',
                'title' => 'Livestock Market Gate Checks',
                'summary' => 'Read a movement permit at the Nyabugogo gate, match the ear tag and refuse an animal that is not on the batch list.',
                'instructor' => 'Fabrice Gasana',
                'status' => 'approved',
                'modules' => 4, 'lessons' => 12, 'duration' => 180, 'enrolled' => 0,
                'level' => 'Intermediate', 'updated' => '12 Sep 2026',
            ],

            // ---- FeedGrid: feed and nutrition ------------------------------
            [
                'slug' => 'least-cost-ration-formulation',
                'platform' => 'feedgrid',
                'title' => 'Least-Cost Ration Formulation with Local Ingredients',
                'summary' => 'Balance maize bran, cotton seed cake, brewers grain and mineral premix against a target crude protein without overspending.',
                'instructor' => 'Diane Iradukunda',
                'status' => 'published',
                'modules' => 6, 'lessons' => 20, 'duration' => 355, 'enrolled' => 521,
                'level' => 'Advanced', 'updated' => '02 Sep 2026',
            ],
            [
                'slug' => 'aflatoxin-control-maize-bran',
                'platform' => 'feedgrid',
                'title' => 'Aflatoxin Control in Maize Bran Storage',
                'summary' => 'Moisture thresholds, pallet stacking and visual screening to keep Aspergillus out of stored feed ingredients.',
                'instructor' => 'Espérance Twagirayezu',
                'status' => 'published',
                'modules' => 4, 'lessons' => 13, 'duration' => 190, 'enrolled' => 347,
                'level' => 'Intermediate', 'updated' => '28 Jul 2026',
            ],
            [
                'slug' => 'mineral-supplementation-lactating',
                'platform' => 'feedgrid',
                'title' => 'Mineral Supplementation for Lactating Cows',
                'summary' => 'Superseded by the 2026 ration formulation track; kept available for cohorts that started before July.',
                'instructor' => 'Emmanuel Nshimiyimana',
                'status' => 'archived',
                'modules' => 3, 'lessons' => 8, 'duration' => 120, 'enrolled' => 74,
                'level' => 'Foundation', 'updated' => '30 Nov 2025',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $course
     * @return array<string, mixed>
     */
    private static function hydrate(array $course): array
    {
        $extra = self::extras()[$course['slug']] ?? [];

        return array_merge([
            'difficulty' => $course['level'] ?? 'Foundation',
            'language' => 'English',
            'paid' => false,
            'certificate_eligible' => ($course['status'] ?? '') === 'published',
            'enrollment_required' => true,
            'description' => $course['summary'] ?? '',
            'academy' => '',
            'academy_slug' => '',
            'category' => '',
            'instructors' => array_values(array_filter([$course['instructor'] ?? ''])),
        ], $course, $extra);
    }

    /**
     * F4 catalogue fields that sit on top of the F1 course records.
     *
     * @return array<string, array<string, mixed>>
     */
    private static function extras(): array
    {
        return [
            'farm-record-keeping' => ['academy' => 'Plot records and costing', 'academy_slug' => 'plot-records', 'category' => 'Field books'],
            'season-planning-terraces' => ['academy' => 'Season planning', 'academy_slug' => 'season-planning', 'category' => 'Plot mapping'],
            'costing-a-season' => ['academy' => 'Plot records and costing', 'academy_slug' => 'plot-records', 'category' => 'Margins'],
            'cooperative-share-records' => ['academy' => 'Cooperative books', 'academy_slug' => 'cooperative-books', 'category' => 'Share register'],
            'mastitis-milk-hygiene' => ['academy' => 'Milk hygiene', 'academy_slug' => 'milk-hygiene', 'category' => 'Milking routine', 'language' => 'English / Kinyarwanda', 'certificate_eligible' => true],
            'cold-chain-collection-centres' => ['academy' => 'Collection centres', 'academy_slug' => 'collection-centres', 'category' => 'Temperature logs'],
            'heat-detection-ai-timing' => ['academy' => 'Herd fertility', 'academy_slug' => 'herd-fertility', 'category' => 'Artificial insemination', 'certificate_eligible' => true],
            'silage-maize-stover-napier' => ['academy' => 'Herd fertility', 'academy_slug' => 'herd-fertility', 'category' => 'Dry-season feed'],
            'evening-intake-lactometer' => ['academy' => 'Collection centres', 'academy_slug' => 'collection-centres', 'category' => 'Evening intake', 'enrollment_required' => false],
            'colostrum-calf-rearing' => ['academy' => 'Milk hygiene', 'academy_slug' => 'milk-hygiene', 'category' => 'Calf rearing'],
            'animal-identification-eartags' => ['academy' => 'Animal identification', 'academy_slug' => 'identification', 'category' => 'Ear-tag application', 'certificate_eligible' => true],
            'movement-permits-transport' => ['academy' => 'Movement and transport', 'academy_slug' => 'movement', 'category' => 'Roadblock checks'],
            'traceable-chain-kraal-abattoir' => ['academy' => 'Outbreak traceback', 'academy_slug' => 'traceback', 'category' => 'Slaughter batches'],
            'outbreak-traceback-drills' => ['academy' => 'Outbreak traceback', 'academy_slug' => 'traceback', 'category' => 'Exposure windows'],
            'kraal-register-reconciliation' => ['academy' => 'Animal identification', 'academy_slug' => 'identification', 'category' => 'Herd register'],
            'market-gate-checks' => ['academy' => 'Movement and transport', 'academy_slug' => 'movement', 'category' => 'Market gates'],
            'least-cost-ration-formulation' => ['academy' => 'Ration formulation', 'academy_slug' => 'ration', 'category' => 'Local ingredients'],
            'aflatoxin-control-maize-bran' => ['academy' => 'Feed safety and storage', 'academy_slug' => 'feed-safety', 'category' => 'Moisture control'],
            'mineral-supplementation-lactating' => ['academy' => 'Feed safety and storage', 'academy_slug' => 'feed-safety', 'category' => 'Mineral licks', 'certificate_eligible' => false],
        ];
    }

    public static function findForPlatform(string $slug, string $platform): ?array
    {
        $course = self::find($slug);

        if (! $course || $course['platform'] !== $platform) {
            return null;
        }

        return $course;
    }

    public static function forPlatform(string $slug): array
    {
        return array_values(array_filter(
            self::all(),
            fn (array $course) => $course['platform'] === $slug,
        ));
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $course) {
            if ($course['slug'] === $slug) {
                return $course;
            }
        }

        return null;
    }

    /**
     * Enrolments for the learner persona, newest first.
     */
    public static function enrolments(): array
    {
        return [
            ['course' => 'mastitis-milk-hygiene', 'status' => 'active', 'progress' => 72, 'lessons_done' => 4, 'next' => 'Audio: a clean milking sequence', 'due' => '28 Sep 2026'],
            ['course' => 'movement-permits-transport', 'status' => 'active', 'progress' => 10, 'lessons_done' => 1, 'next' => 'Field notes', 'due' => '08 Oct 2026'],
            ['course' => 'aflatoxin-control-maize-bran', 'status' => 'active', 'progress' => 9, 'lessons_done' => 1, 'next' => 'Field notes', 'due' => '05 Nov 2026'],
            ['course' => 'cold-chain-collection-centres', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 12, 'next' => 'Certificate issued', 'due' => 'Completed'],
            ['course' => 'animal-identification-eartags', 'status' => 'completed', 'progress' => 100, 'lessons_done' => 14, 'next' => 'Certificate issued', 'due' => 'Completed'],
        ];
    }

    public static function activity(): array
    {
        return [
            ['actor' => 'Jean-Baptiste Habimana', 'action' => 'submitted for review', 'target' => 'Heat Detection and AI Timing for Ankole–Friesian Crosses', 'platform' => 'Gemura', 'at' => '18 minutes ago'],
            ['actor' => 'Olivier Mugisha', 'action' => 'approved', 'target' => 'Animal Identification and Ear-Tag Registration', 'platform' => 'BuchaPro', 'at' => '2 hours ago'],
            ['actor' => 'Josiane Kayitesi', 'action' => 'enrolled 34 learners into', 'target' => 'Cold Chain Discipline at Milk Collection Centres', 'platform' => 'Gemura', 'at' => '4 hours ago'],
            ['actor' => 'Diane Iradukunda', 'action' => 'published', 'target' => 'Least-Cost Ration Formulation with Local Ingredients', 'platform' => 'FeedGrid', 'at' => 'Yesterday'],
            ['actor' => 'Aline Mukamana', 'action' => 'archived', 'target' => 'Mineral Supplementation for Lactating Cows', 'platform' => 'FeedGrid', 'at' => '2 days ago'],
            ['actor' => 'Thierry Munyaneza', 'action' => 'created', 'target' => 'Cooperative Membership and Share Records', 'platform' => 'OroraFarm', 'at' => '3 days ago'],
        ];
    }
}
