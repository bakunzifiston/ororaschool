<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\DemoData\People;
use App\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= 'password12',
            'role' => UserRole::Learner,
            'district' => fake()->randomElement(People::districts()),
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::SuperAdmin,
            'district' => 'Kigali',
        ]);
    }

    public function platformStaff(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::PlatformStaff,
        ]);
    }

    public function learner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Learner,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }
}
