<?php

namespace Database\Factories;

use App\Models\Academy;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Academy>
 */
class AcademyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'platform_id' => Platform::factory(),
            'slug' => Str::slug($name),
            'name' => ucfirst($name),
            'status' => 'published',
        ];
    }
}
