<?php

namespace Database\Seeders;

use App\Models\Coaching;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoachingAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            'vidya.delhi@example.com'    => ['name' => 'Vidya Academy Admin',    'email' => 'coachadmin.vidya@fixmyclass.com',    'mobile' => '9810020001'],
            'brightfuture@example.com'   => ['name' => 'Bright Future Admin',    'email' => 'coachadmin.bright@fixmyclass.com',   'mobile' => '9810020002'],
            'excel.mumbai@example.com'   => ['name' => 'Excel Coaching Admin',   'email' => 'coachadmin.excel@fixmyclass.com',    'mobile' => '9820020001'],
            'rajstudy@example.com'       => ['name' => 'Rajasthan Study Admin',  'email' => 'coachadmin.rajstudy@fixmyclass.com', 'mobile' => '9414020001'],
            'upcareer@example.com'       => ['name' => 'UP Career Admin',        'email' => 'coachadmin.upcareer@fixmyclass.com', 'mobile' => '9415020001'],
            'bihatalent@example.com'     => ['name' => 'Bihar Talent Admin',     'email' => 'coachadmin.bihar@fixmyclass.com',    'mobile' => '9430020001'],
        ];

        foreach ($admins as $coachingEmail => $data) {
            $coaching = Coaching::where('email', $coachingEmail)->first();

            if (! $coaching) {
                $this->command->warn("Coaching '{$coachingEmail}' not found — skipping.");
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'mobile'            => $data['mobile'],
                    'password'          => 'Admin@1234',
                    'city_id'           => $coaching->city_id,
                    'coaching_id'       => $coaching->id,
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles(['Coaching Admin']);

            $this->command->info("Coaching Admin created: {$data['email']}");
        }
    }
}
