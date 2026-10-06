<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use RuntimeException;

class Rwanda
{
    /** @var array<string, list<string>>|null */
    private static ?array $sectorsByDistrict = null;

    /**
     * Official districts of Rwanda (Akarere).
     *
     * @return list<string>
     */
    public static function districts(): array
    {
        return array_keys(self::sectorsByDistrict());
    }

    /**
     * Sectors (Imirenge) in one district.
     *
     * @return list<string>
     */
    public static function sectorsIn(string $district): array
    {
        return self::sectorsByDistrict()[$district] ?? [];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function sectorsByDistrict(): array
    {
        return self::$sectorsByDistrict ??= self::load();
    }

    /**
     * @return array<string, list<string>>
     */
    private static function load(): array
    {
        $decoded = json_decode(File::get(__DIR__.'/rwanda-sectors.json'), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decoded) || $decoded === [] || array_is_list($decoded)) {
            throw new RuntimeException('Rwanda sector data is missing or malformed.');
        }

        /** @var array<string, list<string>> $decoded */
        return $decoded;
    }
}
