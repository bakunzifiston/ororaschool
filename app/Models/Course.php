<?php

namespace App\Models;

use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'platform_id',
    'academy_id',
    'slug',
    'title',
    'summary',
    'description',
    'instructor',
    'status',
    'difficulty',
    'language',
    'category',
    'modules',
    'lessons',
    'duration',
    'enrolled',
    'paid',
    'certificate_eligible',
    'enrollment_required',
    'cover',
    'content_updated_at',
])]
class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

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
            'paid' => 'boolean',
            'certificate_eligible' => 'boolean',
            'enrollment_required' => 'boolean',
            'modules' => 'integer',
            'lessons' => 'integer',
            'duration' => 'integer',
            'enrolled' => 'integer',
            'content_updated_at' => 'date',
        ];
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academy::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }

    public function curriculumModules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('sort_order')->orderBy('id');
    }

    public function curriculumLessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    #[Scope]
    protected function onActivePlatform(Builder $query): Builder
    {
        return $query->whereHas('platform', fn (Builder $platform) => $platform->where('status', 'active'));
    }
}
