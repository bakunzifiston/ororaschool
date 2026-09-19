<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class Quizzes
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function forPlatform(string $platform): array
    {
        return array_values(array_filter(
            self::all(),
            fn (array $quiz) => $quiz['platform'] === $platform,
        ));
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $quiz) {
            if ($quiz['slug'] === $slug) {
                return $quiz;
            }
        }

        return null;
    }

    public static function findForPlatform(string $slug, string $platform): ?array
    {
        $quiz = self::find($slug);

        if (! $quiz || $quiz['platform'] !== $platform) {
            return null;
        }

        return $quiz;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'slug' => 'cmt-paddle-reading',
                'platform' => 'gemura',
                'title' => 'CMT paddle reading',
                'course' => 'Mastitis Detection and Milk Hygiene in Smallholder Herds',
                'course_slug' => 'mastitis-milk-hygiene',
                'lesson' => 'Scoring trace, weak positive and strong positive',
                'questions' => 6, 'pass' => 70, 'attempts' => 3, 'avg' => 78,
                'items' => [
                    ['prompt' => 'A trace reaction on the CMT paddle means the milk is…', 'options' => ['Safe to bulk without a re-test', 'Worth a second stripping and a re-test', 'An automatic rejection', 'Only a problem in evening milk'], 'correct' => 1],
                    ['prompt' => 'Fore-stripping is done…', 'options' => ['After the cluster is attached', 'Into the bulk tank to save time', 'Onto a paddle or the ground, before attaching', 'Only when the cow has kicked'], 'correct' => 2],
                    ['prompt' => 'Collection-centre rejection is triggered when…', 'options' => ['One cow in the kraal failed CMT', 'The lactometer reading is outside the band', 'The tanker is late', 'The farmer forgot their cooperative card'], 'correct' => 1],
                ],
            ],
            [
                'slug' => 'lactometer-evening-intake',
                'platform' => 'gemura',
                'title' => 'Lactometer at evening intake',
                'course' => 'Evening Intake and Lactometer Checks',
                'course_slug' => 'evening-intake-lactometer',
                'lesson' => 'Introduction',
                'questions' => 5, 'pass' => 80, 'attempts' => 2, 'avg' => 71,
                'items' => [
                    ['prompt' => 'The lactometer is read…', 'options' => ['At the meniscus, at eye level', 'From above, looking down the stem', 'After the milk has stood overnight', 'Only on morning intake'], 'correct' => 0],
                    ['prompt' => 'A reading below the band usually means…', 'options' => ['The cow is in heat', 'Added water', 'High butterfat', 'A dirty paddle'], 'correct' => 1],
                ],
            ],
            [
                'slug' => 'cold-chain-rejection',
                'platform' => 'gemura',
                'title' => 'Cold chain rejection log',
                'course' => 'Cold Chain Discipline at Milk Collection Centres',
                'course_slug' => 'cold-chain-collection-centres',
                'lesson' => 'Field notes',
                'questions' => 4, 'pass' => 75, 'attempts' => 3, 'avg' => 84,
                'items' => [
                    ['prompt' => 'Evening milk that arrives above 8°C should be…', 'options' => ['Bulked and cooled later', 'Logged and rejected', 'Discounted 10%', 'Sent to the nearest house'], 'correct' => 1],
                ],
            ],
            [
                'slug' => 'ear-tag-placement',
                'platform' => 'buchapro',
                'title' => 'Ear-tag placement',
                'course' => 'Animal Identification and Ear-Tag Registration',
                'course_slug' => 'animal-identification-eartags',
                'lesson' => 'Placing an ear tag without tearing',
                'questions' => 5, 'pass' => 80, 'attempts' => 2, 'avg' => 73,
                'items' => [
                    ['prompt' => 'The tag goes in the…', 'options' => ['Tip of the ear, for visibility', 'Middle third of the ear, between the ridges', 'Base of the horn', 'Dewlap, so it cannot snag'], 'correct' => 1],
                    ['prompt' => 'A lost tag is replaced by…', 'options' => ['Reusing the old number on a new tag', 'Issuing a new number and recording both', 'Leaving the animal untagged until the AGM', 'Writing the number on the hide'], 'correct' => 1],
                    ['prompt' => 'The herd register must match…', 'options' => ['The cooperative share book', 'What is standing in the kraal', 'Last year’s census only', 'The abattoir batch list'], 'correct' => 1],
                ],
            ],
            [
                'slug' => 'movement-permit-fields',
                'platform' => 'buchapro',
                'title' => 'Movement permit fields',
                'course' => 'Movement Permits and Livestock Transport Records',
                'course_slug' => 'movement-permits-transport',
                'lesson' => 'Introduction',
                'questions' => 6, 'pass' => 70, 'attempts' => 3, 'avg' => 69,
                'items' => [
                    ['prompt' => 'A roadblock check fails if…', 'options' => ['The driver is not the owner', 'The ear tag is not on the permit', 'The journey is under 10 km', 'It is a market day'], 'correct' => 1],
                    ['prompt' => 'Arrivals at the destination are logged against…', 'options' => ['The district register', 'The driver’s phone', 'The cooperative minutes', 'The previous season’s book'], 'correct' => 0],
                ],
            ],
            [
                'slug' => 'kraal-discrepancy',
                'platform' => 'buchapro',
                'title' => 'Kraal discrepancy report',
                'course' => 'Kraal Register Reconciliation',
                'course_slug' => 'kraal-register-reconciliation',
                'lesson' => 'Field notes',
                'questions' => 4, 'pass' => 75, 'attempts' => 2, 'avg' => 81,
                'items' => [
                    ['prompt' => 'An animal in the kraal with no tag is recorded as…', 'options' => ['A surplus, pending identification', 'A theft', 'A calf under 3 months, ignored', 'A market return'], 'correct' => 0],
                ],
            ],
            [
                'slug' => 'plot-book-audit',
                'platform' => 'ororafarm',
                'title' => 'Plot book audit',
                'course' => 'Farm Record Keeping for Smallholder Enterprises',
                'course_slug' => 'farm-record-keeping',
                'lesson' => 'Introduction',
                'questions' => 5, 'pass' => 70, 'attempts' => 3, 'avg' => 76,
                'items' => [
                    ['prompt' => 'A plot book that survives an audit records…', 'options' => ['Only harvest weights', 'Planting dates, receipts, labour days and harvest weights', 'The cooperative chairman’s signature', 'Rainfall rumours'], 'correct' => 1],
                ],
            ],
            [
                'slug' => 'moisture-thresholds',
                'platform' => 'feedgrid',
                'title' => 'Moisture thresholds',
                'course' => 'Aflatoxin Control in Maize Bran Storage',
                'course_slug' => 'aflatoxin-control-maize-bran',
                'lesson' => 'Introduction',
                'questions' => 4, 'pass' => 80, 'attempts' => 2, 'avg' => 82,
                'items' => [
                    ['prompt' => 'Maize bran is stacked…', 'options' => ['Against the wall to save space', 'On pallets, off the floor', 'In a pit like silage', 'In the sun to dry'], 'correct' => 1],
                ],
            ],
        ];
    }
}
