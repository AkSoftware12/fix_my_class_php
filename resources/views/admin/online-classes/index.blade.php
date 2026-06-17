@extends('layouts.app')

@section('title', 'Online Classes')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Online Classes',
        'subtitle' => 'Scheduled live sessions and meeting links',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.online-classes.export', 'module' => 'online-classes'])->render()
            .(auth()->user()->can('online-classes.create')
                ? '<a href="'.route('admin.online-classes.create').'" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Schedule Class</a>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search title…">
                </div>
                <div class="col-md-3">
                    <select id="filterTeacher" class="form-select select2">
                        <option value="">All teachers</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->user?->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterStatus" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (\App\Models\OnlineClass::STATUSES as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" id="filterDate" class="form-control">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="onlineClassesTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Title</th><th>Teacher</th><th>Batch</th>
                            <th>Schedule</th><th>Meeting</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#onlineClassesTable').DataTable({
        ajax: {
            url: '{{ route('admin.online-classes.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.teacher_id = $('#filterTeacher').val();
                d.status = $('#filterStatus').val();
                d.class_date = $('#filterDate').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'title' },
            { data: 'teacher', orderable: false },
            { data: 'batch', orderable: false },
            { data: 'schedule' },
            { data: 'link', orderable: false },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterTeacher, #filterStatus, #filterDate').on('change', () => table.ajax.reload());
</script>
@endpush
