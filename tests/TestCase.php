<?php

namespace Tests;

use App\Models\Platform;
use App\Models\User;
use App\UserRole;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\EnrolmentSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    protected function actingAsSuperAdmin(): static
    {
        return $this->actingAsRole(UserRole::SuperAdmin);
    }

    protected function actingAsPlatformStaff(): static
    {
        return $this->actingAsRole(UserRole::PlatformStaff);
    }

    protected function actingAsLearner(): static
    {
        return $this->actingAsRole(UserRole::Learner);
    }

    protected function actingAsLearnerWithRecord(): static
    {
        $this->seed(CatalogSeeder::class);

        $user = User::factory()->learner()->create([
            'name' => 'Placide Bizimana',
            'district' => 'Gatsibo',
        ]);

        (new EnrolmentSeeder)->seedFor($user);

        return $this->actingAs($user);
    }

    protected function actingAsRole(UserRole $role): static
    {
        $user = match ($role) {
            UserRole::SuperAdmin => User::factory()->superAdmin()->create(),
            UserRole::PlatformStaff => User::factory()->platformStaff()->create(),
            UserRole::Learner => User::factory()->learner()->create(),
        };

        if ($role === UserRole::PlatformStaff) {
            $user->platforms()->sync(
                collect(['gemura', 'buchapro', 'feedgrid'])
                    ->map(fn (string $slug): int => Platform::firstOrCreateFromSlug($slug)->id)
                    ->all(),
            );
        }

        return $this->actingAs($user);
    }
}
