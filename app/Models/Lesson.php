<?php

namespace App\Models;

use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'course_id',
    'module_id',
    'slug',
    'title',
    'type',
    'duration',
    'body',
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
            'is_preview' => $this->is_preview,
            'quiz' => $this->quiz_slug,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toPageArray(): array
    {
        return array_merge($this->toSyllabusArray(), [
            'course_slug' => $this->course->slug,
            'module' => $this->module->title,
            'module_id' => $this->module->slug,
            'body' => $this->body,
            'quiz' => $this->quiz_slug,
        ]);
    }
}
