<?php

namespace App\Models;

use App\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'district', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => 'learner',
        'status' => 'active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class)->withTimestamps();
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }

    public function canAccessWorkspace(string $slug): bool
    {
        return match ($this->role) {
            UserRole::SuperAdmin => true,
            UserRole::PlatformStaff => $this->platforms()->where('platforms.slug', $slug)->exists(),
            default => false,
        };
    }

    public function dashboardUrl(): string
    {
        return match ($this->role) {
            UserRole::SuperAdmin => route('admin.dashboard'),
            UserRole::PlatformStaff => route('workspace.dashboard', [
                'platform' => $this->platforms()->orderBy('sort_order')->value('slug') ?? 'gemura',
            ]),
            default => route('learner.dashboard'),
        };
    }
}
