@extends('layouts.app')

@section('title', $homework->title)

@section('content')
    @include('admin.partials.page-header', [
        'title' => $homework->title,
        'subtitle' => 'Assigned by '.($homework->creator?->name ?? '—').' · '.$homework->created_at->format('d M Y'),
        'actions' => auth()->user()->can('update', $homework)
            ? '<a href="'.route('admin.homework.edit', $homework).'" class="btn btn-primary"><i class="bi bi-pencil me-1"></i> Edit</a>'
            : '',
    ])

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-info-circle me-1 text-primary"></i> Details</h3>
                    <dl class="row small mb-0">
                        <dt class="col-5">Type</dt><dd class="col-7"><span class="badge text-bg-secondary-subtle text-uppercase">{{ $homework->type }}</span></dd>
                        <dt class="col-5">Subject</dt><dd class="col-7">{{ $homework->subject?->name ?: '—' }}</dd>
                        <dt class="col-5">Visibility</dt><dd class="col-7">{{ ucfirst($homework->visibility) }}</dd>
                        <dt class="col-5">Due Date</dt><dd class="col-7">{{ $homework->due_date?->format('d M Y') ?: '—' }}</dd>
                        <dt class="col-5">Status</dt><dd class="col-7">@include('admin.homework.partials.status', ['status' => $homework->status])</dd>
                        @if ($homework->attachment_url)
                            <dt class="col-5">Attachment</dt>
                            <dd class="col-7"><a href="{{ $homework->attachment_url }}" target="_blank"><i class="bi bi-paperclip"></i> Open</a></dd>
                        @endif
                    </dl>
                    <hr>
                    <h4 class="small fw-bold text-muted text-uppercase">Targets</h4>
                    @foreach ($homework->targets as $target)
                        <span class="badge text-bg-primary-subtle me-1 mb-1">{{ ucfirst($target->target_type) }}: {{ $target->target_name }}</span>
                    @endforeach
                    @if ($homework->description)
                        <hr>
                        <h4 class="small fw-bold text-muted text-uppercase">Description</h4>
                        <div class="small">{!! nl2br(e($homework->description)) !!}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3">
                        <i class="bi bi-inbox me-1 text-primary"></i> Submissions ({{ $homework->submissions->count() }})
                    </h3>

                    @forelse ($homework->submissions as $submission)
                        <div class="border rounded p-3 mb-2">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <img src="{{ $submission->student?->photo_url }}" class="avatar-sm" alt="">
                                <div class="me-auto">
                                    <div class="fw-semibold">{{ $submission->student?->user?->name }}</div>
                                    <div class="text-muted small">{{ $submission->student?->admission_number }} · submitted {{ $submission->created_at->diffForHumans() }}</div>
                                </div>
                                @include('admin.homework.partials.status', ['status' => $submission->status])
                                @if ($submission->marks !== null)
                                    <span class="badge text-bg-primary-subtle">{{ $submission->marks }}/100</span>
                                @endif
                                @can('update', $homework)
                                    @php $rd = $submission->only(['id', 'status', 'remarks', 'feedback', 'marks']); @endphp
                                    <button class="btn btn-sm btn-outline-primary"
                                            onclick='openReview(@json($rd))'>
                                        <i class="bi bi-check2-square me-1"></i>Review
                                    </button>
                                @endcan
                            </div>
                            @if ($submission->answer_text)
                                <div class="small mt-2 p-2 bg-body-secondary rounded">{!! nl2br(e(\Illuminate\Support\Str::limit($submission->answer_text, 400))) !!}</div>
                            @endif
                            @if ($submission->attachment_url)
                                <a class="small" href="{{ $submission->attachment_url }}" target="_blank"><i class="bi bi-paperclip"></i> Attachment</a>
                            @endif
                            @if ($submission->remarks || $submission->feedback)
                                <div class="small mt-2 text-muted">
                                    @if ($submission->remarks)<div><strong>Remarks:</strong> {{ $submission->remarks }}</div>@endif
                                    @if ($submission->feedback)<div><strong>Feedback:</strong> {{ $submission->feedback }}</div>@endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No submissions yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Review modal --}}
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="reviewForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title">Review Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="reviewSubmissionId">
                    <div class="mb-3">
                        <label class="form-label required">Status</label>
                        <select id="reviewStatus" class="form-select" required>
                            <option value="submitted">Submitted</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Marks (0–100)</label>
                        <input type="number" id="reviewMarks" class="form-control" min="0" max="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea id="reviewRemarks" class="form-control" rows="2" maxlength="2000"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Feedback for student</label>
                        <textarea id="reviewFeedback" class="form-control" rows="2" maxlength="2000"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Review</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openReview(submission) {
        $('#reviewSubmissionId').val(submission.id);
        $('#reviewStatus').val(submission.status);
        $('#reviewMarks').val(submission.marks);
        $('#reviewRemarks').val(submission.remarks);
        $('#reviewFeedback').val(submission.feedback);
        new bootstrap.Modal('#reviewModal').show();
    }

    $('#reviewForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#reviewSubmissionId').val();
        $.post(`{{ url('admin/homework/'.$homework->id.'/submissions') }}/${id}`, {
            _method: 'PUT',
            status: $('#reviewStatus').val(),
            marks: $('#reviewMarks').val() || null,
            remarks: $('#reviewRemarks').val(),
            feedback: $('#reviewFeedback').val(),
        })
        .done((res) => {
            fmcToast(res.message);
            setTimeout(() => location.reload(), 800);
        })
        .fail((xhr) => fmcToast(xhr.responseJSON?.message || 'Review failed.', 'error'));
    });
</script>
@endpush
