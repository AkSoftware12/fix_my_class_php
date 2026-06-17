@extends('layouts.app')

@section('title', 'Branches')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Branch Management',
        'subtitle' => 'Coaching centre branches',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.branches.export', 'module' => 'branches'])->render()
            .(auth()->user()->can('branches.create')
                ? '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#branchModal" onclick="resetBranchForm()"><i class="bi bi-plus-lg me-1"></i> Add Branch</button>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search name or code…">
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
                <table class="table table-hover fmc-datatable" id="branchesTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Branch</th><th>Code</th><th>Coaching</th><th>Contact</th>
                            <th>Students</th><th>Teachers</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="branchModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" id="branchForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="branchModalTitle">Add Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="branchId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Coaching</label>
                            <select class="form-select select2" id="branchCoaching" name="coaching_id" required>
                                <option value="">Select coaching…</option>
                                @foreach ($coachings as $coaching)
                                    <option value="{{ $coaching->id }}">{{ $coaching->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-field="coaching_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Branch Name</label>
                            <input type="text" class="form-control" id="branchName" name="name" maxlength="180" required>
                            <div class="invalid-feedback" data-field="name"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Branch Code</label>
                            <input type="text" class="form-control" id="branchCode" name="code" maxlength="40" required>
                            <div class="invalid-feedback" data-field="code"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="branchContact" name="contact_number" maxlength="20">
                            <div class="invalid-feedback" data-field="contact_number"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" id="branchAddress" name="address" rows="2" maxlength="1000"></textarea>
                            <div class="invalid-feedback" data-field="address"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Location <span class="text-muted small fw-normal">(for nearby search)</span></label>
                            <div class="row g-2 align-items-end">
                                <div class="col-5">
                                    <label class="form-label small text-muted mb-1">Latitude</label>
                                    <input type="number" class="form-control" id="branchLatitude" name="latitude" step="any" min="-90" max="90" placeholder="e.g. 30.3165">
                                    <div class="invalid-feedback" data-field="latitude"></div>
                                </div>
                                <div class="col-5">
                                    <label class="form-label small text-muted mb-1">Longitude</label>
                                    <input type="number" class="form-control" id="branchLongitude" name="longitude" step="any" min="-180" max="180" placeholder="e.g. 78.0322">
                                    <div class="invalid-feedback" data-field="longitude"></div>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-outline-primary w-100"
                                            onclick="fmcOpenMapPicker('#branchLatitude', '#branchLongitude')">
                                        <i class="bi bi-map"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="branchActive" name="is_active" value="1" checked>
                                <label class="form-check-label" for="branchActive">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#branchesTable').DataTable({
        ajax: {
            url: '{{ route('admin.branches.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.coaching_id = $('#filterCoaching').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'code' },
            { data: 'coaching', orderable: false },
            { data: 'contact_number' },
            { data: 'students_count' },
            { data: 'teachers_count' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterCoaching, #filterStatus').on('change', () => table.ajax.reload());

    function resetBranchForm() {
        $('#branchForm')[0].reset();
        $('#branchId').val('');
        $('#branchCoaching').val('').trigger('change');
        $('#branchModalTitle').text('Add Branch');
        $('#branchForm .is-invalid').removeClass('is-invalid');
    }

    function editBranch(id) {
        $.get(`{{ url('admin/branches') }}/${id}/edit`).done((res) => {
            resetBranchForm();
            const b = res.branch;
            $('#branchId').val(b.id);
            $('#branchCoaching').val(b.coaching_id).trigger('change');
            $('#branchName').val(b.name);
            $('#branchCode').val(b.code);
            $('#branchContact').val(b.contact_number);
            $('#branchAddress').val(b.address);
            $('#branchLatitude').val(b.latitude ?? '');
            $('#branchLongitude').val(b.longitude ?? '');
            $('#branchActive').prop('checked', !!b.is_active);
            $('#branchModalTitle').text('Edit Branch');
            new bootstrap.Modal('#branchModal').show();
        });
    }

    $('#branchForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#branchId').val();
        const url = id ? `{{ url('admin/branches') }}/${id}` : '{{ route('admin.branches.store') }}';
        const payload = {
            coaching_id: $('#branchCoaching').val(),
            name: $('#branchName').val(),
            code: $('#branchCode').val(),
            contact_number: $('#branchContact').val(),
            address: $('#branchAddress').val(),
            latitude: $('#branchLatitude').val() || null,
            longitude: $('#branchLongitude').val() || null,
            is_active: $('#branchActive').is(':checked') ? 1 : 0,
            _method: id ? 'PUT' : 'POST',
        };

        $(this).find('.is-invalid').removeClass('is-invalid');

        $.post(url, payload)
            .done((res) => {
                bootstrap.Modal.getInstance(document.getElementById('branchModal')).hide();
                fmcToast(res.message);
                table.ajax.reload(null, false);
            })
            .fail((xhr) => {
                if (xhr.status === 422) {
                    Object.entries(xhr.responseJSON.errors || {}).forEach(([field, msgs]) => {
                        $(`#branchForm [name="${field}"]`).addClass('is-invalid');
                        $(`#branchForm [data-field="${field}"]`).text(msgs[0]);
                    });
                } else {
                    fmcToast(xhr.responseJSON?.message || 'Save failed.', 'error');
                }
            });
    });
</script>
@endpush
