@extends('layouts.app')

@section('title', 'Students')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Student Management',
        'subtitle' => 'Admitted students across your branches',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.students.export', 'module' => 'students'])->render()
            .(auth()->user()->can('students.create')
                ? '<a href="'.route('admin.students.create').'" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Admit Student</a>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search name, admission no…">
                </div>
                <div class="col-md-2">
                    <select id="filterBranch" class="form-select select2">
                        <option value="">All branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterClass" class="form-select select2">
                        <option value="">All classes</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterBatch" class="form-select select2">
                        <option value="">All batches</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
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

            <div id="bulkBar" class="d-none mb-2 d-flex align-items-center gap-2">
                <span class="text-muted small"><span id="selectedCount">0</span> student(s) selected</span>
                <button class="btn btn-sm btn-primary" onclick="printSelected()">
                    <i class="bi bi-printer me-1"></i> Print ID Cards
                </button>
                <button class="btn btn-sm btn-light" onclick="clearSelection()">Clear</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="studentsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll" title="Select all"></th>
                            <th>#</th><th>Admission No</th><th>Student</th><th>Mobile</th>
                            <th>Branch</th><th>Class</th><th>Batch</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#studentsTable').DataTable({
        ajax: {
            url: '{{ route('admin.students.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.branch_id = $('#filterBranch').val();
                d.school_class_id = $('#filterClass').val();
                d.batch_id = $('#filterBatch').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id', orderable: false, searchable: false, render: (id) =>
                `<input type="checkbox" class="row-check" value="${id}">` },
            { data: 'id' },
            { data: 'admission_number' },
            { data: 'student', orderable: false },
            { data: 'mobile', orderable: false },
            { data: 'branch', orderable: false },
            { data: 'class', orderable: false },
            { data: 'batch', orderable: false },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterBranch, #filterClass, #filterBatch, #filterStatus').on('change', () => table.ajax.reload());

    // Checkbox logic
    function getSelected() {
        return [...document.querySelectorAll('.row-check:checked')].map(c => c.value);
    }
    function updateBulkBar() {
        const sel = getSelected();
        $('#selectedCount').text(sel.length);
        sel.length > 0 ? $('#bulkBar').removeClass('d-none') : $('#bulkBar').addClass('d-none');
    }
    function clearSelection() {
        document.querySelectorAll('.row-check').forEach(c => c.checked = false);
        $('#selectAll').prop('checked', false);
        updateBulkBar();
    }
    function printSelected() {
        const ids = getSelected();
        if (!ids.length) return;
        window.open('{{ route('admin.students.id-cards.bulk') }}?ids=' + ids.join(','), '_blank');
    }

    $('#studentsTable').on('change', '.row-check', updateBulkBar);
    $('#selectAll').on('change', function () {
        document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
        updateBulkBar();
    });
    $('#studentsTable').on('draw.dt', () => {
        $('#selectAll').prop('checked', false);
        updateBulkBar();
    });
</script>
@endpush
