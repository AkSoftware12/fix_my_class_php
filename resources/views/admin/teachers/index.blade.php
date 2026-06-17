@extends('layouts.app')

@section('title', 'Teachers')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Teacher Management',
        'subtitle' => 'Faculty across your branches',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.teachers.export', 'module' => 'teachers'])->render()
            .(auth()->user()->can('teachers.create')
                ? '<a href="'.route('admin.teachers.create').'" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Teacher</a>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search name, email, mobile…">
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
                    <select id="filterSubject" class="form-select select2">
                        <option value="">All subjects</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
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
                <table class="table table-hover fmc-datatable" id="teachersTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Teacher</th><th>Mobile</th><th>Qualification</th>
                            <th>Subject</th><th>Branch</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#teachersTable').DataTable({
        ajax: {
            url: '{{ route('admin.teachers.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.branch_id = $('#filterBranch').val();
                d.subject_id = $('#filterSubject').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'teacher', orderable: false },
            { data: 'mobile', orderable: false },
            { data: 'qualification' },
            { data: 'subject', orderable: false },
            { data: 'branch', orderable: false },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterBranch, #filterSubject, #filterStatus').on('change', () => table.ajax.reload());
</script>
@endpush
