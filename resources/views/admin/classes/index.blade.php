@extends('layouts.app')

@section('title', 'Classes')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Class Management',
        'subtitle' => 'Academic classes (e.g. Class 9, Class 10)',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.classes.export', 'module' => 'classes'])->render()
            .(auth()->user()->can('classes.create')
                ? '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#classModal" onclick="resetClassForm()"><i class="bi bi-plus-lg me-1"></i> Add Class</button>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search classes…">
                </div>
                <div class="col-md-3">
                    <select id="filterBranch" class="form-select select2">
                        <option value="">All branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
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
                <table class="table table-hover fmc-datatable" id="classesTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Class</th><th>Branch</th><th>Batches</th><th>Students</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="classModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="classForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="classModalTitle">Add Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="classId">
                    <div class="mb-3">
                        <label class="form-label required">Class Name</label>
                        <input type="text" class="form-control" id="className" name="name" maxlength="120" required>
                        <div class="invalid-feedback" data-field="name"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Branch <span class="text-muted">(blank = all branches)</span></label>
                        <select class="form-select select2" id="classBranch" name="branch_id">
                            <option value="">All branches</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" data-field="branch_id"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="classDescription" name="description" rows="2" maxlength="1000"></textarea>
                        <div class="invalid-feedback" data-field="description"></div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="classActive" name="is_active" value="1" checked>
                        <label class="form-check-label" for="classActive">Active</label>
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
    const table = $('#classesTable').DataTable({
        ajax: {
            url: '{{ route('admin.classes.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.branch_id = $('#filterBranch').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'branch', orderable: false },
            { data: 'batches_count' },
            { data: 'students_count' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterBranch, #filterStatus').on('change', () => table.ajax.reload());

    function resetClassForm() {
        $('#classForm')[0].reset();
        $('#classId').val('');
        $('#classBranch').val('').trigger('change');
        $('#classModalTitle').text('Add Class');
        $('#classForm .is-invalid').removeClass('is-invalid');
    }

    function editClass(id) {
        $.get(`{{ url('admin/classes') }}/${id}/edit`).done((res) => {
            resetClassForm();
            const c = res.class;
            $('#classId').val(c.id);
            $('#className').val(c.name);
            $('#classBranch').val(c.branch_id || '').trigger('change');
            $('#classDescription').val(c.description);
            $('#classActive').prop('checked', !!c.is_active);
            $('#classModalTitle').text('Edit Class');
            new bootstrap.Modal('#classModal').show();
        });
    }

    $('#classForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#classId').val();
        const url = id ? `{{ url('admin/classes') }}/${id}` : '{{ route('admin.classes.store') }}';
        const payload = {
            name: $('#className').val(),
            branch_id: $('#classBranch').val() || null,
            description: $('#classDescription').val(),
            is_active: $('#classActive').is(':checked') ? 1 : 0,
            _method: id ? 'PUT' : 'POST',
        };

        $(this).find('.is-invalid').removeClass('is-invalid');

        $.post(url, payload)
            .done((res) => {
                bootstrap.Modal.getInstance(document.getElementById('classModal')).hide();
                fmcToast(res.message);
                table.ajax.reload(null, false);
            })
            .fail((xhr) => {
                if (xhr.status === 422) {
                    Object.entries(xhr.responseJSON.errors || {}).forEach(([field, msgs]) => {
                        $(`#classForm [name="${field}"]`).addClass('is-invalid');
                        $(`#classForm [data-field="${field}"]`).text(msgs[0]);
                    });
                } else {
                    fmcToast(xhr.responseJSON?.message || 'Save failed.', 'error');
                }
            });
    });
</script>
@endpush
