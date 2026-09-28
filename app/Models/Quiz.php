<?php

namespace App\Models;

use Database\Factories\QuizFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'course_id',
    'slug',
    'title',
    'lesson_title',
    'question_count',
    'pass_score',
    'attempt_limit',
    'average_score',
    'items',
])]
class Quiz extends Model
{
    /** @use HasFactory<QuizFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'question_count' => 0,
        'pass_score' => 70,
        'attempt_limit' => 3,
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
            'question_count' => 'integer',
            'pass_score' => 'integer',
            'attempt_limit' => 'integer',
            'average_score' => 'integer',
            'items' => 'array',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    #[Scope]
    protected function onPlatform(Builder $query, string $platform): Builder
    {
        return $query->whereHas(
            'course.platform',
            fn (Builder $builder) => $builder->where('slug', $platform),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toPageArray(): array
    {
        $course = $this->course;

        return [
            'slug' => $this->slug,
            'platform' => $course->platform->slug,
            'title' => $this->title,
            'course' => $course->title,
            'course_slug' => $course->slug,
            'lesson' => $this->lesson_title,
            'questions' => $this->question_count,
            'pass' => $this->pass_score,
            'attempts' => $this->attempt_limit,
            'avg' => $this->average_score,
            'items' => $this->items ?? [],
        ];
    }
}
