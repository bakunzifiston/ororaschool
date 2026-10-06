<?php

namespace App\Models;

use App\Support\DemoData\Platforms;
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

#[Fillable(['name', 'email', 'password', 'role', 'district', 'sector', 'status'])]
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
        if ($this->role === UserRole::SuperAdmin) {
            $platform = Platforms::find($slug);

            return $platform !== null && ($platform['status'] ?? '') !== 'deleted';
        }

        if ($this->role === UserRole::PlatformStaff) {
            return $this->platforms()
                ->where('platforms.slug', $slug)
                ->where('platforms.status', 'active')
                ->exists();
        }

        return false;
    }

    public function dashboardUrl(): string
    {
        return match ($this->role) {
            UserRole::SuperAdmin => route('admin.dashboard'),
            UserRole::PlatformStaff => $this->assignedWorkspaceUrl(),
            default => route('learner.dashboard'),
        };
    }

    private function assignedWorkspaceUrl(): string
    {
        $slug = $this->platforms()
            ->where('platforms.status', 'active')
            ->orderBy('sort_order')
            ->value('slug')
            ?? $this->platforms()->orderBy('sort_order')->value('slug');

        return $slug
            ? route('workspace.dashboard', ['platform' => $slug])
            : route('home');
    }
}
