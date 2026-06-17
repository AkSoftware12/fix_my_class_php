@extends('layouts.app')

@section('title', 'Users')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'User Management',
        'subtitle' => 'Platform users across all roles',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.users.export', 'module' => 'users'])->render()
            .(auth()->user()->can('users.create')
                ? '<a href="'.route('admin.users.create').'" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add User</a>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search name, email, mobile…">
                </div>
                <div class="col-md-2">
                    <select id="filterRole" class="form-select">
                        <option value="">All roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterCoaching" class="form-select select2">
                        <option value="">All coachings</option>
                        @foreach ($coachings as $coaching)
                            <option value="{{ $coaching->id }}">{{ $coaching->name }}</option>
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
                <table class="table table-hover fmc-datatable" id="usersTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>User</th><th>Role</th><th>Coaching</th><th>Branch</th>
                            <th>Last Login</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#usersTable').DataTable({
        ajax: {
            url: '{{ route('admin.users.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.role = $('#filterRole').val();
                d.coaching_id = $('#filterCoaching').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'user' },
            { data: 'role', orderable: false },
            { data: 'coaching', orderable: false },
            { data: 'branch', orderable: false },
            { data: 'last_login' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterRole, #filterCoaching, #filterStatus').on('change', () => table.ajax.reload());

    function resetUserPassword(id, name) {
        Swal.fire({
            title: `Reset password for ${name}?`,
            text: 'A new random password will be generated.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Reset',
        }).then((r) => {
            if (!r.isConfirmed) return;
            $.post(`{{ url('admin/users') }}/${id}/reset-password`)
                .done((res) => {
                    if (res.password) {
                        Swal.fire({
                            title: 'Password reset',
                            html: `New password: <code class="user-select-all">${res.password}</code><br><small>Copy it now — it will not be shown again.</small>`,
                            icon: 'success',
                        });
                    } else {
                        fmcToast(res.message);
                    }
                })
                .fail((xhr) => fmcToast(xhr.responseJSON?.message || 'Reset failed.', 'error'));
        });
    }
</script>
@endpush
