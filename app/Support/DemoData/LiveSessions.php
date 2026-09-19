<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Status: scheduled, live, completed, cancelled.
 */
class LiveSessions
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function forPlatform(string $platform): array
    {
        return array_values(array_filter(
            self::all(),
            fn (array $session) => $session['platform'] === $platform,
        ));
    }

    public static function find(int $id): ?array
    {
        foreach (self::all() as $session) {
            if ($session['id'] === $id) {
                return $session;
            }
        }

        return null;
    }

    public static function findForPlatform(int $id, string $platform): ?array
    {
        $session = self::find($id);

        if (! $session || $session['platform'] !== $platform) {
            return null;
        }

        return $session;
    }

    /**
     * Upcoming (scheduled or live), soonest first.
     *
     * @return list<array<string, mixed>>
     */
    public static function upcoming(string $platform, int $limit = 4): array
    {
        $rows = array_values(array_filter(
            self::forPlatform($platform),
            fn (array $session) => in_array($session['status'], ['scheduled', 'live'], true),
        ));

        return array_slice($rows, 0, $limit);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            ['id' => 1, 'platform' => 'gemura', 'title' => 'Reading CMT paddles together', 'instructor' => 'Solange Nyirahabimana', 'course' => 'Mastitis Detection and Milk Hygiene in Smallholder Herds', 'course_slug' => 'mastitis-milk-hygiene', 'starts' => '24 Sep 2026, 09:00', 'duration' => 45, 'status' => 'scheduled', 'url' => 'https://meet.ororaschool.rw/gemura/cmt-paddles', 'recording' => ''],
            ['id' => 2, 'platform' => 'gemura', 'title' => 'Evening intake lactometer checks', 'instructor' => 'Chantal Ingabire', 'course' => 'Evening Intake and Lactometer Checks', 'course_slug' => 'evening-intake-lactometer', 'starts' => '26 Sep 2026, 16:30', 'duration' => 40, 'status' => 'scheduled', 'url' => 'https://meet.ororaschool.rw/gemura/lactometer', 'recording' => ''],
            ['id' => 3, 'platform' => 'gemura', 'title' => 'Cold chain at Kinigi centre', 'instructor' => 'Josiane Kayitesi', 'course' => 'Cold Chain Discipline at Milk Collection Centres', 'course_slug' => 'cold-chain-collection-centres', 'starts' => '12 Sep 2026, 08:00', 'duration' => 50, 'status' => 'completed', 'url' => 'https://meet.ororaschool.rw/gemura/kinigi', 'recording' => 'https://recordings.ororaschool.rw/gemura/kinigi-12sep'],
            ['id' => 4, 'platform' => 'gemura', 'title' => 'Standing heat in Ankole–Friesian crosses', 'instructor' => 'Jean-Baptiste Habimana', 'course' => 'Heat Detection and AI Timing for Ankole–Friesian Crosses', 'course_slug' => 'heat-detection-ai-timing', 'starts' => '30 Sep 2026, 10:00', 'duration' => 60, 'status' => 'scheduled', 'url' => 'https://meet.ororaschool.rw/gemura/heat', 'recording' => ''],
            ['id' => 5, 'platform' => 'gemura', 'title' => 'Pit silage walk-through', 'instructor' => 'Solange Nyirahabimana', 'course' => 'Silage from Maize Stover and Napier Grass', 'course_slug' => 'silage-maize-stover-napier', 'starts' => '08 Sep 2026, 14:00', 'duration' => 35, 'status' => 'cancelled', 'url' => '', 'recording' => ''],

            ['id' => 11, 'platform' => 'buchapro', 'title' => 'Tagging a batch at Rubengera', 'instructor' => 'Olivier Mugisha', 'course' => 'Animal Identification and Ear-Tag Registration', 'course_slug' => 'animal-identification-eartags', 'starts' => '23 Sep 2026, 08:30', 'duration' => 50, 'status' => 'scheduled', 'url' => 'https://meet.ororaschool.rw/buchapro/rubengera-tags', 'recording' => ''],
            ['id' => 12, 'platform' => 'buchapro', 'title' => 'Nyabugogo gate-check drill', 'instructor' => 'Fabrice Gasana', 'course' => 'Livestock Market Gate Checks', 'course_slug' => 'market-gate-checks', 'starts' => '25 Sep 2026, 06:00', 'duration' => 40, 'status' => 'scheduled', 'url' => 'https://meet.ororaschool.rw/buchapro/nyabugogo', 'recording' => ''],
            ['id' => 13, 'platform' => 'buchapro', 'title' => 'Permit fields at a roadblock', 'instructor' => 'Claudine Uwimana', 'course' => 'Movement Permits and Livestock Transport Records', 'course_slug' => 'movement-permits-transport', 'starts' => '10 Sep 2026, 09:00', 'duration' => 45, 'status' => 'completed', 'url' => 'https://meet.ororaschool.rw/buchapro/permits', 'recording' => 'https://recordings.ororaschool.rw/buchapro/permits-10sep'],
            ['id' => 14, 'platform' => 'buchapro', 'title' => 'Kraal walk against the register', 'instructor' => 'Claudine Uwimana', 'course' => 'Kraal Register Reconciliation', 'course_slug' => 'kraal-register-reconciliation', 'starts' => '28 Sep 2026, 07:30', 'duration' => 55, 'status' => 'scheduled', 'url' => 'https://meet.ororaschool.rw/buchapro/kraal-walk', 'recording' => ''],
            ['id' => 15, 'platform' => 'buchapro', 'title' => 'Foot-and-mouth exposure window', 'instructor' => 'Olivier Mugisha', 'course' => 'Disease Outbreak Traceback Drills', 'course_slug' => 'outbreak-traceback-drills', 'starts' => '05 Sep 2026, 11:00', 'duration' => 60, 'status' => 'cancelled', 'url' => '', 'recording' => ''],

            ['id' => 21, 'platform' => 'ororafarm', 'title' => 'Costing a season in Nyagatare', 'instructor' => 'Thierry Munyaneza', 'course' => 'Costing a Season: Inputs, Labour and Margin', 'course_slug' => 'costing-a-season', 'starts' => '27 Sep 2026, 09:30', 'duration' => 50, 'status' => 'scheduled', 'url' => 'https://meet.ororaschool.rw/ororafarm/costing', 'recording' => ''],
            ['id' => 22, 'platform' => 'ororafarm', 'title' => 'Plot-book audit clinic', 'instructor' => 'Aline Mukamana', 'course' => 'Farm Record Keeping for Smallholder Enterprises', 'course_slug' => 'farm-record-keeping', 'starts' => '07 Sep 2026, 10:00', 'duration' => 40, 'status' => 'completed', 'url' => 'https://meet.ororaschool.rw/ororafarm/plot-book', 'recording' => 'https://recordings.ororaschool.rw/ororafarm/plot-book-07sep'],

            ['id' => 31, 'platform' => 'feedgrid', 'title' => 'Reading a feed price sheet', 'instructor' => 'Diane Iradukunda', 'course' => 'Least-Cost Ration Formulation with Local Ingredients', 'course_slug' => 'least-cost-ration-formulation', 'starts' => '22 Sep 2026, 14:00', 'duration' => 45, 'status' => 'scheduled', 'url' => 'https://meet.ororaschool.rw/feedgrid/price-sheet', 'recording' => ''],
            ['id' => 32, 'platform' => 'feedgrid', 'title' => 'Moisture walk in a bran store', 'instructor' => 'Espérance Twagirayezu', 'course' => 'Aflatoxin Control in Maize Bran Storage', 'course_slug' => 'aflatoxin-control-maize-bran', 'starts' => '03 Sep 2026, 08:30', 'duration' => 35, 'status' => 'completed', 'url' => 'https://meet.ororaschool.rw/feedgrid/moisture', 'recording' => 'https://recordings.ororaschool.rw/feedgrid/moisture-03sep'],
        ];
    }
}
