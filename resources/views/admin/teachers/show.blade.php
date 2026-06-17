@extends('layouts.app')

@section('title', $teacher->user?->name)

@section('content')
    @include('admin.partials.page-header', [
        'title' => $teacher->user?->name,
        'subtitle' => 'Teacher profile',
        'actions' => auth()->user()->can('teachers.edit')
            ? '<a href="'.route('admin.teachers.edit', $teacher).'" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Edit</a>'
            : '',
    ])

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ $teacher->user?->avatar_url }}" class="avatar-lg mb-3" alt="">
                    <h2 class="h5 fw-bold mb-1">{{ $teacher->user?->name }}</h2>
                    <div class="mb-3">{!! status_badge($teacher->is_active) !!}</div>
                    <hr>
                    <dl class="row text-start small mb-0">
                        <dt class="col-5">Email</dt><dd class="col-7">{{ $teacher->user?->email }}</dd>
                        <dt class="col-5">Mobile</dt><dd class="col-7">{{ $teacher->user?->mobile ?: '—' }}</dd>
                        <dt class="col-5">Qualification</dt><dd class="col-7">{{ $teacher->qualification ?: '—' }}</dd>
                        <dt class="col-5">Subject</dt><dd class="col-7">{{ $teacher->subject?->name ?: '—' }}</dd>
                        <dt class="col-5">Branch</dt><dd class="col-7">{{ $teacher->branch?->name ?: '—' }}</dd>
                        <dt class="col-5">Joined</dt><dd class="col-7">{{ $teacher->created_at->format('d M Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-collection me-1 text-primary"></i> Assigned Batches</h3>
                    @forelse ($teacher->batches as $batch)
                        <div class="d-flex align-items-center gap-3 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $batch->name }}</div>
                                <div class="text-muted small">{{ $batch->schoolClass?->name }}</div>
                            </div>
                            @if ($batch->start_time)
                                <span class="badge text-bg-primary-subtle">{{ substr($batch->start_time, 0, 5) }} – {{ substr((string) $batch->end_time, 0, 5) }}</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No batches assigned yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
