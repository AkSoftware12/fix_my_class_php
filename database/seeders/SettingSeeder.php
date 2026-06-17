<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'general' => [
                'app_name' => 'Fix My Class',
                'app_tagline' => 'Coaching Management SaaS Platform',
                'contact_email' => 'support@fixmyclass.com',
                'contact_phone' => '',
                'address' => '',
                'timezone' => 'Asia/Kolkata',
                'date_format' => 'd M Y',
            ],
            'smtp' => [
                'smtp_host' => '',
                'smtp_port' => '587',
                'smtp_username' => '',
                'smtp_password' => '',
                'smtp_encryption' => 'tls',
                'smtp_from_address' => 'noreply@fixmyclass.com',
                'smtp_from_name' => 'Fix My Class',
            ],
            'sms' => [
                'sms_provider' => 'none',
                'sms_api_key' => '',
                'sms_sender_id' => '',
            ],
            'notifications' => [
                'notify_email_enabled' => '1',
                'notify_sms_enabled' => '0',
                'notify_homework' => '1',
                'notify_notices' => '1',
                'notify_exam_results' => '1',
            ],
            'theme' => [
                'theme_primary_color' => '#2563EB',
                'theme_default_mode' => 'light',
                'theme_sidebar_compact' => '0',
            ],
        ];

        foreach ($defaults as $group => $values) {
            foreach ($values as $key => $value) {
                Setting::firstOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
            }
        }

        forget_settings_cache();
    }
}
