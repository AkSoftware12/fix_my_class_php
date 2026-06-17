<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price' => 999,
                'billing_cycle' => 'monthly',
                'max_branches' => 1,
                'max_teachers' => 10,
                'max_students' => 200,
                'features' => ['Homework module', 'Notices', 'Study materials', 'Email support'],
            ],
            [
                'name' => 'Growth',
                'slug' => 'growth',
                'price' => 2499,
                'billing_cycle' => 'monthly',
                'max_branches' => 3,
                'max_teachers' => 40,
                'max_students' => 1000,
                'features' => ['Everything in Starter', 'Online classes', 'Exams & results', 'CRM leads', 'Priority support'],
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 7999,
                'billing_cycle' => 'monthly',
                'max_branches' => 25,
                'max_teachers' => 500,
                'max_students' => 20000,
                'features' => ['Everything in Growth', 'Chat monitoring', 'API access', 'Dedicated manager'],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
