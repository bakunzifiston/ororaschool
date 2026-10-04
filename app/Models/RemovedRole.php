<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key'])]
class RemovedRole extends Model
{
    public static function forget(string $key): void
    {
        static::query()->firstOrCreate(['key' => $key]);
    }

    public static function keys(): array
    {
        return static::query()->pluck('key')->all();
    }
}
