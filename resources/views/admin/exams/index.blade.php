@extends('layouts.app')

@section('title', 'Exams')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Exams & Results',
        'subtitle' => 'MCQ, subjective and assignment exams',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.exams.export', 'module' => 'exams'])->render()
            .(auth()->user()->can('exams.create')
                ? '<a href="'.route('admin.exams.create').'" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Create Exam</a>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search title…">
                </div>
                <div class="col-md-2">
                    <select id="filterType" class="form-select">
                        <option value="">All types</option>
                        @foreach (\App\Models\Exam::TYPES as $type)
                            <option value="{{ $type }}">{{ strtoupper($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterStatus" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (\App\Models\Exam::STATUSES as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterSubject" class="form-select select2">
                        <option value="">All subjects</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="examsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Exam</th><th>Type</th><th>Subject</th><th>Class / Batch</th>
                            <th>Date</th><th>Marks</th><th>Questions</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#examsTable').DataTable({
        ajax: {
            url: '{{ route('admin.exams.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.type = $('#filterType').val();
                d.status = $('#filterStatus').val();
                d.subject_id = $('#filterSubject').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'title' },
            { data: 'type' },
            { data: 'subject', orderable: false },
            { data: 'class_batch', orderable: false },
            { data: 'exam_date' },
            { data: 'marks' },
            { data: 'questions_count' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterType, #filterStatus, #filterSubject').on('change', () => table.ajax.reload());
</script>
@endpush
