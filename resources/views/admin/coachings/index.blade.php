@extends('layouts.app')

@section('title', 'Coaching Centres')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Coaching Management',
        'subtitle' => 'All coaching centres on the platform',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.coachings.export', 'module' => 'coachings'])->render()
            .(auth()->user()->can('coachings.create')
                ? '<a href="'.route('admin.coachings.create').'" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Coaching</a>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search name, owner, email…">
                </div>
                <div class="col-md-3">
                    <select id="filterCity" class="form-select select2">
                        <option value="">All cities</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}, {{ $city->state }}</option>
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
                <table class="table table-hover fmc-datatable" id="coachingsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Coaching</th><th>City</th><th>Owner</th><th>Contact</th>
                            <th>Branches</th><th>Students</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#coachingsTable').DataTable({
        ajax: {
            url: '{{ route('admin.coachings.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.city_id = $('#filterCity').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'coaching' },
            { data: 'city', orderable: false },
            { data: 'owner_name' },
            { data: 'contact' },
            { data: 'branches_count' },
            { data: 'students_count' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterCity, #filterStatus').on('change', () => table.ajax.reload());
</script>
@endpush
