<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\City;
use App\Models\Coaching;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CityCoachingSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Delhi', 'state' => 'Delhi'],
            ['name' => 'Mumbai', 'state' => 'Maharashtra'],
            ['name' => 'Jaipur', 'state' => 'Rajasthan'],
            ['name' => 'Lucknow', 'state' => 'Uttar Pradesh'],
            ['name' => 'Patna', 'state' => 'Bihar'],
        ];

        $coachings = [
            'Delhi' => [
                [
                    'name' => 'Vidya Academy Delhi', 'owner_name' => 'Ramesh Sharma',
                    'email' => 'vidya.delhi@example.com', 'mobile' => '9810001001',
                    'branches' => [
                        ['name' => 'Connaught Place Branch', 'code' => 'VDA-CP', 'address' => 'Connaught Place, New Delhi', 'contact_number' => '9810002001'],
                        ['name' => 'Dwarka Branch',          'code' => 'VDA-DW', 'address' => 'Dwarka Sector 12, New Delhi', 'contact_number' => '9810002002'],
                    ],
                ],
                [
                    'name' => 'Bright Future Classes', 'owner_name' => 'Sunita Gupta',
                    'email' => 'brightfuture@example.com', 'mobile' => '9810001002',
                    'branches' => [
                        ['name' => 'Rohini Branch',    'code' => 'BFC-RO', 'address' => 'Rohini Sector 3, Delhi',    'contact_number' => '9810003001'],
                        ['name' => 'Laxmi Nagar Branch', 'code' => 'BFC-LN', 'address' => 'Laxmi Nagar, Delhi',     'contact_number' => '9810003002'],
                    ],
                ],
            ],
            'Mumbai' => [
                [
                    'name' => 'Excel Coaching Mumbai', 'owner_name' => 'Suresh Patil',
                    'email' => 'excel.mumbai@example.com', 'mobile' => '9820001001',
                    'branches' => [
                        ['name' => 'Andheri Branch', 'code' => 'ECM-AN', 'address' => 'Andheri West, Mumbai', 'contact_number' => '9820002001'],
                        ['name' => 'Dadar Branch',   'code' => 'ECM-DD', 'address' => 'Dadar East, Mumbai',   'contact_number' => '9820002002'],
                    ],
                ],
            ],
            'Jaipur' => [
                [
                    'name' => 'Rajasthan Study Hub', 'owner_name' => 'Mohan Meena',
                    'email' => 'rajstudy@example.com', 'mobile' => '9414001001',
                    'branches' => [
                        ['name' => 'Vaishali Nagar Branch', 'code' => 'RSH-VN', 'address' => 'Vaishali Nagar, Jaipur', 'contact_number' => '9414002001'],
                        ['name' => 'Mansarovar Branch',     'code' => 'RSH-MS', 'address' => 'Mansarovar, Jaipur',     'contact_number' => '9414002002'],
                    ],
                ],
            ],
            'Lucknow' => [
                [
                    'name' => 'UP Career Institute', 'owner_name' => 'Anil Verma',
                    'email' => 'upcareer@example.com', 'mobile' => '9415001001',
                    'branches' => [
                        ['name' => 'Hazratganj Branch', 'code' => 'UCI-HG', 'address' => 'Hazratganj, Lucknow', 'contact_number' => '9415002001'],
                        ['name' => 'Gomti Nagar Branch', 'code' => 'UCI-GN', 'address' => 'Gomti Nagar, Lucknow', 'contact_number' => '9415002002'],
                    ],
                ],
            ],
            'Patna' => [
                [
                    'name' => 'Bihar Talent Academy', 'owner_name' => 'Vijay Kumar',
                    'email' => 'bihatalent@example.com', 'mobile' => '9430001001',
                    'branches' => [
                        ['name' => 'Boring Road Branch', 'code' => 'BTA-BR', 'address' => 'Boring Road, Patna', 'contact_number' => '9430002001'],
                        ['name' => 'Kankarbagh Branch',  'code' => 'BTA-KK', 'address' => 'Kankarbagh, Patna',  'contact_number' => '9430002002'],
                    ],
                ],
            ],
        ];

        foreach ($cities as $cityData) {
            $city = City::firstOrCreate(
                ['name' => $cityData['name'], 'state' => $cityData['state']],
                ['is_active' => true]
            );

            foreach ($coachings[$cityData['name']] ?? [] as $coachingData) {
                $coaching = Coaching::firstOrCreate(
                    ['email' => $coachingData['email']],
                    [
                        'city_id'    => $city->id,
                        'name'       => $coachingData['name'],
                        'slug'       => Str::slug($coachingData['name']),
                        'owner_name' => $coachingData['owner_name'],
                        'mobile'     => $coachingData['mobile'],
                        'is_active'  => true,
                    ]
                );

                foreach ($coachingData['branches'] ?? [] as $branchData) {
                    Branch::firstOrCreate(
                        ['code' => $branchData['code']],
                        [
                            'coaching_id'    => $coaching->id,
                            'name'           => $branchData['name'],
                            'address'        => $branchData['address'],
                            'contact_number' => $branchData['contact_number'],
                            'is_active'      => true,
                        ]
                    );
                }
            }
        }
    }
}
