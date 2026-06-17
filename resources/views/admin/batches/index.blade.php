@extends('layouts.app')

@section('title', 'Batches')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Batch Management',
        'subtitle' => 'Batches with assigned students, teachers and subjects',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.batches.export', 'module' => 'batches'])->render()
            .(auth()->user()->can('batches.create')
                ? '<a href="'.route('admin.batches.create').'" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Batch</a>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search batches…">
                </div>
                <div class="col-md-3">
                    <select id="filterBranch" class="form-select select2">
                        <option value="">All branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterClass" class="form-select select2">
                        <option value="">All classes</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterStatus" class="form-select">
                        <option value="">All statuses</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="batchesTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Batch</th><th>Branch</th><th>Class</th><th>Timing</th>
                            <th>Students</th><th>Teachers</th><th>Subjects</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#batchesTable').DataTable({
        ajax: {
            url: '{{ route('admin.batches.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.branch_id = $('#filterBranch').val();
                d.school_class_id = $('#filterClass').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'branch', orderable: false },
            { data: 'class', orderable: false },
            { data: 'timing' },
            { data: 'students_count' },
            { data: 'teachers_count' },
            { data: 'subjects_count' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterBranch, #filterClass, #filterStatus').on('change', () => table.ajax.reload());
</script>
@endpush
