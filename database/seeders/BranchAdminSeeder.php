<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class BranchAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            'VDA-CP' => ['name' => 'VDA Connaught Place Admin', 'email' => 'branchadmin.vda.cp@fixmyclass.com',  'mobile' => '9810030001'],
            'VDA-DW' => ['name' => 'VDA Dwarka Admin',          'email' => 'branchadmin.vda.dw@fixmyclass.com',  'mobile' => '9810030002'],
            'BFC-RO' => ['name' => 'BFC Rohini Admin',          'email' => 'branchadmin.bfc.ro@fixmyclass.com',  'mobile' => '9810030003'],
            'BFC-LN' => ['name' => 'BFC Laxmi Nagar Admin',     'email' => 'branchadmin.bfc.ln@fixmyclass.com',  'mobile' => '9810030004'],
            'ECM-AN' => ['name' => 'ECM Andheri Admin',         'email' => 'branchadmin.ecm.an@fixmyclass.com',  'mobile' => '9820030001'],
            'ECM-DD' => ['name' => 'ECM Dadar Admin',           'email' => 'branchadmin.ecm.dd@fixmyclass.com',  'mobile' => '9820030002'],
            'RSH-VN' => ['name' => 'RSH Vaishali Nagar Admin',  'email' => 'branchadmin.rsh.vn@fixmyclass.com',  'mobile' => '9414030001'],
            'RSH-MS' => ['name' => 'RSH Mansarovar Admin',      'email' => 'branchadmin.rsh.ms@fixmyclass.com',  'mobile' => '9414030002'],
            'UCI-HG' => ['name' => 'UCI Hazratganj Admin',      'email' => 'branchadmin.uci.hg@fixmyclass.com',  'mobile' => '9415030001'],
            'UCI-GN' => ['name' => 'UCI Gomti Nagar Admin',     'email' => 'branchadmin.uci.gn@fixmyclass.com',  'mobile' => '9415030002'],
            'BTA-BR' => ['name' => 'BTA Boring Road Admin',     'email' => 'branchadmin.bta.br@fixmyclass.com',  'mobile' => '9430030001'],
            'BTA-KK' => ['name' => 'BTA Kankarbagh Admin',      'email' => 'branchadmin.bta.kk@fixmyclass.com',  'mobile' => '9430030002'],
        ];

        foreach ($admins as $branchCode => $data) {
            $branch = Branch::where('code', $branchCode)->first();

            if (! $branch) {
                $this->command->warn("Branch '{$branchCode}' not found — skipping.");
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'mobile'            => $data['mobile'],
                    'password'          => 'Admin@1234',
                    'city_id'           => $branch->coaching->city_id,
                    'coaching_id'       => $branch->coaching_id,
                    'branch_id'         => $branch->id,
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles(['Branch Admin']);

            $this->command->info("Branch Admin created: {$data['email']}");
        }
    }
}
