<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Issued certificates on a platform. Codes follow the global numbering format.
 */
class IssuedCertificates
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function forPlatform(string $platform): array
    {
        return array_values(array_filter(
            self::all(),
            fn (array $row) => $row['platform'] === $platform,
        ));
    }

    public static function findByCode(string $code): ?array
    {
        foreach (self::all() as $row) {
            if ($row['code'] === $code) {
                return $row;
            }
        }

        return null;
    }

    public static function findForPlatform(string $code, string $platform): ?array
    {
        $row = self::findByCode($code);

        if (! $row || $row['platform'] !== $platform) {
            return null;
        }

        return $row;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function forLearner(int $learnerId): array
    {
        return array_values(array_filter(
            self::all(),
            fn (array $row) => $row['learner_id'] === $learnerId,
        ));
    }

    public static function countFor(string $platform): int
    {
        return count(self::forPlatform($platform));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            ['code' => 'OS-GEM-2026-1847', 'platform' => 'gemura', 'learner' => 'Dative Nyiranzeyimana', 'learner_id' => 109, 'course' => 'Mastitis Detection and Milk Hygiene in Smallholder Herds', 'course_slug' => 'mastitis-milk-hygiene', 'issued' => '14 Sep 2026', 'status' => 'valid'],
            ['code' => 'OS-GEM-2026-1841', 'platform' => 'gemura', 'learner' => 'Placide Bizimana', 'learner_id' => 101, 'course' => 'Cold Chain Discipline at Milk Collection Centres', 'course_slug' => 'cold-chain-collection-centres', 'issued' => '02 Sep 2026', 'status' => 'valid'],
            ['code' => 'OS-BCH-2026-0498', 'platform' => 'buchapro', 'learner' => 'Placide Bizimana', 'learner_id' => 101, 'course' => 'Animal Identification and Ear-Tag Registration', 'course_slug' => 'animal-identification-eartags', 'issued' => '14 Aug 2026', 'status' => 'valid'],
            ['code' => 'OS-GEM-2026-1832', 'platform' => 'gemura', 'learner' => 'Félicien Nkurunziza', 'learner_id' => 112, 'course' => 'Evening Intake and Lactometer Checks', 'course_slug' => 'evening-intake-lactometer', 'issued' => '28 Aug 2026', 'status' => 'valid'],
            ['code' => 'OS-GEM-2026-1810', 'platform' => 'gemura', 'learner' => 'Célestin Ndayisaba', 'learner_id' => 103, 'course' => 'Mastitis Detection and Milk Hygiene in Smallholder Herds', 'course_slug' => 'mastitis-milk-hygiene', 'issued' => '11 Aug 2026', 'status' => 'revoked'],
            ['code' => 'OS-GEM-2026-1794', 'platform' => 'gemura', 'learner' => 'Sandrine Mukamana', 'learner_id' => 107, 'course' => 'Evening Intake and Lactometer Checks', 'course_slug' => 'evening-intake-lactometer', 'issued' => '30 Jul 2026', 'status' => 'valid'],
            ['code' => 'OS-GEM-2026-1766', 'platform' => 'gemura', 'learner' => 'Vianney Habumuremyi', 'learner_id' => 108, 'course' => 'Cold Chain Discipline at Milk Collection Centres', 'course_slug' => 'cold-chain-collection-centres', 'issued' => '18 Jul 2026', 'status' => 'valid'],

            ['code' => 'OS-BCH-2026-0521', 'platform' => 'buchapro', 'learner' => 'Alphonsine Mukarugwiza', 'learner_id' => 115, 'course' => 'Animal Identification and Ear-Tag Registration', 'course_slug' => 'animal-identification-eartags', 'issued' => '07 Sep 2026', 'status' => 'valid'],
            ['code' => 'OS-BCH-2026-0514', 'platform' => 'buchapro', 'learner' => 'Callixte Nsengiyumva', 'learner_id' => 120, 'course' => 'Kraal Register Reconciliation', 'course_slug' => 'kraal-register-reconciliation', 'issued' => '01 Sep 2026', 'status' => 'valid'],
            ['code' => 'OS-BCH-2026-0502', 'platform' => 'buchapro', 'learner' => 'Théoneste Bagabo', 'learner_id' => 106, 'course' => 'Movement Permits and Livestock Transport Records', 'course_slug' => 'movement-permits-transport', 'issued' => '22 Aug 2026', 'status' => 'valid'],
            ['code' => 'OS-BCH-2026-0488', 'platform' => 'buchapro', 'learner' => 'Epiphanie Nyirahabimana', 'learner_id' => 117, 'course' => 'Animal Identification and Ear-Tag Registration', 'course_slug' => 'animal-identification-eartags', 'issued' => '09 Aug 2026', 'status' => 'revoked'],
            ['code' => 'OS-BCH-2026-0471', 'platform' => 'buchapro', 'learner' => 'Pascal Niyonsenga', 'learner_id' => 114, 'course' => 'Kraal Register Reconciliation', 'course_slug' => 'kraal-register-reconciliation', 'issued' => '28 Jul 2026', 'status' => 'valid'],
            ['code' => 'OS-BCH-2026-0455', 'platform' => 'buchapro', 'learner' => 'Damascène Ndayambaje', 'learner_id' => 118, 'course' => 'Movement Permits and Livestock Transport Records', 'course_slug' => 'movement-permits-transport', 'issued' => '14 Jul 2026', 'status' => 'valid'],

            ['code' => 'OS-ORF-2026-0902', 'platform' => 'ororafarm', 'learner' => 'Immaculée Nyiransabimana', 'learner_id' => 102, 'course' => 'Farm Record Keeping for Smallholder Enterprises', 'course_slug' => 'farm-record-keeping', 'issued' => '20 Aug 2026', 'status' => 'valid'],
            ['code' => 'OS-ORF-2026-0888', 'platform' => 'ororafarm', 'learner' => 'Straton Habimana', 'learner_id' => 122, 'course' => 'Season Planning and Plot Mapping on Terraced Land', 'course_slug' => 'season-planning-terraces', 'issued' => '02 Aug 2026', 'status' => 'valid'],

            ['code' => 'OS-FED-2026-0521', 'platform' => 'feedgrid', 'learner' => 'Béatrice Uwamahoro', 'learner_id' => 104, 'course' => 'Aflatoxin Control in Maize Bran Storage', 'course_slug' => 'aflatoxin-control-maize-bran', 'issued' => '07 Sep 2026', 'status' => 'valid'],
            ['code' => 'OS-FED-2026-0490', 'platform' => 'feedgrid', 'learner' => 'Gaudence Mukarugambwa', 'learner_id' => 121, 'course' => 'Least-Cost Ration Formulation with Local Ingredients', 'course_slug' => 'least-cost-ration-formulation', 'issued' => '19 Aug 2026', 'status' => 'valid'],
        ];
    }
}
