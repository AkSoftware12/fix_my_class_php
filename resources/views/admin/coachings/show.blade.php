@extends('layouts.app')

@section('title', $coaching->name)

@section('content')
    @include('admin.partials.page-header', [
        'title' => $coaching->name,
        'subtitle' => $coaching->city?->name.', '.$coaching->city?->state,
        'actions' => auth()->user()->can('coachings.edit')
            ? '<a href="'.route('admin.coachings.edit', $coaching).'" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Edit</a>'
            : '',
    ])

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    @if ($coaching->logo_url)
                        <img src="{{ $coaching->logo_url }}" class="avatar-lg mb-3" alt="">
                    @else
                        <div class="avatar-lg mx-auto mb-3 text-bg-primary-subtle" style="display:grid;place-items:center;font-size:2rem"><i class="bi bi-building"></i></div>
                    @endif
                    <h2 class="h5 fw-bold mb-1">{{ $coaching->name }}</h2>
                    <div class="mb-3">{!! status_badge($coaching->is_active) !!}</div>
                    <hr>
                    <dl class="row text-start small mb-0">
                        <dt class="col-5">Owner</dt><dd class="col-7">{{ $coaching->owner_name }}</dd>
                        <dt class="col-5">Email</dt><dd class="col-7">{{ $coaching->email }}</dd>
                        <dt class="col-5">Mobile</dt><dd class="col-7">{{ $coaching->mobile }}</dd>
                        <dt class="col-5">Address</dt><dd class="col-7">{{ $coaching->address ?: '—' }}</dd>
                        <dt class="col-5">Joined</dt><dd class="col-7">{{ $coaching->created_at->format('d M Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-3 mb-3">
                <div class="col-sm-4">
                    <div class="card text-center"><div class="card-body">
                        <div class="h3 fw-bold mb-0">{{ $coaching->branches_count }}</div>
                        <div class="text-muted small">Branches</div>
                    </div></div>
                </div>
                <div class="col-sm-4">
                    <div class="card text-center"><div class="card-body">
                        <div class="h3 fw-bold mb-0">{{ $coaching->teachers_count }}</div>
                        <div class="text-muted small">Teachers</div>
                    </div></div>
                </div>
                <div class="col-sm-4">
                    <div class="card text-center"><div class="card-body">
                        <div class="h3 fw-bold mb-0">{{ $coaching->students_count }}</div>
                        <div class="text-muted small">Students</div>
                    </div></div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-credit-card me-1 text-primary"></i> Subscription</h3>
                    @if ($coaching->activeSubscription)
                        <div class="d-flex flex-wrap gap-4">
                            <div>
                                <div class="text-muted small">Plan</div>
                                <div class="fw-semibold">{{ $coaching->activeSubscription->plan?->name }}</div>
                            </div>
                            <div>
                                <div class="text-muted small">Valid until</div>
                                <div class="fw-semibold">{{ $coaching->activeSubscription->ends_at->format('d M Y') }}</div>
                            </div>
                            <div>
                                <div class="text-muted small">Status</div>
                                <span class="badge text-bg-success-subtle">Active</span>
                            </div>
                        </div>
                    @else
                        <p class="text-muted small mb-0">No active subscription.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
