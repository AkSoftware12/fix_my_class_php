@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Subscription Management',
        'subtitle' => 'Coaching plans and billing periods',
        'actions' => view('admin.partials.export-dropdown', ['route' => 'admin.subscriptions.export', 'module' => 'subscriptions'])->render()
            .(auth()->user()->can('subscriptions.view')
                ? '<a href="'.route('admin.subscription-plans.index').'" class="btn btn-outline-primary"><i class="bi bi-box me-1"></i> Plans</a>'
                : '')
            .(auth()->user()->can('subscriptions.create')
                ? '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#subscriptionModal" onclick="resetSubscriptionForm()"><i class="bi bi-plus-lg me-1"></i> Add Subscription</button>'
                : ''),
    ])

    <div class="card">
        <div class="card-body">
            <div class="row g-2 mb-3">
                @if(!$singleCoachingId)
                <div class="col-md-3">
                    <select id="filterCoaching" class="form-select select2">
                        <option value="">All coachings</option>
                        @foreach ($coachings as $coaching)
                            <option value="{{ $coaching->id }}">{{ $coaching->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" id="filterCoaching" value="{{ $singleCoachingId }}">
                @endif
                <div class="col-md-3">
                    <select id="filterPlan" class="form-select select2">
                        <option value="">All plans</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterStatus" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (\App\Models\Subscription::STATUSES as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover fmc-datatable" id="subscriptionsTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th><th>Coaching</th><th>Plan</th><th>Period</th><th>Amount</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="subscriptionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" id="subscriptionForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="subscriptionModalTitle">Add Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="subscriptionId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Coaching</label>
                            @if($singleCoachingId)
                                <input type="text" class="form-control" value="{{ $coachings->first()->name }}" readonly>
                                <input type="hidden" id="subCoaching" name="coaching_id" value="{{ $singleCoachingId }}">
                            @else
                            <select class="form-select select2" id="subCoaching" name="coaching_id" required>
                                <option value="">Select coaching…</option>
                                @foreach ($coachings as $coaching)
                                    <option value="{{ $coaching->id }}">{{ $coaching->name }}</option>
                                @endforeach
                            </select>
                            @endif
                            <div class="invalid-feedback" data-field="coaching_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Plan</label>
                            <select class="form-select select2" id="subPlan" name="subscription_plan_id" required>
                                <option value="">Select plan…</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}" data-price="{{ $plan->price }}">{{ $plan->name }} — {{ number_format((float) $plan->price, 2) }}/{{ $plan->billing_cycle }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-field="subscription_plan_id"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Starts At</label>
                            <input type="date" class="form-control" id="subStarts" name="starts_at" required>
                            <div class="invalid-feedback" data-field="starts_at"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Ends At</label>
                            <input type="date" class="form-control" id="subEnds" name="ends_at" required>
                            <div class="invalid-feedback" data-field="ends_at"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Amount Paid</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="subAmount" name="amount_paid" required>
                            <div class="invalid-feedback" data-field="amount_paid"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Status</label>
                            <select class="form-select" id="subStatus" name="status" required>
                                @foreach (\App\Models\Subscription::STATUSES as $status)
                                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-field="status"></div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Payment Reference</label>
                            <input type="text" class="form-control" id="subRef" name="payment_reference" maxlength="120">
                            <div class="invalid-feedback" data-field="payment_reference"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="subNotes" name="notes" rows="2" maxlength="2000"></textarea>
                            <div class="invalid-feedback" data-field="notes"></div>
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
    const table = $('#subscriptionsTable').DataTable({
        ajax: {
            url: '{{ route('admin.subscriptions.index') }}',
            data: (d) => {
                d.coaching_id = $('#filterCoaching').val();
                d.plan_id = $('#filterPlan').val();
                d.status = $('#filterStatus').val();
            },
        },
        columns: [
            { data: 'id' },
            { data: 'coaching', orderable: false },
            { data: 'plan', orderable: false },
            { data: 'period' },
            { data: 'amount_paid' },
            { data: 'status' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-end' },
        ],
    });

    $('#filterCoaching, #filterPlan, #filterStatus').on('change', () => table.ajax.reload());

    $('#subPlan').on('change', function () {
        const price = $(this).find(':selected').data('price');
        if (price !== undefined && !$('#subscriptionId').val()) $('#subAmount').val(price);
    });

    const singleCoachingId = {{ $singleCoachingId ?? 'null' }};

    function resetSubscriptionForm() {
        $('#subscriptionForm')[0].reset();
        $('#subscriptionId').val('');
        if (!singleCoachingId) $('#subCoaching').val('').trigger('change');
        $('#subPlan').val('').trigger('change');
        $('#subscriptionModalTitle').text('Add Subscription');
        $('#subscriptionForm .is-invalid').removeClass('is-invalid');
    }

    function editSubscription(id) {
        $.get(`{{ url('admin/subscriptions') }}/${id}/edit`).done((res) => {
            resetSubscriptionForm();
            const s = res.subscription;
            $('#subscriptionId').val(s.id);
            $('#subCoaching').val(s.coaching_id).trigger('change');
            $('#subPlan').val(s.subscription_plan_id).trigger('change');
            $('#subStarts').val(s.starts_at?.substring(0, 10));
            $('#subEnds').val(s.ends_at?.substring(0, 10));
            $('#subAmount').val(s.amount_paid);
            $('#subStatus').val(s.status);
            $('#subRef').val(s.payment_reference);
            $('#subNotes').val(s.notes);
            $('#subscriptionModalTitle').text('Edit Subscription');
            new bootstrap.Modal('#subscriptionModal').show();
        });
    }

    $('#subscriptionForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#subscriptionId').val();
        const url = id ? `{{ url('admin/subscriptions') }}/${id}` : '{{ route('admin.subscriptions.store') }}';
        const payload = {
            coaching_id: $('#subCoaching').val(),
            subscription_plan_id: $('#subPlan').val(),
            starts_at: $('#subStarts').val(),
            ends_at: $('#subEnds').val(),
            amount_paid: $('#subAmount').val(),
            status: $('#subStatus').val(),
            payment_reference: $('#subRef').val(),
            notes: $('#subNotes').val(),
            _method: id ? 'PUT' : 'POST',
        };

        $(this).find('.is-invalid').removeClass('is-invalid');

        $.post(url, payload)
            .done((res) => {
                bootstrap.Modal.getInstance(document.getElementById('subscriptionModal')).hide();
                fmcToast(res.message);
                table.ajax.reload(null, false);
            })
            .fail((xhr) => {
                if (xhr.status === 422) {
                    Object.entries(xhr.responseJSON.errors || {}).forEach(([field, msgs]) => {
                        $(`#subscriptionForm [name="${field}"]`).addClass('is-invalid');
                        $(`#subscriptionForm [data-field="${field}"]`).text(msgs[0]);
                    });
                } else {
                    fmcToast(xhr.responseJSON?.message || 'Save failed.', 'error');
                }
            });
    });
</script>
@endpush
