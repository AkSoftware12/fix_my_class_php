@extends('layouts.app')

@section('title', 'Homework')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Homework',
        'subtitle' => 'Assignments and their submission progress',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.homework.export', 'module' => 'homework'])->render()
            .(auth()->user()->can('homework.create')
                ? '<a href="'.route('admin.homework.create').'" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Assign Homework</a>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search title…">
                </div>
                <div class="col-md-2">
                    <select id="filterType" class="form-select">
                        <option value="">All types</option>
                        <option value="text">Text</option>
                        <option value="pdf">PDF</option>
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterStatus" class="form-select">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="submitted">Submitted</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="completed">Completed</option>
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
                    <input type="date" id="filterDueDate" class="form-control" title="Due date">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="homeworkTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Homework</th><th>Subject</th><th>Type</th><th>Created By</th>
                            <th>Due Date</th><th>Submissions</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#homeworkTable').DataTable({
        ajax: {
            url: '{{ route('admin.homework.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.type = $('#filterType').val();
                d.status = $('#filterStatus').val();
                d.subject_id = $('#filterSubject').val();
                d.due_date = $('#filterDueDate').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'title' },
            { data: 'subject', orderable: false },
            { data: 'type' },
            { data: 'creator', orderable: false },
            { data: 'due_date' },
            { data: 'submissions' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterType, #filterStatus, #filterSubject, #filterDueDate').on('change', () => table.ajax.reload());
</script>
@endpush
