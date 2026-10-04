<?php

namespace App\Models;

use App\Support\DemoData\Resources;
use Database\Factories\LearningResourceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

#[Fillable([
    'platform_id',
    'title',
    'slug',
    'type',
    'attached_kind',
    'attached_to',
    'attached_key',
    'size',
    'path',
])]
class LearningResource extends Model
{
    /** @use HasFactory<LearningResourceFactory> */
    use HasFactory;

    /**
     * @param  array{title: string, type: string, attached_kind: string, attached_key: string, file?: UploadedFile|null}  $payload
     */
    public static function createOnPlatform(Platform $platform, array $payload): self
    {
        $kind = $payload['attached_kind'];
        $key = (string) $payload['attached_key'];
        $target = self::findTarget($platform, $kind, $key);

        abort_unless($target !== null, 422);

        $file = $payload['file'] ?? null;
        $path = null;
        $size = '—';

        if ($file instanceof UploadedFile) {
            $path = $file->store('learning-resources');
            $size = self::formatSize((int) $file->getSize());
        }

        return self::query()->create([
            'platform_id' => $platform->id,
            'title' => $payload['title'],
            'slug' => self::uniqueSlug($platform, $payload['title']),
            'type' => $payload['type'],
            'attached_kind' => $kind,
            'attached_to' => $target['label'],
            'attached_key' => $key,
            'size' => $size,
            'path' => $path,
        ]);
    }

    /**
     * @return array{label: string, model: Model}|null
     */
    public static function findTarget(Platform $platform, string $kind, string $key): ?array
    {
        if ($key === '' || ! ctype_digit($key)) {
            return null;
        }

        $id = (int) $key;

        return match ($kind) {
            'academy' => self::presentTarget(
                $platform->academies()->whereKey($id)->first(),
                fn (Academy $academy): string => 'Academy · '.$academy->name,
            ),
            'course' => self::presentTarget(
                $platform->courses()->whereKey($id)->first(),
                fn (Course $course): string => 'Course · '.$course->title,
            ),
            'module' => self::presentTarget(
                Module::query()
                    ->whereKey($id)
                    ->whereHas('course', fn ($query) => $query->where('platform_id', $platform->id))
                    ->first(),
                fn (Module $module): string => 'Module · '.$module->title,
            ),
            'lesson' => self::presentTarget(
                Lesson::query()
                    ->whereKey($id)
                    ->whereHas('course', fn ($query) => $query->where('platform_id', $platform->id))
                    ->first(),
                fn (Lesson $lesson): string => 'Lesson · '.$lesson->title,
            ),
            default => null,
        };
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function targetsFor(Platform $platform): array
    {
        $courses = $platform->courses()
            ->with(['curriculumModules.lessons'])
            ->orderBy('title')
            ->get();

        $academies = ['' => 'Choose a focus'];
        $courseOptions = ['' => 'Choose a course'];
        $modules = ['' => 'Choose a module'];
        $lessons = ['' => 'Choose a lesson'];

        foreach ($platform->academies()->orderBy('name')->get() as $academy) {
            $academies[(string) $academy->id] = $academy->name;
        }

        foreach ($courses as $course) {
            $courseOptions[(string) $course->id] = $course->title;

            foreach ($course->curriculumModules as $module) {
                $modules[(string) $module->id] = $module->title.' · '.$course->title;

                foreach ($module->lessons as $lesson) {
                    $lessons[(string) $lesson->id] = $lesson->title.' · '.$course->title;
                }
            }
        }

        return [
            'academy' => $academies,
            'course' => $courseOptions,
            'module' => $modules,
            'lesson' => $lessons,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toFixtureArray(): array
    {
        return [
            'slug' => $this->slug,
            'platform' => $this->platform->slug,
            'title' => $this->title,
            'type' => $this->type,
            'attached_to' => $this->attached_to,
            'attached_kind' => $this->attached_kind,
            'size' => $this->size,
            'updated' => $this->updated_at?->format('d M Y') ?? now()->format('d M Y'),
        ];
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * @template T of Model
     *
     * @param  T|null  $model
     * @param  callable(T): string  $label
     * @return array{label: string, model: T}|null
     */
    private static function presentTarget(?Model $model, callable $label): ?array
    {
        if ($model === null) {
            return null;
        }

        return [
            'label' => $label($model),
            'model' => $model,
        ];
    }

    private static function uniqueSlug(Platform $platform, string $title): string
    {
        $base = Str::slug($title) ?: 'resource';
        $slug = $base;
        $taken = array_column(Resources::fixturesForPlatform($platform->slug), 'slug');
        $index = 2;

        while (in_array($slug, $taken, true) || self::query()->where('platform_id', $platform->id)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$index;
            $index++;
        }

        return $slug;
    }

    private static function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1).' MB';
        }

        return max(1, (int) ceil($bytes / 1024)).' KB';
    }
}
