<?php

namespace App\Models;

use App\Support\DemoData\Resources;
use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;

#[Fillable([
    'course_id',
    'module_id',
    'slug',
    'title',
    'type',
    'duration',
    'body',
    'source_url',
    'path',
    'is_preview',
    'quiz_slug',
    'sort_order',
])]
class Lesson extends Model
{
    /** @use HasFactory<LessonFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => 'video',
        'duration' => 0,
        'is_preview' => false,
        'sort_order' => 1,
    ];

    protected static function booted(): void
    {
        static::deleting(function (Lesson $lesson): void {
            if (filled($lesson->path)) {
                Storage::delete($lesson->path);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'is_preview' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function storeUploadedFile(UploadedFile $file): string
    {
        if (filled($this->path)) {
            Storage::delete($this->path);
        }

        return $file->store('lesson-files');
    }

    public function stream(bool $download = false): BinaryFileResponse
    {
        abort_unless(filled($this->path) && Storage::exists($this->path), 404);

        $extension = strtolower(pathinfo((string) $this->path, PATHINFO_EXTENSION) ?: 'pdf');
        $name = Str::slug($this->title).'.'.$extension;
        $mime = Storage::mimeType($this->path) ?: 'application/octet-stream';
        $inline = ! $download && $extension === 'pdf';

        $response = response()->file(Storage::path($this->path), [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
        ]);

        $response->headers->set('Content-Disposition', HeaderUtils::makeDisposition(
            $inline ? HeaderUtils::DISPOSITION_INLINE : HeaderUtils::DISPOSITION_ATTACHMENT,
            $name,
        ));

        return $response;
    }

    /**
     * Nested syllabus row: title, type, duration. No body.
     *
     * @return array<string, mixed>
     */
    public function toSyllabusArray(): array
    {
        return [
            'id' => $this->slug,
            'title' => $this->title,
            'type' => $this->type,
            'duration' => $this->duration,
            'body' => $this->body,
            'source_url' => $this->source_url,
            'path' => $this->path,
            'has_file' => filled($this->path),
            'is_preview' => $this->is_preview,
            'quiz' => $this->quiz_slug,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toPageArray(): array
    {
        $youtubeId = Resources::youtubeId($this->source_url);
        $hasFile = filled($this->path);

        return array_merge($this->toSyllabusArray(), [
            'course_slug' => $this->course->slug,
            'module' => $this->module->title,
            'module_id' => $this->module->slug,
            'body' => $this->body,
            'quiz' => $this->quiz_slug,
            'youtube_id' => $youtubeId,
            'youtube_embed' => $youtubeId === null ? null : 'https://www.youtube-nocookie.com/embed/'.$youtubeId,
            'youtube_watch' => $youtubeId === null ? null : 'https://www.youtube.com/watch?v='.$youtubeId,
            'file_url' => $hasFile
                ? route('learner.courses.lessons.file', ['course' => $this->course->slug, 'lesson' => $this->slug])
                : null,
            'download_url' => $hasFile
                ? route('learner.courses.lessons.file', ['course' => $this->course->slug, 'lesson' => $this->slug, 'download' => 1])
                : null,
        ]);
    }
}
