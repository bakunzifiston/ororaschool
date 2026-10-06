<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\LearningResource;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Platform;
use Illuminate\Database\Seeder;

/**
 * Replaces placeholder curriculum with realistic modules, lessons and resources
 * for the four active FarmSchool academies (three published courses each).
 */
class AcademyLearningContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->catalogue() as $courseSlug => $definition) {
            $course = Course::query()->where('slug', $courseSlug)->first();

            if ($course === null) {
                continue;
            }

            $this->seedCourseCurriculum($course, $definition);
        }

        $this->seedOpenAcademyHandouts();
    }

    /**
     * Public /resources only lists academy-attached handouts.
     */
    private function seedOpenAcademyHandouts(): void
    {
        foreach ($this->openAcademyHandouts() as $platformSlug => $handouts) {
            $platform = Platform::query()->where('slug', $platformSlug)->first();

            if ($platform === null) {
                continue;
            }

            $academy = $platform->academies()->orderBy('id')->first();

            if ($academy === null) {
                continue;
            }

            foreach ($handouts as $handout) {
                $this->seedResource(
                    $platform,
                    $handout,
                    'academy',
                    (string) $academy->id,
                    'Academy · '.$academy->name,
                );
            }
        }
    }

    /**
     * @return array<string, list<array{slug: string, title: string, type: string, size: string}>>
     */
    private function openAcademyHandouts(): array
    {
        return [
            'ororafarm' => [
                [
                    'slug' => 'terrace-guide',
                    'title' => 'Terrace numbering guide',
                    'type' => 'guide',
                    'size' => '980 KB',
                ],
                [
                    'slug' => 'ororafarm-season-checklist',
                    'title' => 'Season opening checklist for smallholdings',
                    'type' => 'template',
                    'size' => '140 KB',
                ],
                [
                    'slug' => 'ororafarm-plot-book-handout',
                    'title' => 'Plot book starter handout',
                    'type' => 'manual',
                    'size' => '1.4 MB',
                ],
            ],
            'gemura' => [
                [
                    'slug' => 'colostrum-note',
                    'title' => 'Colostrum timing note',
                    'type' => 'document',
                    'size' => '220 KB',
                ],
                [
                    'slug' => 'gemura-milking-hygiene-poster',
                    'title' => 'Kraal milking hygiene poster',
                    'type' => 'infographic',
                    'size' => '520 KB',
                ],
                [
                    'slug' => 'gemura-collection-centre-brief',
                    'title' => 'Collection centre intake brief',
                    'type' => 'guide',
                    'size' => '860 KB',
                ],
            ],
            'buchapro' => [
                [
                    'slug' => 'traceback-guide',
                    'title' => 'Foot-and-mouth traceback guide',
                    'type' => 'guide',
                    'size' => '1.6 MB',
                ],
                [
                    'slug' => 'buchapro-tagging-handout',
                    'title' => 'Ear-tagging field handout',
                    'type' => 'manual',
                    'size' => '1.1 MB',
                ],
                [
                    'slug' => 'buchapro-movement-reminder',
                    'title' => 'Movement permit reminder card',
                    'type' => 'document',
                    'size' => '180 KB',
                ],
            ],
            'feedgrid' => [
                [
                    'slug' => 'feedgrid-storage-handout',
                    'title' => 'Feed store moisture handout',
                    'type' => 'guide',
                    'size' => '940 KB',
                ],
                [
                    'slug' => 'feedgrid-mixing-poster',
                    'title' => 'On-farm mixing steps poster',
                    'type' => 'infographic',
                    'size' => '610 KB',
                ],
                [
                    'slug' => 'feedgrid-ingredient-card',
                    'title' => 'Local ingredient reference card',
                    'type' => 'template',
                    'size' => '160 KB',
                ],
            ],
        ];
    }

    /**
     * @param  array{modules: list<array<string, mixed>>, resources?: list<array<string, mixed>>}  $definition
     */
    private function seedCourseCurriculum(Course $course, array $definition): void
    {
        $moduleSlugs = [];
        $lessonCount = 0;
        $totalDuration = 0;

        foreach ($definition['modules'] as $moduleIndex => $moduleRow) {
            $module = Module::query()->updateOrCreate(
                [
                    'course_id' => $course->id,
                    'slug' => $moduleRow['slug'],
                ],
                [
                    'title' => $moduleRow['title'],
                    'sort_order' => $moduleIndex + 1,
                ],
            );

            $moduleSlugs[] = $module->slug;
            $lessonSlugs = [];

            foreach ($moduleRow['lessons'] as $lessonIndex => $lessonRow) {
                Lesson::query()->updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'slug' => $lessonRow['slug'],
                    ],
                    [
                        'module_id' => $module->id,
                        'title' => $lessonRow['title'],
                        'type' => $lessonRow['type'],
                        'duration' => $lessonRow['duration'],
                        'body' => $lessonRow['body'],
                        'is_preview' => (bool) ($lessonRow['is_preview'] ?? false),
                        'quiz_slug' => $lessonRow['quiz_slug'] ?? null,
                        'sort_order' => $lessonIndex + 1,
                    ],
                );

                $lessonSlugs[] = $lessonRow['slug'];
                $lessonCount++;
                $totalDuration += (int) $lessonRow['duration'];
            }

            $course->curriculumLessons()
                ->where('module_id', $module->id)
                ->whereNotIn('slug', $lessonSlugs)
                ->get()
                ->each->delete();

            foreach ($moduleRow['resources'] ?? [] as $resourceRow) {
                $this->seedResource($course->platform, $resourceRow, 'module', (string) $module->id, 'Module · '.$module->title);
            }
        }

        $course->curriculumModules()
            ->whereNotIn('slug', $moduleSlugs)
            ->get()
            ->each->delete();

        foreach ($definition['resources'] ?? [] as $resourceRow) {
            if (($resourceRow['attach'] ?? 'course') === 'academy') {
                $academy = $course->academy;

                if ($academy === null) {
                    continue;
                }

                $this->seedResource(
                    $course->platform,
                    $resourceRow,
                    'academy',
                    (string) $academy->id,
                    'Academy · '.$academy->name,
                );

                continue;
            }

            $this->seedResource($course->platform, $resourceRow, 'course', (string) $course->id, 'Course · '.$course->title);
        }

        foreach ($definition['lesson_resources'] ?? [] as $resourceRow) {
            $lesson = Lesson::query()
                ->where('course_id', $course->id)
                ->where('slug', $resourceRow['lesson_slug'])
                ->first();

            if ($lesson === null) {
                continue;
            }

            $this->seedResource(
                $course->platform,
                $resourceRow,
                'lesson',
                (string) $lesson->id,
                'Lesson · '.$lesson->title,
            );
        }

        $course->forceFill([
            'modules' => count($moduleSlugs),
            'lessons' => $lessonCount,
            'duration' => $totalDuration,
            'status' => 'published',
            'description' => $definition['description'] ?? $course->description,
        ])->save();
    }

    /**
     * @param  array{slug: string, title: string, type: string, size?: string, source_url?: string|null}  $resourceRow
     */
    private function seedResource(
        Platform $platform,
        array $resourceRow,
        string $kind,
        string $key,
        string $attachedTo,
    ): void {
        LearningResource::query()->updateOrCreate(
            [
                'platform_id' => $platform->id,
                'slug' => $resourceRow['slug'],
            ],
            [
                'title' => $resourceRow['title'],
                'type' => $resourceRow['type'],
                'attached_kind' => $kind,
                'attached_to' => $attachedTo,
                'attached_key' => $key,
                'size' => $resourceRow['size'] ?? '—',
                'path' => null,
                'source_url' => $resourceRow['source_url'] ?? null,
            ],
        );
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function catalogue(): array
    {
        return [
            'farm-record-keeping' => $this->farmRecordKeeping(),
            'season-planning-terraces' => $this->seasonPlanningTerraces(),
            'soil-fertility-compost-pits' => $this->soilFertilityCompost(),
            'mastitis-milk-hygiene' => $this->mastitisMilkHygiene(),
            'cold-chain-collection-centres' => $this->coldChainCentres(),
            'evening-intake-lactometer' => $this->eveningIntakeLactometer(),
            'animal-identification-eartags' => $this->animalIdentification(),
            'movement-permits-transport' => $this->movementPermits(),
            'kraal-register-reconciliation' => $this->kraalRegister(),
            'least-cost-ration-formulation' => $this->leastCostRation(),
            'aflatoxin-control-maize-bran' => $this->aflatoxinControl(),
            'on-farm-feed-mixing-batches' => $this->onFarmFeedMixing(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function farmRecordKeeping(): array
    {
        return [
            'description' => 'Keep a plot book that survives an audit: planting dates, input receipts, labour days and harvest weights recorded so a season can actually be costed.',
            'modules' => [
                [
                    'slug' => 'plot-book-setup',
                    'title' => 'Setting up the plot book',
                    'lessons' => [
                        [
                            'slug' => 'why-plot-books-fail',
                            'title' => 'Why most plot books fail an audit',
                            'type' => 'video',
                            'duration' => 11,
                            'is_preview' => true,
                            'body' => "A plot book that only lists crop names will not help you cost a season. Auditors and cooperative buyers look for planting dates, seed lots, input receipts and harvest weights that reconcile.\n\nIn this lesson you will see three common failures from Nyagatare cooperatives and how a simple column layout prevents each one.",
                        ],
                        [
                            'slug' => 'columns-that-survive-audit',
                            'title' => 'Columns that survive a season audit',
                            'type' => 'text',
                            'duration' => 9,
                            'body' => "Use one double-page spread per terrace or plot. Left page: planting date, variety, seed source and quantity. Right page: fertiliser and spray dates with receipt numbers, labour days by task, and harvest weight by bag.\n\nLeave a margin for corrections. Never erase a wrong entry—strike through and initial it so the book remains credible.",
                        ],
                        [
                            'slug' => 'receipt-and-labour-log',
                            'title' => 'Logging receipts and labour days',
                            'type' => 'text',
                            'duration' => 8,
                            'body' => "Tape or staple shop receipts to the matching plot page the same day you buy. For family labour, record half-days honestly; unpaid labour still has a cost when you compare enterprises.\n\nAt month end, total labour days and inputs so the season summary does not wait until harvest.",
                        ],
                    ],
                    'resources' => [
                        [
                            'slug' => 'plot-book-column-guide',
                            'title' => 'Plot book column guide',
                            'type' => 'guide',
                            'size' => '420 KB',
                        ],
                    ],
                ],
                [
                    'slug' => 'season-summary',
                    'title' => 'Closing a season on paper',
                    'lessons' => [
                        [
                            'slug' => 'harvest-weights-and-rejects',
                            'title' => 'Harvest weights and reject notes',
                            'type' => 'video',
                            'duration' => 10,
                            'body' => 'Weigh bags at the store gate, not in the field guess. Record rejects separately so margin calculations stay honest when the buyer docks moisture or quality.',
                        ],
                        [
                            'slug' => 'one-page-season-cost',
                            'title' => 'One-page season cost summary',
                            'type' => 'text',
                            'duration' => 12,
                            'body' => "Transfer plot totals onto a single season sheet: seed, fertiliser, spray, hired labour, family labour valued at local day rates, transport and packing.\n\nSubtract that from sales. The number tells you whether to expand the enterprise or cut it next year.",
                        ],
                    ],
                ],
            ],
            'resources' => [
                [
                    'slug' => 'plot-book-template-seed',
                    'title' => 'Blank plot book template',
                    'type' => 'template',
                    'size' => '150 KB',
                ],
            ],
            'lesson_resources' => [
                [
                    'slug' => 'sample-filled-plot-page',
                    'title' => 'Sample filled plot page (Nyagatare)',
                    'type' => 'pdf',
                    'size' => '280 KB',
                    'lesson_slug' => 'columns-that-survive-audit',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function seasonPlanningTerraces(): array
    {
        return [
            'description' => 'Sketch and number terraced plots, rotate crops across them, and plan a season against the two rainfall windows.',
            'modules' => [
                [
                    'slug' => 'mapping-terraces',
                    'title' => 'Mapping and numbering terraces',
                    'lessons' => [
                        [
                            'slug' => 'sketch-the-slope',
                            'title' => 'Sketching the slope before you plant',
                            'type' => 'video',
                            'duration' => 13,
                            'is_preview' => true,
                            'body' => 'Walk the contour with a notebook and mark each terrace from the top of the slope downward. Number them in the same order every season so crop rotation history stays attached to the land, not to memory.',
                        ],
                        [
                            'slug' => 'plot-codes-that-stick',
                            'title' => 'Plot codes that stick across seasons',
                            'type' => 'text',
                            'duration' => 7,
                            'body' => 'Use a fixed code such as T1–T12 for terraces and keep a legend on the inside cover of the plot book. Changing codes mid-year is how rotation plans fall apart.',
                        ],
                    ],
                ],
                [
                    'slug' => 'rainfall-windows',
                    'title' => 'Planning against rainfall windows',
                    'lessons' => [
                        [
                            'slug' => 'two-seasons-eastern-province',
                            'title' => 'The two seasons in the Eastern Province',
                            'type' => 'text',
                            'duration' => 9,
                            'body' => 'Season A (Sept–Jan) and Season B (Feb–Jun) set your planting deadlines. Back-plan from expected harvest so seed and labour arrive two weeks before the first workable rains.',
                        ],
                        [
                            'slug' => 'rotation-on-terraces',
                            'title' => 'Rotating legumes and cereals on terraces',
                            'type' => 'video',
                            'duration' => 11,
                            'body' => 'Alternate a cereal terrace with a legume terrace each season. Leave a written note of what preceded the current crop so fertiliser rates stay appropriate.',
                        ],
                        [
                            'slug' => 'labour-calendar',
                            'title' => 'Building a labour calendar',
                            'type' => 'text',
                            'duration' => 8,
                            'body' => 'List peak labour weeks for land prep, planting, weeding and harvest. If two terraces need weeding in the same week, stagger planting dates or hire in advance.',
                        ],
                    ],
                    'resources' => [
                        [
                            'slug' => 'terrace-numbering-sheet',
                            'title' => 'Terrace numbering field sheet',
                            'type' => 'template',
                            'size' => '95 KB',
                        ],
                    ],
                ],
            ],
            'resources' => [
                [
                    'slug' => 'season-a-b-planner',
                    'title' => 'Season A / B planner poster',
                    'type' => 'infographic',
                    'size' => '640 KB',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function soilFertilityCompost(): array
    {
        return [
            'description' => 'Build and manage compost pits that restore terrace soils: carbon–nitrogen balance, turning schedules and when to spread before the rains.',
            'modules' => [
                [
                    'slug' => 'building-the-pit',
                    'title' => 'Building the compost pit',
                    'lessons' => [
                        [
                            'slug' => 'site-and-dimensions',
                            'title' => 'Choosing the site and pit size',
                            'type' => 'video',
                            'duration' => 10,
                            'is_preview' => true,
                            'body' => 'Place the pit on well-drained ground near the kraal but above flood lines. A pit of about 2 m × 1.5 m × 1 m serves a smallholding without becoming unmanageable to turn.',
                        ],
                        [
                            'slug' => 'layering-green-and-brown',
                            'title' => 'Layering green and brown materials',
                            'type' => 'text',
                            'duration' => 8,
                            'body' => 'Alternate moist green matter (weeds, manure) with dry brown matter (maize stover, dry grass). Aim for roughly three parts brown to one part green by volume so the pile does not go anaerobic and foul.',
                        ],
                    ],
                ],
                [
                    'slug' => 'managing-and-spreading',
                    'title' => 'Managing and spreading compost',
                    'lessons' => [
                        [
                            'slug' => 'turning-schedule',
                            'title' => 'Turning schedule and moisture checks',
                            'type' => 'video',
                            'duration' => 9,
                            'body' => 'Turn every two to three weeks. Squeeze a handful: it should feel like a wrung sponge. Add water if dusty; add dry stover if it drips.',
                        ],
                        [
                            'slug' => 'when-compost-is-ready',
                            'title' => 'Knowing when compost is ready',
                            'type' => 'text',
                            'duration' => 7,
                            'body' => 'Finished compost is dark, crumbly and earthy-smelling. Coarse stalks should break easily. If you still see fresh green, give it another turn and two more weeks.',
                        ],
                        [
                            'slug' => 'spreading-before-rains',
                            'title' => 'Spreading before the rains',
                            'type' => 'text',
                            'duration' => 8,
                            'body' => 'Broadcast and lightly incorporate two weeks before planting rains so nutrients settle into the root zone. Avoid leaving heaps on the surface through heavy storms.',
                        ],
                    ],
                    'resources' => [
                        [
                            'slug' => 'compost-turning-log',
                            'title' => 'Compost turning log sheet',
                            'type' => 'template',
                            'size' => '88 KB',
                        ],
                    ],
                ],
            ],
            'resources' => [
                [
                    'slug' => 'compost-pit-field-guide',
                    'title' => 'Compost pit field guide',
                    'type' => 'guide',
                    'size' => '1.1 MB',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mastitisMilkHygiene(): array
    {
        return [
            'description' => 'Catch subclinical mastitis with the California Mastitis Test and build a milking routine that keeps somatic cell counts inside collection-centre limits.',
            'modules' => [
                [
                    'slug' => 'm-hygiene-1',
                    'title' => 'Why somatic cell counts move',
                    'lessons' => [
                        [
                            'slug' => 'l-cmt-1',
                            'title' => 'Reading a CMT paddle',
                            'type' => 'video',
                            'duration' => 12,
                            'is_preview' => true,
                            'quiz_slug' => 'cmt-paddle-check',
                            'body' => 'Hold the paddle level, strip an equal volume from each quarter, add reagent, and swirl. Gel strength tells you which quarters need attention before the tanker arrives.',
                        ],
                        [
                            'slug' => 'l-cmt-2',
                            'title' => 'Scoring trace, weak positive and strong positive',
                            'type' => 'text',
                            'duration' => 8,
                            'body' => 'Trace is a slight slime; weak positive forms a distinct gel; strong positive forms a gelatinous mass. Record the score per quarter so treatment and strip-milk decisions stay consistent across milkers.',
                        ],
                        [
                            'slug' => 'l-cmt-3',
                            'title' => 'CMT field sheet',
                            'type' => 'pdf',
                            'duration' => 4,
                            'body' => 'Use the attached field sheet to log cow identity, quarter scores and the action taken. Bring completed sheets to the collection centre when somatic cell counts spike.',
                        ],
                    ],
                ],
                [
                    'slug' => 'm-hygiene-2',
                    'title' => 'The milking routine',
                    'lessons' => [
                        [
                            'slug' => 'l-milk-1',
                            'title' => 'Fore-stripping at the kraal',
                            'type' => 'video',
                            'duration' => 9,
                            'body' => 'Fore-strip onto a dark surface and check for clots before the full milking. Never strip into the milking vessel.',
                        ],
                        [
                            'slug' => 'l-milk-2',
                            'title' => 'Audio: a clean milking sequence',
                            'type' => 'audio',
                            'duration' => 6,
                            'body' => 'Listen through a clean sequence: wash hands, clean teats, fore-strip, dry, milk, post-dip. Pause the audio and practise each step at the kraal.',
                        ],
                        [
                            'slug' => 'l-milk-3',
                            'title' => 'MINAGRI milk hygiene note',
                            'type' => 'external',
                            'duration' => 5,
                            'body' => 'Review the national milk hygiene expectations for smallholder herds delivering to collection centres, then compare them with your kraal routine.',
                        ],
                    ],
                    'resources' => [
                        [
                            'slug' => 'rejection-log',
                            'title' => 'Collection-centre rejection log',
                            'type' => 'template',
                            'size' => '94 KB',
                        ],
                    ],
                ],
                [
                    'slug' => 'm-hygiene-3',
                    'title' => 'Clinic: paddles together',
                    'lessons' => [
                        [
                            'slug' => 'l-milk-4',
                            'title' => 'Reading CMT paddles together',
                            'type' => 'live_session',
                            'duration' => 45,
                            'body' => 'Join the live clinic to score paddles side by side with an instructor. Bring photographs of your last CMT round if you have them.',
                        ],
                    ],
                ],
            ],
            'lesson_resources' => [
                [
                    'slug' => 'cmt-field-sheet',
                    'title' => 'CMT field sheet',
                    'type' => 'template',
                    'size' => '180 KB',
                    'lesson_slug' => 'l-cmt-1',
                ],
                [
                    'slug' => 'cmt-demo-clip',
                    'title' => 'Paddle scoring demonstration',
                    'type' => 'video',
                    'size' => '48 MB',
                    'lesson_slug' => 'l-cmt-2',
                ],
            ],
            'resources' => [
                [
                    'slug' => 'milk-hygiene-manual',
                    'title' => 'Smallholder milk hygiene manual',
                    'type' => 'manual',
                    'size' => '2.4 MB',
                    'attach' => 'academy',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function coldChainCentres(): array
    {
        return [
            'description' => 'Temperature logging, lactometer checks and rejection protocols for centre operators handling evening and morning intake.',
            'modules' => [
                [
                    'slug' => 'temperature-discipline',
                    'title' => 'Temperature discipline at the centre',
                    'lessons' => [
                        [
                            'slug' => 'logging-intake-temperatures',
                            'title' => 'Logging intake temperatures',
                            'type' => 'video',
                            'duration' => 10,
                            'is_preview' => true,
                            'body' => 'Record can temperature at arrival and after blending. A break in the cold chain of even one evening can spoil a morning tanker load.',
                        ],
                        [
                            'slug' => 'cooler-and-agitator-checks',
                            'title' => 'Cooler and agitator checks',
                            'type' => 'text',
                            'duration' => 8,
                            'body' => 'Confirm the cooler reaches target before intake opens. Listen for agitator faults; stagnant milk warms in pockets and fails the next lactometer round.',
                        ],
                    ],
                ],
                [
                    'slug' => 'rejection-protocols',
                    'title' => 'Rejection protocols that stick',
                    'lessons' => [
                        [
                            'slug' => 'when-to-reject-a-can',
                            'title' => 'When to reject a can',
                            'type' => 'text',
                            'duration' => 9,
                            'body' => 'Reject for smell, clots, abnormal colour or lactometer readings outside the centre band. Write the reason in the rejection log before the supplier leaves the yard.',
                        ],
                        [
                            'slug' => 'handover-to-the-tanker',
                            'title' => 'Hand-over to the tanker',
                            'type' => 'video',
                            'duration' => 11,
                            'body' => 'Seal the consignment sheet, confirm temperature and volume, and keep a carbon copy for the centre file. Never release milk that lacks a complete hand-over line.',
                        ],
                    ],
                ],
            ],
            'resources' => [
                [
                    'slug' => 'cold-chain-guide',
                    'title' => 'Evening cold-chain guide',
                    'type' => 'guide',
                    'size' => '1.1 MB',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function eveningIntakeLactometer(): array
    {
        return [
            'description' => 'Run the evening collection without losing the cold chain: lactometer reading, rejection log and the hand-over to the tanker.',
            'modules' => [
                [
                    'slug' => 'evening-setup',
                    'title' => 'Opening the evening intake',
                    'lessons' => [
                        [
                            'slug' => 'bench-ready-before-dusk',
                            'title' => 'Getting the bench ready before dusk',
                            'type' => 'video',
                            'duration' => 8,
                            'is_preview' => true,
                            'body' => 'Clean the reception bench, calibrate the lactometer jar and stage rejection tags before the first bicycle arrives. Evening queues punish slow setup.',
                        ],
                        [
                            'slug' => 'queue-and-can-order',
                            'title' => 'Queue discipline and can order',
                            'type' => 'text',
                            'duration' => 6,
                            'body' => 'Process in arrival order unless a can is obviously spoiled. Keep filled and empty cans on separate sides of the bench to avoid mix-ups.',
                        ],
                    ],
                ],
                [
                    'slug' => 'lactometer-practice',
                    'title' => 'Lactometer practice',
                    'lessons' => [
                        [
                            'slug' => 'reading-the-lactometer',
                            'title' => 'Reading the lactometer correctly',
                            'type' => 'video',
                            'duration' => 9,
                            'body' => 'Fill to the mark, let the lactometer settle, and read at eye level. Temperature correction matters on hot evenings—use the centre chart, not memory.',
                        ],
                        [
                            'slug' => 'logging-rejects-same-night',
                            'title' => 'Logging rejects the same night',
                            'type' => 'text',
                            'duration' => 7,
                            'body' => 'Enter supplier name, can ID, reading and reason before closing. Morning staff cannot reconstruct what you rejected after dark.',
                        ],
                        [
                            'slug' => 'tanker-handover-evening',
                            'title' => 'Evening hand-over checklist',
                            'type' => 'text',
                            'duration' => 8,
                            'body' => 'Confirm volume, temperature, reject count and seal numbers. Sign with the driver and keep your copy in the locked intake folder.',
                        ],
                    ],
                ],
            ],
            'resources' => [
                [
                    'slug' => 'lactometer-poster',
                    'title' => 'Lactometer reading poster',
                    'type' => 'infographic',
                    'size' => '640 KB',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function animalIdentification(): array
    {
        return [
            'description' => 'Apply and register ear tags correctly, handle replacements for lost tags, and keep the herd register matching what is standing in the kraal.',
            'modules' => [
                [
                    'slug' => 'm-tag-1',
                    'title' => 'The tag and the pliers',
                    'lessons' => [
                        [
                            'slug' => 'l-tag-1',
                            'title' => 'Placing an ear tag without tearing',
                            'type' => 'video',
                            'duration' => 11,
                            'is_preview' => true,
                            'quiz_slug' => 'ear-tag-placement',
                            'body' => 'Load the pliers correctly, choose the middle third of the ear, and avoid cartilage ridges. A torn ear costs you a replacement tag and a register correction.',
                        ],
                        [
                            'slug' => 'l-tag-2',
                            'title' => 'The herd register columns',
                            'type' => 'text',
                            'duration' => 7,
                            'body' => 'Every tag number needs an animal description, sex, colour marks, owner and date of tagging. Empty columns are how animals vanish from the district list.',
                        ],
                        [
                            'slug' => 'l-tag-3',
                            'title' => 'Ear-tag application checklist',
                            'type' => 'pdf',
                            'duration' => 3,
                            'body' => 'Print the checklist and tick each step as you tag a batch. Incomplete rows are the first thing a district officer challenges.',
                        ],
                    ],
                ],
                [
                    'slug' => 'm-tag-2',
                    'title' => 'When a tag is lost',
                    'lessons' => [
                        [
                            'slug' => 'l-tag-4',
                            'title' => 'Replacement protocol at the kraal',
                            'type' => 'video',
                            'duration' => 8,
                            'body' => 'Retag only after confirming the animal against the register description. Record the old number, new number and reason on the same day.',
                        ],
                        [
                            'slug' => 'l-tag-5',
                            'title' => 'District officer call-in (audio)',
                            'type' => 'audio',
                            'duration' => 5,
                            'body' => 'Listen to a model call reporting a lost tag batch. Note the fields the officer asks for so your next call is complete the first time.',
                        ],
                        [
                            'slug' => 'l-tag-6',
                            'title' => 'RAB identification circular',
                            'type' => 'external',
                            'duration' => 6,
                            'body' => 'Read the current identification circular and mark any changes to replacement rules that affect your kraal.',
                        ],
                    ],
                ],
                [
                    'slug' => 'm-tag-3',
                    'title' => 'Clinic: tagging a batch',
                    'lessons' => [
                        [
                            'slug' => 'l-tag-7',
                            'title' => 'Tagging a batch at Rubengera',
                            'type' => 'live_session',
                            'duration' => 50,
                            'body' => 'Live clinic on batch tagging order, restraint and register entry under time pressure.',
                        ],
                    ],
                ],
            ],
            'lesson_resources' => [
                [
                    'slug' => 'eartag-checklist',
                    'title' => 'Ear-tag application checklist',
                    'type' => 'template',
                    'size' => '120 KB',
                    'lesson_slug' => 'l-tag-1',
                ],
                [
                    'slug' => 'register-note',
                    'title' => 'Herd register columns note',
                    'type' => 'document',
                    'size' => '190 KB',
                    'lesson_slug' => 'l-tag-2',
                ],
                [
                    'slug' => 'tagging-clip',
                    'title' => 'Tagging a batch at Rubengera',
                    'type' => 'video',
                    'size' => '62 MB',
                    'lesson_slug' => 'l-tag-7',
                ],
            ],
            'resources' => [
                [
                    'slug' => 'identification-manual',
                    'title' => 'National animal identification manual',
                    'type' => 'manual',
                    'size' => '3.8 MB',
                    'attach' => 'academy',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function movementPermits(): array
    {
        return [
            'description' => 'Complete a movement permit that will pass a roadblock check, and log arrivals and departures against the district register.',
            'modules' => [
                [
                    'slug' => 'permit-fields',
                    'title' => 'Permit fields that pass a roadblock',
                    'lessons' => [
                        [
                            'slug' => 'required-permit-lines',
                            'title' => 'Required lines on a movement permit',
                            'type' => 'video',
                            'duration' => 12,
                            'is_preview' => true,
                            'body' => 'Origin, destination, animal IDs, transporter and validity window must all be complete. A missing ear-tag list is the fastest reason for a roadside refusal.',
                        ],
                        [
                            'slug' => 'matching-tags-to-permit',
                            'title' => 'Matching tags to the permit list',
                            'type' => 'text',
                            'duration' => 8,
                            'body' => 'Read every tag against the paper list before the truck leaves. Animals on the truck but not on the permit will be turned back with the whole load at risk.',
                        ],
                    ],
                    'resources' => [
                        [
                            'slug' => 'permit-blank',
                            'title' => 'Blank movement permit',
                            'type' => 'pdf',
                            'size' => '240 KB',
                        ],
                    ],
                ],
                [
                    'slug' => 'logging-movements',
                    'title' => 'Logging arrivals and departures',
                    'lessons' => [
                        [
                            'slug' => 'departure-log-entry',
                            'title' => 'Writing the departure log',
                            'type' => 'text',
                            'duration' => 7,
                            'body' => 'Enter date, time, permit number, destination and escort name. Keep the log in the office, not in the driver’s cab.',
                        ],
                        [
                            'slug' => 'arrival-reconciliation',
                            'title' => 'Arrival reconciliation at destination',
                            'type' => 'video',
                            'duration' => 10,
                            'body' => 'Count animals off the truck against the permit before unloading into the kraal. Note deaths or shortages immediately.',
                        ],
                        [
                            'slug' => 'roadblock-response',
                            'title' => 'Responding at a roadblock',
                            'type' => 'text',
                            'duration' => 6,
                            'body' => 'Present the permit first, then open the truck for tag checks. Argue after compliance, never before documents are shown.',
                        ],
                    ],
                ],
            ],
            'resources' => [
                [
                    'slug' => 'permit-fields-poster',
                    'title' => 'Movement permit fields poster',
                    'type' => 'infographic',
                    'size' => '510 KB',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function kraalRegister(): array
    {
        return [
            'description' => 'Walk the kraal against the paper register, mark missing tags and write the discrepancy report the district officer will accept.',
            'modules' => [
                [
                    'slug' => 'walking-the-kraal',
                    'title' => 'Walking the kraal against the book',
                    'lessons' => [
                        [
                            'slug' => 'headcount-method',
                            'title' => 'A headcount method that finishes',
                            'type' => 'video',
                            'duration' => 9,
                            'is_preview' => true,
                            'body' => 'Work clockwise from the gate with one reader and one scribe. Never count from memory after the walk.',
                        ],
                        [
                            'slug' => 'marking-missing-tags',
                            'title' => 'Marking missing and extra tags',
                            'type' => 'text',
                            'duration' => 7,
                            'body' => 'Tick animals present, circle missing numbers, and list any untagged animals separately. Those three lists become the discrepancy report.',
                        ],
                    ],
                ],
                [
                    'slug' => 'discrepancy-report',
                    'title' => 'Writing the discrepancy report',
                    'lessons' => [
                        [
                            'slug' => 'report-the-officer-accepts',
                            'title' => 'The report a district officer accepts',
                            'type' => 'text',
                            'duration' => 10,
                            'body' => 'State date, kraal, enumerator names, missing tags, extras and proposed actions. Attach the marked register page copy.',
                        ],
                        [
                            'slug' => 'closing-out-discrepancies',
                            'title' => 'Closing out discrepancies',
                            'type' => 'video',
                            'duration' => 8,
                            'body' => 'Retag, transfer or write-off only with an approved action line. Unclosed discrepancies reappear at the next inspection.',
                        ],
                    ],
                    'resources' => [
                        [
                            'slug' => 'kraal-discrepancy-log',
                            'title' => 'Kraal discrepancy log',
                            'type' => 'template',
                            'size' => '88 KB',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function leastCostRation(): array
    {
        return [
            'description' => 'Balance maize bran, cotton seed cake, brewers grain and mineral premix against a target crude protein without overspending.',
            'modules' => [
                [
                    'slug' => 'ingredients-and-targets',
                    'title' => 'Ingredients and protein targets',
                    'lessons' => [
                        [
                            'slug' => 'local-ingredient-profiles',
                            'title' => 'Local ingredient protein profiles',
                            'type' => 'video',
                            'duration' => 14,
                            'is_preview' => true,
                            'body' => 'Compare typical crude protein ranges for maize bran, cotton seed cake and brewers grain as bought in Rwamagana markets. Always verify with a recent lab slip when the mill changes supplier.',
                        ],
                        [
                            'slug' => 'setting-the-cp-target',
                            'title' => 'Setting the crude protein target',
                            'type' => 'text',
                            'duration' => 9,
                            'body' => 'Lactating dairy cows on local forages often need a concentrate around 16–18% CP. Write the target before you open the price sheet so cost does not drive the formula alone.',
                        ],
                    ],
                ],
                [
                    'slug' => 'balancing-the-mix',
                    'title' => 'Balancing the mix on paper',
                    'lessons' => [
                        [
                            'slug' => 'worksheet-pass',
                            'title' => 'First pass on the ration worksheet',
                            'type' => 'text',
                            'duration' => 12,
                            'body' => 'Fix the premix inclusion, then adjust bran and cake until protein and cost meet the band. Recalculate when any ingredient price moves more than 10%.',
                        ],
                        [
                            'slug' => 'checking-palatability',
                            'title' => 'Checking palatability and dust',
                            'type' => 'video',
                            'duration' => 8,
                            'body' => 'A least-cost mix that animals refuse is not least-cost. Watch intake for three days and note refusals before locking the formula.',
                        ],
                        [
                            'slug' => 'documenting-the-formula',
                            'title' => 'Documenting the approved formula',
                            'type' => 'text',
                            'duration' => 7,
                            'body' => 'File ingredient percentages, target CP, cost per kilo and approval date. Version the sheet whenever you change a supplier.',
                        ],
                    ],
                ],
            ],
            'resources' => [
                [
                    'slug' => 'price-sheet',
                    'title' => 'Feed ingredient price sheet',
                    'type' => 'template',
                    'size' => '130 KB',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function aflatoxinControl(): array
    {
        return [
            'description' => 'Moisture thresholds, pallet stacking and visual screening to keep Aspergillus out of stored feed ingredients.',
            'modules' => [
                [
                    'slug' => 'moisture-is-the-story',
                    'title' => 'Why moisture is the whole story',
                    'lessons' => [
                        [
                            'slug' => 'why-moisture-matters',
                            'title' => 'Why moisture is the whole story',
                            'type' => 'video',
                            'duration' => 11,
                            'is_preview' => true,
                            'body' => 'Aspergillus thrives when maize bran sits warm and damp. Know the moisture threshold for safe storage and refuse loads that arrive above it.',
                        ],
                        [
                            'slug' => 'sampling-incoming-bran',
                            'title' => 'Sampling incoming maize bran',
                            'type' => 'text',
                            'duration' => 8,
                            'body' => 'Probe multiple bags, not only the top layer. Record supplier, date and meter reading before the load enters the store.',
                        ],
                    ],
                    'resources' => [
                        [
                            'slug' => 'moisture-poster',
                            'title' => 'Moisture threshold poster',
                            'type' => 'infographic',
                            'size' => '470 KB',
                        ],
                    ],
                ],
                [
                    'slug' => 'store-discipline',
                    'title' => 'Store stacking and screening',
                    'lessons' => [
                        [
                            'slug' => 'pallet-stacking-rules',
                            'title' => 'Pallet stacking rules that air the bags',
                            'type' => 'text',
                            'duration' => 7,
                            'body' => 'Keep bags off the floor, leave aisles for airflow, and rotate oldest stock first. Solid floor stacks are where mould starts.',
                        ],
                        [
                            'slug' => 'visual-screening',
                            'title' => 'Visual screening for mould',
                            'type' => 'video',
                            'duration' => 9,
                            'body' => 'Look for clumping, discoloration and musty smell. Quarantine suspect bags and call for a lab test before they enter a ration.',
                        ],
                        [
                            'slug' => 'incident-log',
                            'title' => 'Keeping an aflatoxin incident log',
                            'type' => 'text',
                            'duration' => 6,
                            'body' => 'Log every rejected or quarantined lot with photos and meter readings. Patterns in the log show which suppliers need dropping.',
                        ],
                    ],
                ],
            ],
            'resources' => [
                [
                    'slug' => 'aflatoxin-manual',
                    'title' => 'Aflatoxin control manual',
                    'type' => 'manual',
                    'size' => '2.1 MB',
                    'attach' => 'academy',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function onFarmFeedMixing(): array
    {
        return [
            'description' => 'Weigh, mix and label dairy and goat rations on the farm: batch sheets, premix handling and how to avoid carry-over between species.',
            'modules' => [
                [
                    'slug' => 'weighing-and-batch-sheets',
                    'title' => 'Weighing and batch sheets',
                    'lessons' => [
                        [
                            'slug' => 'zero-the-scale',
                            'title' => 'Zero the scale before every batch',
                            'type' => 'video',
                            'duration' => 8,
                            'is_preview' => true,
                            'body' => 'Tare the container, then weigh each ingredient to the formula sheet. Guessing by basin volume is how protein targets drift week to week.',
                        ],
                        [
                            'slug' => 'filling-the-batch-sheet',
                            'title' => 'Filling the batch sheet completely',
                            'type' => 'text',
                            'duration' => 7,
                            'body' => 'Record date, formula version, weights, mixer name and destination herd. Incomplete sheets make recall impossible if animals refuse a batch.',
                        ],
                    ],
                    'resources' => [
                        [
                            'slug' => 'feed-batch-sheet',
                            'title' => 'On-farm feed batch sheet',
                            'type' => 'template',
                            'size' => '110 KB',
                        ],
                    ],
                ],
                [
                    'slug' => 'mixing-and-carryover',
                    'title' => 'Mixing without carry-over',
                    'lessons' => [
                        [
                            'slug' => 'premix-handling',
                            'title' => 'Handling mineral premix safely',
                            'type' => 'text',
                            'duration' => 8,
                            'body' => 'Premix is concentrated. Weigh it carefully, disperse it through a small bran share first, then fold into the main mix so hot spots do not burn mouths.',
                        ],
                        [
                            'slug' => 'cleaning-between-species',
                            'title' => 'Cleaning between dairy and goat batches',
                            'type' => 'video',
                            'duration' => 9,
                            'body' => 'Sweep and flush the mixer between species formulas. Carry-over of medicated or high-copper mixes can harm the next herd.',
                        ],
                        [
                            'slug' => 'labelling-bags',
                            'title' => 'Labelling bags for the kraal',
                            'type' => 'text',
                            'duration' => 6,
                            'body' => 'Mark formula name, batch date and use-by window on every bag. Unlabelled bags are the first to be fed to the wrong animals.',
                        ],
                    ],
                ],
            ],
            'resources' => [
                [
                    'slug' => 'on-farm-mixing-guide',
                    'title' => 'On-farm mixing quick guide',
                    'type' => 'guide',
                    'size' => '760 KB',
                ],
            ],
        ];
    }
}
