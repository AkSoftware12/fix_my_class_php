@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Role Management',
        'subtitle' => 'Roles and their permission sets',
        'actions' => auth()->user()->can('roles.create')
            ? '<a href="'.route('admin.roles.create').'" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Role</a>'
            : '',
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search roles…">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="rolesTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Role</th><th>Users</th><th>Permissions</th><th>Type</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#rolesTable').DataTable({
        ajax: {
            url: '{{ route('admin.roles.index') }}',
            data: (d) => { d.search = $('#filterSearch').val(); },
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'users_count' },
            { data: 'permissions_count' },
            { data: 'protected', orderable: false },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
</script>
@endpush
