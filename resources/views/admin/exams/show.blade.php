@extends('layouts.app')

@section('title', $exam->title)

@section('content')
    @include('admin.partials.page-header', [
        'title' => $exam->title,
        'subtitle' => strtoupper($exam->type).' exam · created by '.($exam->creator?->name ?? '—'),
        'actions' => (auth()->user()->can('results.view')
                ? '<a href="'.route('admin.exams.results.entry', $exam).'" class="btn btn-outline-primary"><i class="bi bi-card-checklist me-1"></i> Results</a> '
                : '')
            .(auth()->user()->can('update', $exam)
                ? '<a href="'.route('admin.exams.edit', $exam).'" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Edit</a>'
                : ''),
    ])

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-info-circle me-1 text-primary"></i> Details</h3>
                    <dl class="row small mb-0">
                        <dt class="col-5">Type</dt><dd class="col-7"><span class="badge text-bg-secondary-subtle text-uppercase">{{ $exam->type }}</span></dd>
                        <dt class="col-5">Subject</dt><dd class="col-7">{{ $exam->subject?->name ?: '—' }}</dd>
                        <dt class="col-5">Class</dt><dd class="col-7">{{ $exam->schoolClass?->name ?: 'All' }}</dd>
                        <dt class="col-5">Batch</dt><dd class="col-7">{{ $exam->batch?->name ?: 'All' }}</dd>
                        <dt class="col-5">Date</dt><dd class="col-7">{{ $exam->exam_date?->format('d M Y') ?: '—' }} {{ $exam->start_time ? substr($exam->start_time, 0, 5) : '' }}</dd>
                        <dt class="col-5">Duration</dt><dd class="col-7">{{ $exam->duration_minutes ? $exam->duration_minutes.' min' : '—' }}</dd>
                        <dt class="col-5">Total Marks</dt><dd class="col-7">{{ $exam->total_marks }}</dd>
                        <dt class="col-5">Passing Marks</dt><dd class="col-7">{{ $exam->passing_marks }}</dd>
                        <dt class="col-5">Status</dt><dd class="col-7">@include('admin.exams.partials.status', ['status' => $exam->status])</dd>
                        <dt class="col-5">Results</dt><dd class="col-7">{{ $exam->results_count }}</dd>
                    </dl>
                    @if ($exam->instructions)
                        <hr>
                        <h4 class="small fw-bold text-muted text-uppercase">Instructions</h4>
                        <div class="small">{!! nl2br(e($exam->instructions)) !!}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-question-circle me-1 text-primary"></i> Questions ({{ $exam->questions->count() }})</h3>
                    @forelse ($exam->questions as $question)
                        <div class="border rounded p-3 mb-2">
                            <div class="d-flex gap-2">
                                <span class="fw-bold text-primary">Q{{ $loop->iteration }}.</span>
                                <div class="flex-grow-1">
                                    <div>{{ $question->question }}</div>
                                    @if ($question->type === 'mcq' && $question->options)
                                        <div class="row g-1 mt-1 small">
                                            @foreach ($question->options as $idx => $option)
                                                @php($letter = chr(65 + $idx))
                                                <div class="col-md-6">
                                                    <span class="badge {{ $question->correct_option === $letter ? 'text-bg-success-subtle' : 'text-bg-secondary-subtle' }}">{{ $letter }}</span>
                                                    {{ $option }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <span class="badge text-bg-primary-subtle align-self-start">{{ $question->marks }} marks</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No questions added{{ $exam->type === 'assignment' ? ' (assignment exam)' : '' }}.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
