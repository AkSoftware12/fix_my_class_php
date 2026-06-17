@extends('layouts.app')

@section('title', 'Lead: '.$lead->student_name)

@section('content')
    @include('admin.partials.page-header', [
        'title' => $lead->student_name,
        'subtitle' => 'Lead since '.$lead->created_at->format('d M Y'),
        'actions' => auth()->user()->can('leads.edit')
            ? '<a href="'.route('admin.leads.edit', $lead).'" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Edit</a>'
            : '',
    ])

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="h6 fw-bold mb-0"><i class="bi bi-person me-1 text-primary"></i> Lead Details</h3>
                        @include('admin.leads.partials.stage', ['stage' => $lead->stage])
                    </div>
                    <dl class="row small mb-0">
                        <dt class="col-5">Guardian</dt><dd class="col-7">{{ $lead->guardian_name ?: '—' }}</dd>
                        <dt class="col-5">Mobile</dt><dd class="col-7">{{ $lead->mobile }}</dd>
                        <dt class="col-5">Email</dt><dd class="col-7">{{ $lead->email ?: '—' }}</dd>
                        <dt class="col-5">Class</dt><dd class="col-7">{{ $lead->interested_class ?: '—' }}</dd>
                        <dt class="col-5">Source</dt><dd class="col-7">{{ $lead->source ?: '—' }}</dd>
                        <dt class="col-5">Branch</dt><dd class="col-7">{{ $lead->branch?->name ?: '—' }}</dd>
                        <dt class="col-5">Assigned To</dt><dd class="col-7">{{ $lead->assignee?->name ?: 'Unassigned' }}</dd>
                        <dt class="col-5">Next Follow-up</dt><dd class="col-7">{{ $lead->next_follow_up_at?->format('d M Y') ?: '—' }}</dd>
                    </dl>
                    @if ($lead->notes)
                        <hr>
                        <h4 class="small fw-bold text-muted text-uppercase">Notes</h4>
                        <div class="small">{!! nl2br(e($lead->notes)) !!}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            @can('leads.edit')
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="h6 fw-bold mb-3"><i class="bi bi-plus-circle me-1 text-primary"></i> Record Follow-up</h3>
                        <form id="followUpForm" class="row g-2" novalidate>
                            <div class="col-md-3">
                                <select id="fuStage" class="form-select" required>
                                    @foreach (\App\Models\AdmissionLead::STAGES as $stage => $label)
                                        <option value="{{ $stage }}" @selected($lead->stage === $stage)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="fuRemarks" class="form-control" placeholder="Remarks…" maxlength="1000" required>
                            </div>
                            <div class="col-md-3">
                                <input type="date" id="fuNext" class="form-control" min="{{ today()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-clock-history me-1 text-primary"></i> Follow-up History</h3>
                    @forelse ($lead->followUps as $followUp)
                        <div class="d-flex gap-3 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                            <div>@include('admin.leads.partials.stage', ['stage' => $followUp->stage])</div>
                            <div class="flex-grow-1">
                                <div class="small">{{ $followUp->remarks }}</div>
                                <div class="text-muted" style="font-size:.72rem">
                                    {{ $followUp->user?->name }} · {{ $followUp->followed_up_at->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No follow-ups recorded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $('#followUpForm').on('submit', function (e) {
        e.preventDefault();
        $.post('{{ route('admin.leads.follow-up', $lead) }}', {
            stage: $('#fuStage').val(),
            remarks: $('#fuRemarks').val(),
            next_follow_up_at: $('#fuNext').val() || null,
        })
        .done((res) => {
            fmcToast(res.message);
            setTimeout(() => location.reload(), 800);
        })
        .fail((xhr) => {
            const errors = xhr.responseJSON?.errors;
            fmcToast(errors ? Object.values(errors)[0][0] : (xhr.responseJSON?.message || 'Failed.'), 'error');
        });
    });
</script>
@endpush
