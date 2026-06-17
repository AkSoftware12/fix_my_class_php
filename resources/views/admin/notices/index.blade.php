@extends('layouts.app')

@section('title', 'Notices')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Notice Board',
        'subtitle' => 'Announcements with scheduling and read tracking',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.notices.export', 'module' => 'notices'])->render()
            .(auth()->user()->can('notices.create')
                ? '<a href="'.route('admin.notices.create').'" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Publish Notice</a>'
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
                        @foreach (\App\Models\Notice::TYPES as $type)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterAudience" class="form-select">
                        <option value="">All audiences</option>
                        <option value="all">Everyone</option>
                        <option value="teachers">Teachers only</option>
                        <option value="students">Students only</option>
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
                <table class="table table-hover fmc-datatable" id="noticesTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Title</th><th>Type</th><th>Audience</th><th>Published</th>
                            <th>Expires</th><th>Reads</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#noticesTable').DataTable({
        ajax: {
            url: '{{ route('admin.notices.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.type = $('#filterType').val();
                d.audience = $('#filterAudience').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'title' },
            { data: 'type' },
            { data: 'audience' },
            { data: 'publish_at' },
            { data: 'expires_at' },
            { data: 'reads_count' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterType, #filterAudience, #filterStatus').on('change', () => table.ajax.reload());
</script>
@endpush
