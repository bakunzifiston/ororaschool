<?php

namespace Database\Factories;

use App\Models\LearningResource;
use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LearningResource>
 */
class LearningResourceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'platform_id' => Platform::factory(),
            'title' => Str::title($title),
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('##'),
            'type' => 'manual',
            'attached_kind' => 'academy',
            'attached_to' => 'Academy · Milk hygiene',
            'attached_key' => '1',
            'size' => '120 KB',
            'path' => null,
            'source_url' => null,
        ];
    }
}
