<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Seeder;

class CityAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            'Delhi'         => ['name' => 'Delhi City Admin',   'email' => 'admin.delhi@fixmyclass.com',   'mobile' => '9810010001'],
            'Mumbai'        => ['name' => 'Mumbai City Admin',  'email' => 'admin.mumbai@fixmyclass.com',  'mobile' => '9820010001'],
            'Jaipur'        => ['name' => 'Jaipur City Admin',  'email' => 'admin.jaipur@fixmyclass.com',  'mobile' => '9414010001'],
            'Lucknow'       => ['name' => 'Lucknow City Admin', 'email' => 'admin.lucknow@fixmyclass.com', 'mobile' => '9415010001'],
            'Patna'         => ['name' => 'Patna City Admin',   'email' => 'admin.patna@fixmyclass.com',   'mobile' => '9430010001'],
        ];

        foreach ($admins as $cityName => $data) {
            $city = City::where('name', $cityName)->first();

            if (! $city) {
                $this->command->warn("City '{$cityName}' not found — skipping.");
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'               => $data['name'],
                    'mobile'             => $data['mobile'],
                    'password'           => 'Admin@1234',
                    'city_id'            => $city->id,
                    'is_active'          => true,
                    'email_verified_at'  => now(),
                ]
            );

            $user->syncRoles(['City Admin']);

            $this->command->info("City Admin created: {$data['email']}");
        }
    }
}
