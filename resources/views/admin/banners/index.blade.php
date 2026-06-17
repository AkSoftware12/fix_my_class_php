@extends('layouts.app')

@section('title', 'Banners')

@section('content')
    @include('admin.partials.page-header', [
        'title'    => 'Banner Management',
        'subtitle' => 'Promotional banners for coaching centres',
        'actions'  => auth()->user()->can('banners.create')
            ? '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bannerModal" onclick="resetBannerForm()"><i class="bi bi-plus-lg me-1"></i> Add Banner</button>'
            : '',
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search title…">
                </div>
                @if($coachings->count() > 1)
                <div class="col-md-3">
                    <select id="filterCoaching" class="form-select select2">
                        <option value="">All coachings</option>
                        @foreach($coachings as $coaching)
                            <option value="{{ $coaching->id }}">{{ $coaching->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-2">
                    <select id="filterStatus" class="form-select">
                        <option value="">All statuses</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="bannersTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Image</th><th>Title</th><th>Coaching</th><th>Period</th><th>Order</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Create / Edit Modal --}}
    <div class="modal fade" id="bannerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" id="bannerForm" enctype="multipart/form-data" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="bannerModalTitle">Add Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="bannerId">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required">Title</label>
                            <input type="text" class="form-control" id="bannerTitle" name="title" maxlength="180" required>
                            <div class="invalid-feedback" data-field="title"></div>
                        </div>

                        @if($coachings->count() > 1)
                        <div class="col-md-6">
                            <label class="form-label">Coaching Centre</label>
                            <select class="form-select select2" id="bannerCoaching" name="coaching_id">
                                <option value="">— Global (all) —</option>
                                @foreach($coachings as $coaching)
                                    <option value="{{ $coaching->id }}">{{ $coaching->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-field="coaching_id"></div>
                        </div>
                        @else
                            <input type="hidden" name="coaching_id" value="{{ $coachings->first()?->id }}">
                        @endif

                        <div class="col-md-{{ $coachings->count() > 1 ? '6' : '12' }}">
                            <label class="form-label">Click URL <small class="text-muted">(optional)</small></label>
                            <input type="url" class="form-control" id="bannerUrl" name="url" maxlength="500" placeholder="https://…">
                            <div class="invalid-feedback" data-field="url"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label required" id="bannerImageLabel">Banner Image <small class="text-muted">(jpg/png/webp, max 2MB)</small></label>
                            <input type="file" class="form-control" id="bannerImage" name="image" accept="image/jpg,image/jpeg,image/png,image/webp">
                            <div id="bannerImagePreview" class="mt-2" style="display:none">
                                <img id="bannerPreviewImg" src="" style="max-height:120px;border-radius:6px;" alt="Preview">
                            </div>
                            <div class="invalid-feedback" data-field="image"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Starts At</label>
                            <input type="date" class="form-control" id="bannerStartsAt" name="starts_at">
                            <div class="invalid-feedback" data-field="starts_at"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ends At</label>
                            <input type="date" class="form-control" id="bannerEndsAt" name="ends_at">
                            <div class="invalid-feedback" data-field="ends_at"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="bannerSortOrder" name="sort_order" min="0" max="9999" value="0">
                            <div class="invalid-feedback" data-field="sort_order"></div>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="bannerActive" name="is_active" value="1" checked>
                                <label class="form-check-label" for="bannerActive">Active</label>
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
    const table = $('#bannersTable').DataTable({
        ajax: {
            url: '{{ route('admin.banners.index') }}',
            data: (d) => {
                d.search      = $('#filterSearch').val();
                d.coaching_id = $('#filterCoaching').val() || '';
                d.status      = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'image', orderable: false, searchable: false },
            { data: 'title' },
            { data: 'coaching', orderable: false },
            { data: 'period', orderable: false },
            { data: 'sort_order' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }

    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));
    $('#filterCoaching, #filterStatus').on('change', () => table.ajax.reload());

    // Image preview
    $('#bannerImage').on('change', function () {
        const file = this.files[0];
        if (!file) { $('#bannerImagePreview').hide(); return; }
        const reader = new FileReader();
        reader.onload = e => {
            $('#bannerPreviewImg').attr('src', e.target.result);
            $('#bannerImagePreview').show();
        };
        reader.readAsDataURL(file);
    });

    function resetBannerForm() {
        $('#bannerForm')[0].reset();
        $('#bannerId').val('');
        $('#bannerModalTitle').text('Add Banner');
        $('#bannerImageLabel').html('Banner Image <small class="text-muted">(jpg/png/webp, max 2MB)</small>');
        $('#bannerImagePreview').hide();
        $('#bannerImage').prop('required', true);
        $('#bannerForm .is-invalid').removeClass('is-invalid');
        $('#bannerCoaching').val('').trigger('change');
    }

    function editBanner(id) {
        $.get(`{{ url('admin/banners') }}/${id}/edit`).done((res) => {
            resetBannerForm();
            const b = res.banner;
            $('#bannerId').val(b.id);
            $('#bannerTitle').val(b.title);
            $('#bannerCoaching').val(b.coaching_id).trigger('change');
            $('#bannerUrl').val(b.url);
            $('#bannerStartsAt').val(b.starts_at?.substring(0, 10) ?? '');
            $('#bannerEndsAt').val(b.ends_at?.substring(0, 10) ?? '');
            $('#bannerSortOrder').val(b.sort_order);
            $('#bannerActive').prop('checked', !!b.is_active);
            $('#bannerImage').prop('required', false);
            $('#bannerImageLabel').html('Banner Image <small class="text-muted">(leave blank to keep current)</small>');
            if (b.image_url) {
                $('#bannerPreviewImg').attr('src', b.image_url);
                $('#bannerImagePreview').show();
            }
            $('#bannerModalTitle').text('Edit Banner');
            new bootstrap.Modal('#bannerModal').show();
        });
    }

    $('#bannerForm').on('submit', function (e) {
        e.preventDefault();
        const id  = $('#bannerId').val();
        const url = id ? `{{ url('admin/banners') }}/${id}` : '{{ route('admin.banners.store') }}';

        const fd = new FormData(this);
        if (id) fd.append('_method', 'PUT');

        $(this).find('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url, type: 'POST', data: fd,
            processData: false, contentType: false,
        })
        .done((res) => {
            bootstrap.Modal.getInstance(document.getElementById('bannerModal')).hide();
            fmcToast(res.message);
            table.ajax.reload(null, false);
        })
        .fail((xhr) => {
            if (xhr.status === 422) {
                Object.entries(xhr.responseJSON.errors || {}).forEach(([field, msgs]) => {
                    $(`#bannerForm [name="${field}"]`).addClass('is-invalid');
                    $(`#bannerForm [data-field="${field}"]`).text(msgs[0]);
                });
            } else {
                fmcToast(xhr.responseJSON?.message || 'Save failed.', 'error');
            }
        });
    });
</script>
@endpush
