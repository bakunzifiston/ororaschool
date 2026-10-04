<?php

namespace App\Models;

use App\Support\DemoData\Platforms as PlatformFixtures;
use Database\Factories\PlatformFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'slug',
    'name',
    'discipline',
    'tagline',
    'description',
    'steward',
    'region',
    'learner_count',
    'instructor_count',
    'status',
    'sort_order',
    'joined_at',
    'completion_rate',
    'cover',
])]
class Platform extends Model
{
    /** @use HasFactory<PlatformFactory> */
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
            'joined_at' => 'date',
            'learner_count' => 'integer',
            'instructor_count' => 'integer',
            'sort_order' => 'integer',
            'completion_rate' => 'integer',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function academies(): HasMany
    {
        return $this->hasMany(Academy::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public static function firstOrCreateFromSlug(string $slug): self
    {
        $fixture = PlatformFixtures::find($slug);

        return static::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $fixture['name'] ?? Str::headline($slug),
                'discipline' => $fixture['discipline'] ?? 'Farm management',
                'tagline' => $fixture['tagline'] ?? '',
                'description' => $fixture['description'] ?? '',
                'steward' => $fixture['steward'] ?? 'FarmSchool',
                'region' => $fixture['region'] ?? 'Rwanda',
                'learner_count' => $fixture['learners'] ?? 0,
                'instructor_count' => $fixture['instructors'] ?? 0,
                'status' => $fixture['status'] ?? 'active',
                'sort_order' => 10,
                'joined_at' => now()->subYear(),
                'completion_rate' => $fixture['completion_rate'] ?? 0,
                'cover' => $fixture['cover'] ?? null,
            ],
        );
    }

    public function toggleActive(): void
    {
        $this->status = $this->status === 'active' ? 'inactive' : 'active';
        $this->save();
    }

    public function removeFromEstate(): void
    {
        $this->status = 'deleted';
        $this->save();
    }

    /**
     * @param  array{name: string, slug: string, description?: string|null, active?: bool}  $attributes
     */
    public static function createOnEstate(array $attributes): self
    {
        return static::query()->create([
            'slug' => $attributes['slug'],
            'name' => $attributes['name'],
            'discipline' => $attributes['discipline'] ?? 'Farm management',
            'tagline' => $attributes['tagline'] ?? '',
            'description' => $attributes['description'] ?? '',
            'steward' => $attributes['steward'] ?? 'FarmSchool',
            'region' => $attributes['region'] ?? 'Rwanda',
            'learner_count' => 0,
            'instructor_count' => 0,
            'status' => ($attributes['active'] ?? false) ? 'active' : 'inactive',
            'sort_order' => ((int) static::query()->max('sort_order')) + 1,
            'joined_at' => now(),
            'completion_rate' => 0,
            'cover' => null,
        ]);
    }

    /**
     * @param  array{name: string, description?: string|null, active?: bool}  $attributes
     */
    public function updateOnEstate(array $attributes): void
    {
        $this->fill([
            'name' => $attributes['name'],
            'description' => $attributes['description'] ?? $this->description,
            'status' => $this->isRemoved()
                ? 'deleted'
                : (($attributes['active'] ?? false) ? 'active' : 'inactive'),
        ]);
        $this->save();
    }

    public function isRemoved(): bool
    {
        return $this->status === 'deleted';
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
