<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboard)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('admin.dashboard', [
            'cards' => $this->dashboard->cards($user),
            'admissionsTrend' => $this->dashboard->admissionsTrend($user),
            'homeworkStatus' => $this->dashboard->homeworkStatus($user),
            'leadPipeline' => $this->dashboard->leadPipeline($user),
            'recentNotices' => $this->dashboard->recentNotices($user),
        ]);
    }
}
