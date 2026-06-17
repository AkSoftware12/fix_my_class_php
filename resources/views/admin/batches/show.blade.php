@extends('layouts.app')

@section('title', $batch->name)

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Batch: '.$batch->name,
        'subtitle' => ($batch->branch?->name ?? '').' · '.($batch->schoolClass?->name ?? ''),
        'actions' => auth()->user()->can('batches.edit')
            ? '<a href="'.route('admin.batches.edit', $batch).'" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Edit / Assign</a>'
            : '',
    ])

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-info-circle me-1 text-primary"></i> Details</h3>
                    <dl class="row small mb-0">
                        <dt class="col-5">Branch</dt><dd class="col-7">{{ $batch->branch?->name ?: '—' }}</dd>
                        <dt class="col-5">Class</dt><dd class="col-7">{{ $batch->schoolClass?->name ?: '—' }}</dd>
                        <dt class="col-5">Timing</dt>
                        <dd class="col-7">{{ $batch->start_time ? substr($batch->start_time, 0, 5).' – '.substr((string) $batch->end_time, 0, 5) : '—' }}</dd>
                        <dt class="col-5">Capacity</dt><dd class="col-7">{{ $batch->capacity ?: 'Unlimited' }}</dd>
                        <dt class="col-5">Status</dt><dd class="col-7">{!! status_badge($batch->is_active) !!}</dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-book me-1 text-primary"></i> Subjects ({{ $batch->subjects->count() }})</h3>
                    @forelse ($batch->subjects as $subject)
                        <span class="badge text-bg-primary-subtle me-1 mb-1">{{ $subject->name }}</span>
                    @empty
                        <p class="text-muted small mb-0">No subjects assigned.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-person-video3 me-1 text-primary"></i> Teachers ({{ $batch->teachers->count() }})</h3>
                    <div class="row g-2">
                        @forelse ($batch->teachers as $teacher)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 border rounded p-2">
                                    <img src="{{ $teacher->user?->avatar_url }}" class="avatar-sm" alt="">
                                    <div>
                                        <div class="fw-semibold small">{{ $teacher->user?->name }}</div>
                                        <div class="text-muted" style="font-size:.75rem">{{ $teacher->subject?->name ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No teachers assigned.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-mortarboard me-1 text-primary"></i> Students ({{ $batch->students->count() }})</h3>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead><tr><th>Admission No</th><th>Name</th><th>Mobile</th></tr></thead>
                            <tbody>
                                @forelse ($batch->students as $student)
                                    <tr>
                                        <td>{{ $student->admission_number }}</td>
                                        <td><a href="{{ route('admin.students.show', $student) }}" class="text-decoration-none">{{ $student->user?->name }}</a></td>
                                        <td>{{ $student->user?->mobile ?: '—' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted small">No students assigned.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
