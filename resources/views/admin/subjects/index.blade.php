@extends('layouts.app')

@section('title', 'Subjects')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Subject Management',
        'subtitle' => 'Subjects taught at your coaching',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.subjects.export', 'module' => 'subjects'])->render()
            .(auth()->user()->can('subjects.create')
                ? '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#subjectModal" onclick="resetSubjectForm()"><i class="bi bi-plus-lg me-1"></i> Add Subject</button>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search name or code…">
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
                <table class="table table-hover fmc-datatable" id="subjectsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Subject</th><th>Code</th><th>Coaching</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="subjectModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="subjectForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="subjectModalTitle">Add Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="subjectId">
                    @if ($coachings->isNotEmpty())
                    <div class="mb-3">
                        <label class="form-label required">Coaching</label>
                        <select class="form-select select2" id="subjectCoaching" name="coaching_id">
                            <option value="">Select coaching…</option>
                            @foreach ($coachings as $coaching)
                                <option value="{{ $coaching->id }}">{{ $coaching->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" data-field="coaching_id"></div>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label required">Subject Name</label>
                        <input type="text" class="form-control" id="subjectName" name="name" maxlength="120" required>
                        <div class="invalid-feedback" data-field="name"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" class="form-control" id="subjectCode" name="code" maxlength="40">
                        <div class="invalid-feedback" data-field="code"></div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="subjectActive" name="is_active" value="1" checked>
                        <label class="form-check-label" for="subjectActive">Active</label>
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
    const table = $('#subjectsTable').DataTable({
        ajax: {
            url: '{{ route('admin.subjects.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'code' },
            { data: 'coaching', orderable: false },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterStatus').on('change', () => table.ajax.reload());

    function resetSubjectForm() {
        $('#subjectForm')[0].reset();
        $('#subjectId').val('');
        $('#subjectModalTitle').text('Add Subject');
        $('#subjectForm .is-invalid').removeClass('is-invalid');
        if ($('#subjectCoaching').length) $('#subjectCoaching').val('').trigger('change');
    }

    function editSubject(id) {
        $.get(`{{ url('admin/subjects') }}/${id}/edit`).done((res) => {
            resetSubjectForm();
            const s = res.subject;
            $('#subjectId').val(s.id);
            $('#subjectName').val(s.name);
            $('#subjectCode').val(s.code);
            $('#subjectActive').prop('checked', !!s.is_active);
            if ($('#subjectCoaching').length) $('#subjectCoaching').val(s.coaching_id || '').trigger('change');
            $('#subjectModalTitle').text('Edit Subject');
            new bootstrap.Modal('#subjectModal').show();
        });
    }

    $('#subjectForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#subjectId').val();
        const url = id ? `{{ url('admin/subjects') }}/${id}` : '{{ route('admin.subjects.store') }}';
        const payload = {
            name: $('#subjectName').val(),
            code: $('#subjectCode').val(),
            is_active: $('#subjectActive').is(':checked') ? 1 : 0,
            _method: id ? 'PUT' : 'POST',
        };
        if ($('#subjectCoaching').length && $('#subjectCoaching').val()) {
            payload.coaching_id = $('#subjectCoaching').val();
        }

        $(this).find('.is-invalid').removeClass('is-invalid');

        $.post(url, payload)
            .done((res) => {
                bootstrap.Modal.getInstance(document.getElementById('subjectModal')).hide();
                fmcToast(res.message);
                table.ajax.reload(null, false);
            })
            .fail((xhr) => {
                if (xhr.status === 422) {
                    Object.entries(xhr.responseJSON.errors || {}).forEach(([field, msgs]) => {
                        $(`#subjectForm [name="${field}"]`).addClass('is-invalid');
                        $(`#subjectForm [data-field="${field}"]`).text(msgs[0]);
                    });
                } else {
                    fmcToast(xhr.responseJSON?.message || 'Save failed.', 'error');
                }
            });
    });
</script>
@endpush
