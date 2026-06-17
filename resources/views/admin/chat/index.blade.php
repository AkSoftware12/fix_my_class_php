@extends('layouts.app')

@section('title', 'Chat Monitoring')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Chat Monitoring',
        'subtitle' => 'Rooms, messages and user activity',
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search rooms…">
                </div>
                <div class="col-md-2">
                    <select id="filterType" class="form-select">
                        <option value="">All types</option>
                        <option value="group">Group</option>
                        <option value="direct">Direct</option>
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
                <table class="table table-hover fmc-datatable" id="roomsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Room</th><th>Type</th><th>Batch</th>
                            <th>Members</th><th>Messages</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#roomsTable').DataTable({
        ajax: {
            url: '{{ route('admin.chat-rooms.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.type = $('#filterType').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'type' },
            { data: 'batch', orderable: false },
            { data: 'members_count' },
            { data: 'messages_count' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterType, #filterStatus').on('change', () => table.ajax.reload());
</script>
@endpush
