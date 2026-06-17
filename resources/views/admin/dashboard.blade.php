@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $cardDefs = [
            ['key' => 'cities', 'label' => 'Total Cities', 'icon' => 'bi-geo-alt', 'bg' => '#dbeafe', 'fg' => '#1d4ed8'],
            ['key' => 'coachings', 'label' => 'Total Coaching', 'icon' => 'bi-building', 'bg' => '#e0e7ff', 'fg' => '#4338ca'],
            ['key' => 'branches', 'label' => 'Total Branches', 'icon' => 'bi-diagram-3', 'bg' => '#cffafe', 'fg' => '#0e7490'],
            ['key' => 'teachers', 'label' => 'Total Teachers', 'icon' => 'bi-person-video3', 'bg' => '#fef9c3', 'fg' => '#a16207'],
            ['key' => 'students', 'label' => 'Total Students', 'icon' => 'bi-mortarboard', 'bg' => '#dcfce7', 'fg' => '#15803d'],
            ['key' => 'classes', 'label' => 'Total Classes', 'icon' => 'bi-easel', 'bg' => '#fae8ff', 'fg' => '#a21caf'],
            ['key' => 'batches', 'label' => 'Total Batches', 'icon' => 'bi-collection', 'bg' => '#ffe4e6', 'fg' => '#be123c'],
            ['key' => 'active_users', 'label' => 'Active Users', 'icon' => 'bi-person-check', 'bg' => '#d1fae5', 'fg' => '#047857'],
            ['key' => 'todays_homework', 'label' => "Today's Homework", 'icon' => 'bi-journal-check', 'bg' => '#fee2e2', 'fg' => '#b91c1c'],
            ['key' => 'active_notices', 'label' => 'Active Notices', 'icon' => 'bi-megaphone', 'bg' => '#ffedd5', 'fg' => '#c2410c'],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach ($cardDefs as $def)
            <div class="col-6 col-md-4 col-xl-cards" style="--bs-columns: 5;">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3 py-3">
                        <div class="stat-icon" style="background: {{ $def['bg'] }}; color: {{ $def['fg'] }}">
                            <i class="bi {{ $def['icon'] }}"></i>
                        </div>
                        <div>
                            <h3>{{ number_format($cards[$def['key']] ?? 0) }}</h3>
                            <div class="text-muted small">{{ $def['label'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-graph-up-arrow me-1 text-primary"></i> Student Admissions (12 months)</h2>
                    <div id="chartAdmissions"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-pie-chart me-1 text-primary"></i> Homework Status</h2>
                    <div id="chartHomework"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-funnel me-1 text-primary"></i> Admission Lead Pipeline</h2>
                    <div id="chartLeads"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-megaphone me-1 text-primary"></i> Recent Notices</h2>
                    @forelse ($recentNotices as $notice)
                        <div class="d-flex gap-3 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                            <div class="pt-1">
                                @include('admin.notices.partials.type', ['type' => $notice->type])
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $notice->title }}</div>
                                <div class="text-muted small">{{ $notice->publish_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No active notices.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    @media (min-width: 1200px) { .col-xl-cards { flex: 0 0 20%; max-width: 20%; } }
</style>
@endpush

@push('scripts')
<script>
    const primary = getComputedStyle(document.documentElement).getPropertyValue('--fmc-primary').trim() || '#2563EB';

    new ApexCharts(document.querySelector('#chartAdmissions'), {
        chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'Inter' },
        series: [{ name: 'Admissions', data: @json($admissionsTrend['values']) }],
        xaxis: { categories: @json($admissionsTrend['labels']), labels: { rotate: -35 } },
        colors: [primary],
        stroke: { curve: 'smooth', width: 3 },
        dataLabels: { enabled: false },
        fill: { type: 'gradient', gradient: { opacityFrom: .35, opacityTo: .02 } },
        grid: { strokeDashArray: 4 },
    }).render();

    new ApexCharts(document.querySelector('#chartHomework'), {
        chart: { type: 'donut', height: 300, fontFamily: 'Inter' },
        series: @json(array_values($homeworkStatus)),
        labels: @json(array_keys($homeworkStatus)),
        colors: ['#f59e0b', '#3b82f6', '#8b5cf6', '#10b981'],
        legend: { position: 'bottom' },
        dataLabels: { enabled: false },
        plotOptions: { pie: { donut: { size: '70%' } } },
    }).render();

    new ApexCharts(document.querySelector('#chartLeads'), {
        chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Inter' },
        series: [{ name: 'Leads', data: @json(array_values($leadPipeline)) }],
        xaxis: { categories: @json(array_keys($leadPipeline)) },
        colors: [primary],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '45%', distributed: false } },
        dataLabels: { enabled: false },
        grid: { strokeDashArray: 4 },
    }).render();
</script>
@endpush
