<?php

namespace App\Models;

use App\Support\DemoData\Curriculum;
use App\Support\DemoData\PublicCatalog;
use Database\Factories\EnrolmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'course_id',
    'status',
    'progress',
    'lessons_done',
    'enrolled_at',
    'completed_at',
    'due_on',
])]
class Enrolment extends Model
{
    /** @use HasFactory<EnrolmentFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'active',
        'progress' => 0,
        'lessons_done' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'lessons_done' => 'integer',
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'due_on' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public static function enrol(User $user, Course $course): self
    {
        $enrolment = self::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                'status' => 'active',
                'progress' => 0,
                'lessons_done' => 0,
                'enrolled_at' => now(),
                'due_on' => now()->addWeeks(2),
            ],
        );

        if ($enrolment->wasRecentlyCreated) {
            $course->increment('enrolled');
        }

        return $enrolment;
    }

    public function completeCurrentLesson(): void
    {
        $total = Lesson::query()->where('course_id', $this->course_id)->count();

        if ($this->status === 'completed' || $this->lessons_done >= $total) {
            return;
        }

        $this->lessons_done++;
        $this->progress = $total === 0 ? 100 : (int) round(($this->lessons_done / $total) * 100);

        if ($this->lessons_done >= $total) {
            $this->status = 'completed';
            $this->progress = 100;
            $this->completed_at = now();
        }

        $this->save();
    }

    /**
     * @return array<string, mixed>
     */
    public function toProgressArray(): array
    {
        $course = PublicCatalog::present($this->course);
        $lessons = Curriculum::lessonsFor($course['slug']);
        $index = min($this->lessons_done, max(0, count($lessons) - 1));

        return [
            'course' => $course['slug'],
            'status' => $this->status,
            'progress' => $this->progress,
            'lessons_done' => $this->lessons_done,
            'due' => $this->status === 'completed'
                ? 'Completed'
                : ($this->due_on?->format('j M Y') ?? ''),
            'course_data' => $course,
            'platform_name' => $course['platform_name'],
            'platform_slug' => $course['platform'],
            'platform_discipline' => $course['platform_discipline'],
            'lesson' => $lessons[$index] ?? null,
        ];
    }
}
