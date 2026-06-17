@extends('layouts.app')

@section('title', 'Exam Results')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Results: '.$exam->title,
        'subtitle' => 'Total marks '.$exam->total_marks.' · passing '.$exam->passing_marks,
        'actions' => '<a href="'.route('admin.exams.results.export', ['exam' => $exam, 'format' => 'csv']).'" class="btn btn-outline-primary"><i class="bi bi-download me-1"></i> Export</a>'
            .' <button class="btn btn-success" onclick="fmcPost(\''.route('admin.exams.results.publish', $exam).'\', \'Publish all results for this exam?\')"><i class="bi bi-megaphone me-1"></i> Publish</button>'
            .' <button class="btn btn-outline-warning" onclick="fmcPost(\''.route('admin.exams.results.unpublish', $exam).'\', \'Unpublish results?\')"><i class="bi bi-eye-slash me-1"></i> Unpublish</button>',
    ])

    <form method="POST" action="{{ route('admin.exams.results.store', $exam) }}">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th><th>Admission No</th><th>Student</th>
                                <th style="width:140px">Marks (/{{ $exam->total_marks }})</th>
                                <th>Grade</th><th>Rank</th><th>Result</th><th style="width:220px">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                @php($result = $existing[$student->id] ?? null)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->admission_number }}</td>
                                    <td>
                                        <input type="hidden" name="results[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                        {{ $student->user?->name }}
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" step="0.01" min="0" max="{{ $exam->total_marks }}"
                                               name="results[{{ $loop->index }}][marks_obtained]"
                                               value="{{ old('results.'.$loop->index.'.marks_obtained', $result?->marks_obtained) }}">
                                    </td>
                                    <td>{!! $result?->grade ? '<span class="badge text-bg-primary-subtle">'.$result->grade.'</span>' : '—' !!}</td>
                                    <td>{{ $result?->rank ?: '—' }}</td>
                                    <td>
                                        @if ($result)
                                            <span class="badge {{ $result->is_pass ? 'text-bg-success-subtle' : 'text-bg-danger-subtle' }}">
                                                {{ $result->is_pass ? 'Pass' : 'Fail' }}
                                            </span>
                                            @if ($result->is_published)
                                                <span class="badge text-bg-info-subtle">Published</span>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" maxlength="500"
                                               name="results[{{ $loop->index }}][remarks]"
                                               value="{{ old('results.'.$loop->index.'.remarks', $result?->remarks) }}">
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-muted">No students found for this exam's class/batch/branch scope.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($students->isNotEmpty())
                <div class="card-footer bg-transparent d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Results</button>
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-light">Back to exams</a>
                </div>
            @endif
        </div>
    </form>

    @if ($existing->isNotEmpty())
        <div class="card mt-3">
            <div class="card-body">
                <h3 class="h6 fw-bold mb-3"><i class="bi bi-file-earmark-pdf me-1 text-primary"></i> Marksheets</h3>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($existing as $result)
                        <a class="btn btn-sm btn-outline-secondary"
                           href="{{ route('admin.exams.results.marksheet', [$exam, $result]) }}">
                            <i class="bi bi-download me-1"></i>{{ $result->student?->user?->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection
