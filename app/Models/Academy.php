<?php

namespace App\Models;

use Database\Factories\AcademyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'platform_id',
    'slug',
    'name',
    'status',
])]
class Academy extends Model
{
    /** @use HasFactory<AcademyFactory> */
    use HasFactory;

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
