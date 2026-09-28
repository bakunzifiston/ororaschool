<?php

namespace Database\Seeders;

use App\Models\Platform;
use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $gloriose = User::query()->updateOrCreate(
            ['email' => 'g.mukandayisenga@ororaschool.rw'],
            [
                'name' => 'Gloriose Mukandayisenga',
                'password' => 'password12',
                'role' => UserRole::SuperAdmin,
                'district' => 'Kigali',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        $this->attachPlatforms($gloriose, ['ororafarm', 'gemura', 'buchapro', 'feedgrid']);

        $solange = User::query()->updateOrCreate(
            ['email' => 's.nyirahabimana@gemura.rw'],
            [
                'name' => 'Solange Nyirahabimana',
                'password' => 'password12',
                'role' => UserRole::PlatformStaff,
                'district' => 'Burera',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        $this->attachPlatforms($solange, ['gemura', 'buchapro', 'feedgrid']);

        $placide = User::query()->updateOrCreate(
            ['email' => 'p.bizimana@umuhinzi.rw'],
            [
                'name' => 'Placide Bizimana',
                'password' => 'password12',
                'role' => UserRole::Learner,
                'district' => 'Gatsibo',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        $this->attachPlatforms($placide, ['gemura', 'buchapro', 'feedgrid']);
    }

    /**
     * @param  list<string>  $slugs
     */
    private function attachPlatforms(User $user, array $slugs): void
    {
        $ids = Platform::query()->whereIn('slug', $slugs)->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        $user->platforms()->sync($ids);
    }
}
