@extends('layouts.app')

@section('title', 'Study Materials')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Study Materials',
        'subtitle' => 'PDF, DOC, PPT, ZIP, images and videos',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.study-materials.export', 'module' => 'study-materials'])->render()
            .(auth()->user()->can('study-materials.create')
                ? '<a href="'.route('admin.study-materials.create').'" class="btn btn-primary"><i class="bi bi-cloud-arrow-up me-1"></i> Upload Material</a>'
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
                        @foreach (\App\Models\StudyMaterial::FILE_TYPES as $type)
                            <option value="{{ $type }}">{{ strtoupper($type) }}</option>
                        @endforeach
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
                    <select id="filterStatus" class="form-select">
                        <option value="">All statuses</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="materialsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Material</th><th>Subject</th><th>Type</th><th>Size</th>
                            <th>Downloads</th><th>Uploaded By</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const table = $('#materialsTable').DataTable({
        ajax: {
            url: '{{ route('admin.study-materials.index') }}',
            data: (d) => {
                d.search = $('#filterSearch').val();
                d.file_type = $('#filterType').val();
                d.subject_id = $('#filterSubject').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'title' },
            { data: 'subject', orderable: false },
            { data: 'file_type' },
            { data: 'size' },
            { data: 'downloads' },
            { data: 'uploader', orderable: false },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterType, #filterSubject, #filterStatus').on('change', () => table.ajax.reload());
</script>
@endpush
