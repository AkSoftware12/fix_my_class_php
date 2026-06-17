@extends('layouts.app')

@section('title', $student->user?->name)

@section('content')
    @include('admin.partials.page-header', [
        'title' => $student->user?->name,
        'subtitle' => 'Admission No: '.$student->admission_number,
        'actions' => '<a href="'.route('admin.students.id-card', $student).'" class="btn btn-outline-primary"><i class="bi bi-person-badge me-1"></i> ID Card</a>'
            .(auth()->user()->can('students.edit')
                ? ' <a href="'.route('admin.students.edit', $student).'" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Edit</a>'
                : ''),
    ])

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ $student->photo_url }}" class="avatar-lg mb-3" alt="">
                    <h2 class="h5 fw-bold mb-1">{{ $student->user?->name }}</h2>
                    <div class="text-muted small mb-2">{{ $student->admission_number }}</div>
                    <div class="mb-3">{!! status_badge($student->is_active) !!}</div>
                    <hr>
                    <dl class="row text-start small mb-0">
                        <dt class="col-5">Email</dt><dd class="col-7">{{ $student->user?->email }}</dd>
                        <dt class="col-5">Mobile</dt><dd class="col-7">{{ $student->user?->mobile ?: '—' }}</dd>
                        <dt class="col-5">Date of Birth</dt><dd class="col-7">{{ $student->date_of_birth?->format('d M Y') ?: '—' }}</dd>
                        <dt class="col-5">Branch</dt><dd class="col-7">{{ $student->branch?->name ?: '—' }}</dd>
                        <dt class="col-5">Class</dt><dd class="col-7">{{ $student->schoolClass?->name ?: '—' }}</dd>
                        <dt class="col-5">Batch</dt><dd class="col-7">{{ $student->batch?->name ?: '—' }}</dd>
                        <dt class="col-5">Guardian</dt><dd class="col-7">{{ $student->guardian_name ?: '—' }}</dd>
                        <dt class="col-5">Guardian Mobile</dt><dd class="col-7">{{ $student->guardian_mobile ?: '—' }}</dd>
                        <dt class="col-5">Admitted</dt><dd class="col-7">{{ $student->created_at->format('d M Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-journal-check me-1 text-primary"></i> Homework Submissions</h3>
                    @forelse ($student->homeworkSubmissions->take(8) as $submission)
                        <div class="d-flex align-items-center gap-3 py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $submission->homework?->title }}</div>
                                <div class="text-muted small">Submitted {{ $submission->created_at->format('d M Y') }}</div>
                            </div>
                            @include('admin.homework.partials.status', ['status' => $submission->status])
                            @if ($submission->marks !== null)
                                <span class="badge text-bg-primary-subtle">{{ $submission->marks }} marks</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No homework submissions yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-clipboard-check me-1 text-primary"></i> Exam Results</h3>
                    @if ($student->examResults->isEmpty())
                        <p class="text-muted small mb-0">No results published yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Exam</th><th>Marks</th><th>Grade</th><th>Rank</th><th>Result</th></tr></thead>
                                <tbody>
                                    @foreach ($student->examResults as $result)
                                        <tr>
                                            <td>{{ $result->exam?->title }}</td>
                                            <td>{{ $result->marks_obtained }} / {{ $result->exam?->total_marks }}</td>
                                            <td><span class="badge text-bg-primary-subtle">{{ $result->grade }}</span></td>
                                            <td>{{ $result->rank ?: '—' }}</td>
                                            <td>
                                                <span class="badge {{ $result->is_pass ? 'text-bg-success-subtle' : 'text-bg-danger-subtle' }}">
                                                    {{ $result->is_pass ? 'Pass' : 'Fail' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-folder2-open me-1 text-primary"></i> Documents</h3>
                    @forelse ($student->documents as $document)
                        <a href="{{ $document->file_url }}" target="_blank"
                           class="d-flex align-items-center gap-2 py-2 text-decoration-none {{ ! $loop->last ? 'border-bottom' : '' }}">
                            <i class="bi bi-file-earmark-text text-primary"></i>
                            <span class="flex-grow-1">{{ $document->title }}</span>
                            <span class="text-muted small">{{ number_format($document->size / 1024, 0) }} KB</span>
                        </a>
                    @empty
                        <p class="text-muted small mb-0">No documents uploaded.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
