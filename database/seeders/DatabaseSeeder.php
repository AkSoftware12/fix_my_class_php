<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            SettingSeeder::class,
            SubscriptionPlanSeeder::class,
            CityCoachingSeeder::class,
            CityAdminSeeder::class,
            CoachingAdminSeeder::class,
            BranchAdminSeeder::class,
        ]);
    }
}
