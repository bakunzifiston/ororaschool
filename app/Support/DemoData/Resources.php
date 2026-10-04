<?php

namespace App\Support\DemoData;

use App\Models\LearningResource;
use Illuminate\Support\Facades\Schema;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Types: pdf, manual, guide, template, video, presentation, document, infographic.
 * Attached to course / module / lesson / academy.
 */
class Resources
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function forPlatform(string $platform): array
    {
        return array_values(array_filter(
            self::all(),
            fn (array $resource) => $resource['platform'] === $platform,
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function fixturesForPlatform(string $platform): array
    {
        return array_values(array_filter(
            self::fixtures(),
            fn (array $resource) => $resource['platform'] === $platform,
        ));
    }

    /**
     * Academy handouts are public. Course, module and lesson files wait for enrolment.
     *
     * @param  array<string, mixed>  $resource
     */
    public static function requiresEnrolment(array $resource): bool
    {
        return ($resource['attached_kind'] ?? '') !== 'academy';
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function openOnPlatform(string $slug): array
    {
        return array_values(array_filter(
            self::forPlatform($slug),
            fn (array $resource): bool => ! self::requiresEnrolment($resource),
        ));
    }

    /**
     * Open academy handouts on live academies, ready for the public catalogue.
     *
     * @return list<array<string, mixed>>
     */
    public static function open(): array
    {
        $platforms = [];

        foreach (PublicCatalog::activePlatforms() as $platform) {
            $platforms[$platform['slug']] = $platform;
        }

        return array_values(array_filter(array_map(
            function (array $resource) use ($platforms): ?array {
                $platform = $platforms[$resource['platform']] ?? null;

                if ($platform === null || self::requiresEnrolment($resource)) {
                    return null;
                }

                return array_merge($resource, [
                    'type_label' => self::typeLabel($resource['type']),
                    'platform_name' => $platform['name'],
                    'href' => $platform['href'],
                ]);
            },
            self::all(),
        )));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            ...self::persisted(),
            ...self::fixtures(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function fixtures(): array
    {
        return [
            ['slug' => 'cmt-field-sheet', 'platform' => 'gemura', 'title' => 'CMT field sheet', 'type' => 'template', 'attached_to' => 'Lesson · Reading a CMT paddle', 'attached_kind' => 'lesson', 'size' => '180 KB', 'updated' => '30 Aug 2026'],
            ['slug' => 'milk-hygiene-manual', 'platform' => 'gemura', 'title' => 'Smallholder milk hygiene manual', 'type' => 'manual', 'attached_to' => 'Academy · Milk hygiene', 'attached_kind' => 'academy', 'size' => '2.4 MB', 'updated' => '12 Aug 2026'],
            ['slug' => 'lactometer-poster', 'platform' => 'gemura', 'title' => 'Lactometer reading poster', 'type' => 'infographic', 'attached_to' => 'Course · Evening Intake and Lactometer Checks', 'attached_kind' => 'course', 'size' => '640 KB', 'updated' => '11 Sep 2026'],
            ['slug' => 'rejection-log', 'platform' => 'gemura', 'title' => 'Collection-centre rejection log', 'type' => 'template', 'attached_to' => 'Module · The milking routine', 'attached_kind' => 'module', 'size' => '94 KB', 'updated' => '04 Sep 2026'],
            ['slug' => 'cold-chain-guide', 'platform' => 'gemura', 'title' => 'Evening cold-chain guide', 'type' => 'guide', 'attached_to' => 'Course · Cold Chain Discipline at Milk Collection Centres', 'attached_kind' => 'course', 'size' => '1.1 MB', 'updated' => '04 Sep 2026'],
            ['slug' => 'cmt-demo-clip', 'platform' => 'gemura', 'title' => 'Paddle scoring demonstration', 'type' => 'video', 'attached_to' => 'Lesson · Scoring trace, weak positive and strong positive', 'attached_kind' => 'lesson', 'size' => '48 MB', 'updated' => '30 Aug 2026'],
            ['slug' => 'ai-timing-slides', 'platform' => 'gemura', 'title' => 'Morning–evening rule slides', 'type' => 'presentation', 'attached_to' => 'Course · Heat Detection and AI Timing for Ankole–Friesian Crosses', 'attached_kind' => 'course', 'size' => '3.2 MB', 'updated' => '14 Sep 2026'],
            ['slug' => 'colostrum-note', 'platform' => 'gemura', 'title' => 'Colostrum timing note', 'type' => 'document', 'attached_to' => 'Academy · Milk hygiene', 'attached_kind' => 'academy', 'size' => '220 KB', 'updated' => '18 Sep 2026'],
            ['slug' => 'silage-pdf', 'platform' => 'gemura', 'title' => 'Pit and tube silage sheet', 'type' => 'pdf', 'attached_to' => 'Course · Silage from Maize Stover and Napier Grass', 'attached_kind' => 'course', 'size' => '410 KB', 'updated' => '17 Sep 2026'],

            ['slug' => 'eartag-checklist', 'platform' => 'buchapro', 'title' => 'Ear-tag application checklist', 'type' => 'template', 'attached_to' => 'Lesson · Placing an ear tag without tearing', 'attached_kind' => 'lesson', 'size' => '120 KB', 'updated' => '28 Aug 2026'],
            ['slug' => 'identification-manual', 'platform' => 'buchapro', 'title' => 'National animal identification manual', 'type' => 'manual', 'attached_to' => 'Academy · Animal identification', 'attached_kind' => 'academy', 'size' => '3.8 MB', 'updated' => '02 Aug 2026'],
            ['slug' => 'permit-fields-poster', 'platform' => 'buchapro', 'title' => 'Movement permit fields poster', 'type' => 'infographic', 'attached_to' => 'Course · Movement Permits and Livestock Transport Records', 'attached_kind' => 'course', 'size' => '510 KB', 'updated' => '09 Sep 2026'],
            ['slug' => 'kraal-discrepancy-log', 'platform' => 'buchapro', 'title' => 'Kraal discrepancy log', 'type' => 'template', 'attached_to' => 'Module · When a tag is lost', 'attached_kind' => 'module', 'size' => '88 KB', 'updated' => '05 Sep 2026'],
            ['slug' => 'traceback-guide', 'platform' => 'buchapro', 'title' => 'Foot-and-mouth traceback guide', 'type' => 'guide', 'attached_to' => 'Academy · Outbreak traceback', 'attached_kind' => 'academy', 'size' => '1.6 MB', 'updated' => '16 Sep 2026'],
            ['slug' => 'tagging-clip', 'platform' => 'buchapro', 'title' => 'Tagging a batch at Rubengera', 'type' => 'video', 'attached_to' => 'Lesson · Tagging a batch at Rubengera', 'attached_kind' => 'lesson', 'size' => '62 MB', 'updated' => '28 Aug 2026'],
            ['slug' => 'gate-check-slides', 'platform' => 'buchapro', 'title' => 'Nyabugogo gate-check slides', 'type' => 'presentation', 'attached_to' => 'Course · Livestock Market Gate Checks', 'attached_kind' => 'course', 'size' => '2.7 MB', 'updated' => '12 Sep 2026'],
            ['slug' => 'register-note', 'platform' => 'buchapro', 'title' => 'Herd register columns note', 'type' => 'document', 'attached_to' => 'Lesson · The herd register columns', 'attached_kind' => 'lesson', 'size' => '190 KB', 'updated' => '28 Aug 2026'],
            ['slug' => 'permit-blank', 'platform' => 'buchapro', 'title' => 'Blank movement permit', 'type' => 'pdf', 'attached_to' => 'Course · Movement Permits and Livestock Transport Records', 'attached_kind' => 'course', 'size' => '240 KB', 'updated' => '09 Sep 2026'],

            ['slug' => 'plot-book-template', 'platform' => 'ororafarm', 'title' => 'Plot book template', 'type' => 'template', 'attached_to' => 'Course · Farm Record Keeping for Smallholder Enterprises', 'attached_kind' => 'course', 'size' => '150 KB', 'updated' => '02 Sep 2026'],
            ['slug' => 'terrace-guide', 'platform' => 'ororafarm', 'title' => 'Terrace numbering guide', 'type' => 'guide', 'attached_to' => 'Academy · Season planning', 'attached_kind' => 'academy', 'size' => '980 KB', 'updated' => '21 Aug 2026'],
            ['slug' => 'share-register', 'platform' => 'ororafarm', 'title' => 'Share register sheet', 'type' => 'pdf', 'attached_to' => 'Course · Cooperative Membership and Share Records', 'attached_kind' => 'course', 'size' => '210 KB', 'updated' => '18 Sep 2026'],

            ['slug' => 'price-sheet', 'platform' => 'feedgrid', 'title' => 'Feed ingredient price sheet', 'type' => 'template', 'attached_to' => 'Course · Least-Cost Ration Formulation with Local Ingredients', 'attached_kind' => 'course', 'size' => '130 KB', 'updated' => '02 Sep 2026'],
            ['slug' => 'moisture-poster', 'platform' => 'feedgrid', 'title' => 'Moisture threshold poster', 'type' => 'infographic', 'attached_to' => 'Lesson · Why moisture is the whole story', 'attached_kind' => 'lesson', 'size' => '470 KB', 'updated' => '28 Jul 2026'],
            ['slug' => 'aflatoxin-manual', 'platform' => 'feedgrid', 'title' => 'Aflatoxin control manual', 'type' => 'manual', 'attached_to' => 'Academy · Feed safety and storage', 'attached_kind' => 'academy', 'size' => '2.1 MB', 'updated' => '28 Jul 2026'],
        ];
    }

    public static function types(): array
    {
        return [
            '' => 'Any type',
            ...self::formTypes(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function formTypes(): array
    {
        return [
            'manual' => 'Manual',
            'guide' => 'Guide',
            'template' => 'Template',
            'document' => 'Document',
            'infographic' => 'Infographic',
            'presentation' => 'Presentation',
            'pdf' => 'PDF',
            'video' => 'Video',
        ];
    }

    public static function typeLabel(string $type): string
    {
        return self::types()[$type] ?? $type;
    }

    /**
     * @return array<string, string>
     */
    public static function accepts(): array
    {
        return [
            'manual' => '.pdf,.doc,.docx',
            'guide' => '.pdf,.doc,.docx',
            'template' => '.pdf,.doc,.docx',
            'document' => '.pdf,.doc,.docx',
            'presentation' => '.pdf,.ppt,.pptx',
            'pdf' => '.pdf',
            'infographic' => '.png,.jpg,.jpeg,.webp,.pdf',
            'video' => '.mp4,.webm,.mov',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function fileHints(): array
    {
        return [
            'manual' => 'PDF or Word. This is labelled a manual, even if the file is a PDF.',
            'guide' => 'PDF or Word. This is labelled a guide, even if the file is a PDF.',
            'template' => 'PDF or Word. A filled-in sheet or blank form.',
            'document' => 'PDF or Word. A short note or circular.',
            'presentation' => 'PDF or PowerPoint slides.',
            'pdf' => 'A PDF that does not fit the other labels.',
            'infographic' => 'PNG, JPG, WebP or PDF.',
            'video' => 'MP4, WebM or MOV.',
        ];
    }

    /**
     * @return list<string>
     */
    public static function mimesFor(string $type): array
    {
        return match ($type) {
            'video' => ['mp4', 'webm', 'mov'],
            'infographic' => ['png', 'jpg', 'jpeg', 'webp', 'pdf'],
            'presentation' => ['pdf', 'ppt', 'pptx'],
            'pdf' => ['pdf'],
            default => ['pdf', 'doc', 'docx'],
        };
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function persisted(): array
    {
        if (! Schema::hasTable('learning_resources')) {
            return [];
        }

        return LearningResource::query()
            ->with('platform')
            ->latest()
            ->get()
            ->map(fn (LearningResource $resource): array => $resource->toFixtureArray())
            ->all();
    }
}
