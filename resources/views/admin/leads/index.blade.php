@extends('layouts.app')

@section('title', 'Admission Leads')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Admission Leads (CRM)',
        'subtitle' => 'Track enquiries from first contact to admission',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.leads.export', 'module' => 'leads'])->render()
            .(auth()->user()->can('leads.create')
                ? '<a href="'.route('admin.leads.create').'" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Lead</a>'
                : ''),
    ])

    {{-- Pipeline summary --}}
    <div class="row g-3 mb-3">
        @foreach (\App\Models\AdmissionLead::STAGES as $stage => $label)
            <div class="col">
                <div class="card text-center stage-filter" style="cursor:pointer" data-stage="{{ $stage }}">
                    <div class="card-body py-3">
                        <div class="h4 fw-bold mb-0">{{ $stageCounts[$stage] ?? 0 }}</div>
                        <div class="small text-muted">{{ $label }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search name, mobile, email…">
                </div>
                <div class="col-md-2">
                    <select id="filterStage" class="form-select">
                        <option value="">All stages</option>
                        @foreach ($stages as $stage => $label)
                            <option value="{{ $stage }}">{{ $label }}</option>
                        @endforeach
                    </select>
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
                    <select id="filterAssignee" class="form-select select2">
                        <option value="">All assignees</option>
                        @foreach ($staff as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="leadsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Student</th><th>Mobile</th><th>Class</th><th>Source</th>
                            <th>Stage</th><th>Assigned To</th><th>Next Follow-up</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#leadsTable').DataTable({
        ajax: {
            url: '{{ route('admin.leads.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.stage = $('#filterStage').val();
                d.branch_id = $('#filterBranch').val();
                d.assigned_to = $('#filterAssignee').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'student_name' },
            { data: 'mobile' },
            { data: 'interested_class' },
            { data: 'source' },
            { data: 'stage' },
            { data: 'assignee', orderable: false },
            { data: 'next_follow_up' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterStage, #filterBranch, #filterAssignee').on('change', () => table.ajax.reload());

    $('.stage-filter').on('click', function () {
        $('#filterStage').val($(this).data('stage')).trigger('change');
    });
</script>
@endpush
