@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Subscription Plans',
        'subtitle' => 'Pricing tiers for coaching centres',
        'actions' => '<a href="'.route('admin.subscriptions.index').'" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-1"></i> Subscriptions</a>'
            .(auth()->user()->can('subscriptions.create')
                ? ' <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#planModal" onclick="resetPlanForm()"><i class="bi bi-plus-lg me-1"></i> Add Plan</button>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" id="filterSearch" class="form-control" placeholder="Search plans…">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="plansTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Plan</th><th>Price</th><th>Billing</th><th>Limits</th><th>Subscribers</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="planModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" id="planForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="planModalTitle">Add Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="planId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Plan Name</label>
                            <input type="text" class="form-control" id="planName" name="name" maxlength="120" required>
                            <div class="invalid-feedback" data-field="name"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label required">Price</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="planPrice" name="price" required>
                            <div class="invalid-feedback" data-field="price"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label required">Billing Cycle</label>
                            <select class="form-select" id="planCycle" name="billing_cycle" required>
                                @foreach (\App\Models\SubscriptionPlan::BILLING_CYCLES as $cycle)
                                    <option value="{{ $cycle }}">{{ ucwords(str_replace('_', ' ', $cycle)) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-field="billing_cycle"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Max Branches</label>
                            <input type="number" min="1" class="form-control" id="planBranches" name="max_branches" required>
                            <div class="invalid-feedback" data-field="max_branches"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Max Teachers</label>
                            <input type="number" min="1" class="form-control" id="planTeachers" name="max_teachers" required>
                            <div class="invalid-feedback" data-field="max_teachers"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Max Students</label>
                            <input type="number" min="1" class="form-control" id="planStudents" name="max_students" required>
                            <div class="invalid-feedback" data-field="max_students"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Features <span class="text-muted">(one per line)</span></label>
                            <textarea class="form-control" id="planFeatures" name="features" rows="3"></textarea>
                            <div class="invalid-feedback" data-field="features"></div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="planActive" name="is_active" value="1" checked>
                                <label class="form-check-label" for="planActive">Active</label>
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
    const table = $('#plansTable').DataTable({
        ajax: {
            url: '{{ route('admin.subscription-plans.index') }}',
            data: (d) => { d.search = $('#filterSearch').val(); },
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'price' },
            { data: 'billing_cycle' },
            { data: 'limits', orderable: false },
            { data: 'subscriptions_count' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }
    $('#filterSearch').on('keyup', debounce(() => table.ajax.reload(), 400));

    function resetPlanForm() {
        $('#planForm')[0].reset();
        $('#planId').val('');
        $('#planModalTitle').text('Add Plan');
        $('#planForm .is-invalid').removeClass('is-invalid');
    }

    function editPlan(id) {
        $.get(`{{ url('admin/subscription-plans') }}/${id}/edit`).done((res) => {
            resetPlanForm();
            const p = res.plan;
            $('#planId').val(p.id);
            $('#planName').val(p.name);
            $('#planPrice').val(p.price);
            $('#planCycle').val(p.billing_cycle);
            $('#planBranches').val(p.max_branches);
            $('#planTeachers').val(p.max_teachers);
            $('#planStudents').val(p.max_students);
            $('#planFeatures').val((p.features || []).join('\n'));
            $('#planActive').prop('checked', !!p.is_active);
            $('#planModalTitle').text('Edit Plan');
            new bootstrap.Modal('#planModal').show();
        });
    }

    $('#planForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#planId').val();
        const url = id ? `{{ url('admin/subscription-plans') }}/${id}` : '{{ route('admin.subscription-plans.store') }}';
        const payload = {
            name: $('#planName').val(),
            price: $('#planPrice').val(),
            billing_cycle: $('#planCycle').val(),
            max_branches: $('#planBranches').val(),
            max_teachers: $('#planTeachers').val(),
            max_students: $('#planStudents').val(),
            features: $('#planFeatures').val(),
            is_active: $('#planActive').is(':checked') ? 1 : 0,
            _method: id ? 'PUT' : 'POST',
        };

        $(this).find('.is-invalid').removeClass('is-invalid');

        $.post(url, payload)
            .done((res) => {
                bootstrap.Modal.getInstance(document.getElementById('planModal')).hide();
                fmcToast(res.message);
                table.ajax.reload(null, false);
            })
            .fail((xhr) => {
                if (xhr.status === 422) {
                    Object.entries(xhr.responseJSON.errors || {}).forEach(([field, msgs]) => {
                        $(`#planForm [name="${field}"]`).addClass('is-invalid');
                        $(`#planForm [data-field="${field}"]`).text(msgs[0]);
                    });
                } else {
                    fmcToast(xhr.responseJSON?.message || 'Save failed.', 'error');
                }
            });
    });
</script>
@endpush
