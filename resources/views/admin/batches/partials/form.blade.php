@php($batch = $batch ?? null)

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label required" for="name">Batch Name</label>
        <input type="text" id="name" name="name" maxlength="120" required
               class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $batch?->name) }}">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label required" for="branch_id">Branch</label>
        <select id="branch_id" name="branch_id" class="form-select select2 @error('branch_id') is-invalid @enderror" required>
            <option value="">Select branch…</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected(old('branch_id', $batch?->branch_id) == $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label required" for="school_class_id">Class</label>
        <select id="school_class_id" name="school_class_id" class="form-select select2 @error('school_class_id') is-invalid @enderror" required>
            <option value="">Select class…</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" @selected(old('school_class_id', $batch?->school_class_id) == $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        @error('school_class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="start_time">Start Time</label>
        <input type="time" id="start_time" name="start_time"
               class="form-control @error('start_time') is-invalid @enderror"
               value="{{ old('start_time', $batch?->start_time ? substr($batch->start_time, 0, 5) : '') }}">
        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="end_time">End Time</label>
        <input type="time" id="end_time" name="end_time"
               class="form-control @error('end_time') is-invalid @enderror"
               value="{{ old('end_time', $batch?->end_time ? substr($batch->end_time, 0, 5) : '') }}">
        @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="capacity">Capacity</label>
        <input type="number" id="capacity" name="capacity" min="1" max="10000"
               class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $batch?->capacity) }}">
        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @can('batches.assign')
        <div class="col-12 mt-4"><h3 class="h6 fw-bold text-primary mb-0"><i class="bi bi-link-45deg me-1"></i>Assignments</h3></div>

        <div class="col-md-4">
            <label class="form-label" for="subject_ids">Subjects</label>
            <select id="subject_ids" name="subject_ids[]" multiple class="form-select select2 @error('subject_ids.*') is-invalid @enderror">
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}"
                            @selected(in_array($subject->id, old('subject_ids', $batch?->subjects->pluck('id')->all() ?? [])))>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label" for="teacher_ids">Teachers</label>
            <select id="teacher_ids" name="teacher_ids[]" multiple class="form-select select2 @error('teacher_ids.*') is-invalid @enderror">
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}"
                            @selected(in_array($teacher->id, old('teacher_ids', $batch?->teachers->pluck('id')->all() ?? [])))>
                        {{ $teacher->user?->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label" for="student_ids">Students</label>
            <select id="student_ids" name="student_ids[]" multiple class="form-select select2 @error('student_ids.*') is-invalid @enderror">
                @foreach ($students as $student)
                    <option value="{{ $student->id }}"
                            @selected(in_array($student->id, old('student_ids', $batch?->students->pluck('id')->all() ?? [])))>
                        {{ $student->user?->name }} ({{ $student->admission_number }})
                    </option>
                @endforeach
            </select>
        </div>
    @endcan

    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   @checked(old('is_active', $batch?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $batch ? 'Update Batch' : 'Create Batch' }}</button>
    <a href="{{ route('admin.batches.index') }}" class="btn btn-light">Cancel</a>
</div>
