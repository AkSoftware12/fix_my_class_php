@php($exam = $exam ?? null)

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label required" for="title">Exam Title</label>
        <input type="text" id="title" name="title" maxlength="200" required
               class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $exam?->title) }}">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label required" for="type">Exam Type</label>
        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
            @foreach (\App\Models\Exam::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $exam?->type ?? 'mcq') === $type)>{{ strtoupper($type) }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label required" for="status">Status</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach (\App\Models\Exam::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', $exam?->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="subject_id">Subject</label>
        <select id="subject_id" name="subject_id" class="form-select select2 @error('subject_id') is-invalid @enderror">
            <option value="">Select subject…</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(old('subject_id', $exam?->subject_id) == $subject->id)>{{ $subject->name }}</option>
            @endforeach
        </select>
        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="branch_id">Branch</label>
        <select id="branch_id" name="branch_id" class="form-select select2 @error('branch_id') is-invalid @enderror">
            <option value="">All branches</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected(old('branch_id', $exam?->branch_id) == $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="school_class_id">Class</label>
        <select id="school_class_id" name="school_class_id" class="form-select select2 @error('school_class_id') is-invalid @enderror">
            <option value="">All classes</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected(old('school_class_id', $exam?->school_class_id) == $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        @error('school_class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="batch_id">Batch</label>
        <select id="batch_id" name="batch_id" class="form-select select2 @error('batch_id') is-invalid @enderror">
            <option value="">All batches</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected(old('batch_id', $exam?->batch_id) == $batch->id)>{{ $batch->name }}</option>
            @endforeach
        </select>
        @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="exam_date">Exam Date</label>
        <input type="date" id="exam_date" name="exam_date"
               class="form-control @error('exam_date') is-invalid @enderror"
               value="{{ old('exam_date', $exam?->exam_date?->format('Y-m-d')) }}">
        @error('exam_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="start_time">Start Time</label>
        <input type="time" id="start_time" name="start_time"
               class="form-control @error('start_time') is-invalid @enderror"
               value="{{ old('start_time', $exam?->start_time ? substr($exam->start_time, 0, 5) : '') }}">
        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-2">
        <label class="form-label" for="duration_minutes">Duration (min)</label>
        <input type="number" id="duration_minutes" name="duration_minutes" min="5" max="600"
               class="form-control @error('duration_minutes') is-invalid @enderror"
               value="{{ old('duration_minutes', $exam?->duration_minutes) }}">
        @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-2">
        <label class="form-label required" for="total_marks">Total Marks</label>
        <input type="number" id="total_marks" name="total_marks" min="1" max="1000" required
               class="form-control @error('total_marks') is-invalid @enderror"
               value="{{ old('total_marks', $exam?->total_marks ?? 100) }}">
        @error('total_marks')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-2">
        <label class="form-label required" for="passing_marks">Passing Marks</label>
        <input type="number" id="passing_marks" name="passing_marks" min="0" required
               class="form-control @error('passing_marks') is-invalid @enderror"
               value="{{ old('passing_marks', $exam?->passing_marks ?? 33) }}">
        @error('passing_marks')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="instructions">Instructions</label>
        <textarea id="instructions" name="instructions" rows="3"
                  class="form-control @error('instructions') is-invalid @enderror">{{ old('instructions', $exam?->instructions) }}</textarea>
        @error('instructions')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 mt-4 d-flex align-items-center">
        <h3 class="h6 fw-bold text-primary mb-0"><i class="bi bi-question-circle me-1"></i>Questions</h3>
        <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="addQuestion">
            <i class="bi bi-plus-lg me-1"></i>Add Question
        </button>
    </div>

    <div class="col-12">
        <div id="questionsContainer"></div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $exam ? 'Update Exam' : 'Create Exam' }}</button>
    <a href="{{ route('admin.exams.index') }}" class="btn btn-light">Cancel</a>
</div>

@push('scripts')
<script>
    const existingQuestions = {!! json_encode(old('questions') ?: ($exam?->questions?->map(fn ($q) => [
        'id' => $q->id,
        'question' => $q->question,
        'type' => $q->type,
        'options' => $q->options ?? [],
        'correct_option' => $q->correct_option,
        'marks' => $q->marks,
    ])->values()->all() ?? [])) !!};

    let qIndex = 0;

    function questionCard(q = {}) {
        const i = qIndex++;
        const options = q.options || ['', '', '', ''];
        const optionInputs = [0, 1, 2, 3].map((n) => `
            <div class="input-group input-group-sm mb-1">
                <span class="input-group-text">${String.fromCharCode(65 + n)}</span>
                <input type="text" class="form-control" name="questions[${i}][options][${n}]" value="${(options[n] || '').replace(/"/g, '&quot;')}">
            </div>`).join('');

        return `
        <div class="card border mb-2 question-card">
            <div class="card-body">
                <input type="hidden" name="questions[${i}][id]" value="${q.id || ''}">
                <div class="row g-2">
                    <div class="col-md-8">
                        <label class="form-label small required">Question</label>
                        <textarea class="form-control form-control-sm" name="questions[${i}][question]" rows="2" required>${q.question || ''}</textarea>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Type</label>
                        <select class="form-select form-select-sm q-type" name="questions[${i}][type]" data-index="${i}">
                            <option value="mcq" ${q.type !== 'subjective' ? 'selected' : ''}>MCQ</option>
                            <option value="subjective" ${q.type === 'subjective' ? 'selected' : ''}>Subjective</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Marks</label>
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control" name="questions[${i}][marks]" min="1" max="100" value="${q.marks || 1}">
                            <button type="button" class="btn btn-outline-danger remove-question"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                    <div class="col-md-8 mcq-only" data-index="${i}">
                        <label class="form-label small">Options</label>
                        ${optionInputs}
                    </div>
                    <div class="col-md-4 mcq-only" data-index="${i}">
                        <label class="form-label small">Correct Option</label>
                        <select class="form-select form-select-sm" name="questions[${i}][correct_option]">
                            <option value="">—</option>
                            ${['A','B','C','D'].map((o) => `<option value="${o}" ${q.correct_option === o ? 'selected' : ''}>${o}</option>`).join('')}
                        </select>
                    </div>
                </div>
            </div>
        </div>`;
    }

    function syncQuestionTypes() {
        $('.q-type').each(function () {
            const show = $(this).val() === 'mcq';
            $(`.mcq-only[data-index="${$(this).data('index')}"]`).toggle(show);
        });
    }

    $('#addQuestion').on('click', () => {
        $('#questionsContainer').append(questionCard());
        syncQuestionTypes();
    });

    $(document).on('change', '.q-type', syncQuestionTypes);
    $(document).on('click', '.remove-question', function () {
        $(this).closest('.question-card').remove();
    });

    existingQuestions.forEach((q) => $('#questionsContainer').append(questionCard(q)));
    syncQuestionTypes();
</script>
@endpush
