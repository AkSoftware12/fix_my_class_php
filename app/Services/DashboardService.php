<?php

namespace App\Services;

use App\Models\AdmissionLead;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\City;
use App\Models\Coaching;
use App\Models\Homework;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

class DashboardService
{
    /**
     * Headline cards scoped to the viewer's tenancy level.
     */
    public function cards(User $user): array
    {
        return [
            'cities' => $user->hasRole('Super Admin')
                ? City::count()
                : City::where('id', $user->city_id ?? $user->coaching?->city_id)->count(),
            'coachings' => Coaching::visibleTo($user)->count(),
            'branches' => Branch::visibleTo($user)->count(),
            'teachers' => Teacher::visibleTo($user)->count(),
            'students' => Student::visibleTo($user)->count(),
            'classes' => SchoolClass::visibleTo($user)->count(),
            'batches' => Batch::visibleTo($user)->count(),
            'active_users' => User::visibleTo($user)->active()->count(),
            'todays_homework' => Homework::visibleTo($user)->whereDate('created_at', today())->count(),
            'active_notices' => Notice::query()
                ->when(! $user->hasRole('Super Admin'), function ($q) use ($user) {
                    $user->hasRole('City Admin')
                        ? $q->whereHas('coaching', fn ($c) => $c->where('city_id', $user->city_id))
                        : $q->where('coaching_id', $user->coaching_id);
                })
                ->live()
                ->count(),
        ];
    }

    /**
     * Monthly student admissions for the last 12 months (ApexCharts series).
     */
    public function admissionsTrend(User $user): array
    {
        $start = now()->subMonths(11)->startOfMonth();

        // Grouped in PHP to stay portable across MySQL/SQLite.
        $raw = Student::visibleTo($user)
            ->where('created_at', '>=', $start)
            ->pluck('created_at')
            ->countBy(fn ($createdAt) => $createdAt->format('Y-m'));

        $labels = [];
        $values = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);
            $labels[] = $month->format('M Y');
            $values[] = (int) ($raw[$month->format('Y-m')] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Homework status distribution (donut chart).
     */
    public function homeworkStatus(User $user): array
    {
        $counts = Homework::visibleTo($user)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(Homework::STATUSES)
            ->mapWithKeys(fn ($s) => [ucfirst($s) => (int) ($counts[$s] ?? 0)])
            ->all();
    }

    /**
     * CRM pipeline counts by stage (bar chart).
     */
    public function leadPipeline(User $user): array
    {
        $counts = AdmissionLead::visibleTo($user)
            ->selectRaw('stage, count(*) as total')
            ->groupBy('stage')
            ->pluck('total', 'stage');

        return collect(AdmissionLead::STAGES)
            ->mapWithKeys(fn ($label, $stage) => [$label => (int) ($counts[$stage] ?? 0)])
            ->all();
    }

    public function recentNotices(User $user, int $limit = 6)
    {
        return Notice::query()
            ->when(! $user->hasRole('Super Admin'), function ($q) use ($user) {
                $user->hasRole('City Admin')
                    ? $q->whereHas('coaching', fn ($c) => $c->where('city_id', $user->city_id))
                    : $q->where('coaching_id', $user->coaching_id);
            })
            ->live()
            ->latest('publish_at')
            ->limit($limit)
            ->get();
    }
}
