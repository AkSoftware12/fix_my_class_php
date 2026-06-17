@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Reports & Analytics',
        'subtitle' => 'Generate and export operational reports',
    ])

    @php($defs = [
        'students' => ['Student Report', 'bi-mortarboard', 'Admissions, placement and status of all students'],
        'teachers' => ['Teacher Report', 'bi-person-video3', 'Faculty list with qualifications and subjects'],
        'homework' => ['Homework Report', 'bi-journal-check', 'Assignments, due dates and submission counts'],
        'notices' => ['Notice Report', 'bi-megaphone', 'Published notices with read tracking'],
        'exams' => ['Exam Report', 'bi-clipboard-check', 'Exams with marks and result counts'],
        'leads' => ['CRM Report', 'bi-funnel', 'Admission leads across pipeline stages'],
    ])

    <div class="row g-3">
        @foreach ($types as $type)
            @php([$label, $icon, $description] = $defs[$type])
            <div class="col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="stat-icon text-bg-primary-subtle" style="width:46px;height:46px;border-radius:.8rem;display:grid;place-items:center">
                                <i class="bi {{ $icon }} fs-5"></i>
                            </div>
                            <h2 class="h6 fw-bold mb-0">{{ $label }}</h2>
                        </div>
                        <p class="text-muted small flex-grow-1">{{ $description }}</p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.show', $type) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            @can('reports.export')
                                <div class="btn-group btn-group-sm">
                                    <a class="btn btn-outline-primary" href="{{ route('admin.reports.export', [$type, 'format' => 'csv']) }}">CSV</a>
                                    <a class="btn btn-outline-primary" href="{{ route('admin.reports.export', [$type, 'format' => 'excel']) }}">Excel</a>
                                    <a class="btn btn-outline-primary" href="{{ route('admin.reports.export', [$type, 'format' => 'pdf']) }}">PDF</a>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
