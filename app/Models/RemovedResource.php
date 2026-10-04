<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

#[Fillable(['platform', 'slug'])]
class RemovedResource extends Model
{
    public static function forget(string $platform, string $slug): void
    {
        static::query()->firstOrCreate([
            'platform' => $platform,
            'slug' => $slug,
        ]);
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        if (! Schema::hasTable('removed_resources')) {
            return [];
        }

        return static::query()
            ->get()
            ->map(fn (self $row): string => $row->platform.':'.$row->slug)
            ->all();
    }
}
