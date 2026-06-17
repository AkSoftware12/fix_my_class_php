@php($onlineClass = $onlineClass ?? null)

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label required" for="title">Title</label>
        <input type="text" id="title" name="title" maxlength="200" required
               class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $onlineClass?->title) }}">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label required" for="teacher_id">Teacher</label>
        <select id="teacher_id" name="teacher_id" class="form-select select2 @error('teacher_id') is-invalid @enderror" required>
            <option value="">Select teacher…</option>
            @foreach ($teachers as $teacher)
                <option value="{{ $teacher->id }}" @selected(old('teacher_id', $onlineClass?->teacher_id) == $teacher->id)>{{ $teacher->user?->name }}</option>
            @endforeach
        </select>
        @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="branch_id">Branch</label>
        <select id="branch_id" name="branch_id" class="form-select select2 @error('branch_id') is-invalid @enderror">
            <option value="">Teacher's branch</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected(old('branch_id', $onlineClass?->branch_id) == $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="batch_id">Batch</label>
        <select id="batch_id" name="batch_id" class="form-select select2 @error('batch_id') is-invalid @enderror">
            <option value="">Select batch…</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected(old('batch_id', $onlineClass?->batch_id) == $batch->id)>{{ $batch->name }}</option>
            @endforeach
        </select>
        @error('batch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label required" for="status">Status</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach (\App\Models\OnlineClass::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', $onlineClass?->status ?? 'scheduled') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label required" for="class_date">Date</label>
        <input type="date" id="class_date" name="class_date" required
               class="form-control @error('class_date') is-invalid @enderror"
               value="{{ old('class_date', $onlineClass?->class_date?->format('Y-m-d')) }}">
        @error('class_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label required" for="start_time">Start Time</label>
        <input type="time" id="start_time" name="start_time" required
               class="form-control @error('start_time') is-invalid @enderror"
               value="{{ old('start_time', $onlineClass?->start_time ? substr($onlineClass->start_time, 0, 5) : '') }}">
        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="end_time">End Time</label>
        <input type="time" id="end_time" name="end_time"
               class="form-control @error('end_time') is-invalid @enderror"
               value="{{ old('end_time', $onlineClass?->end_time ? substr($onlineClass->end_time, 0, 5) : '') }}">
        @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label required" for="meeting_link">Meeting Link</label>
        <input type="url" id="meeting_link" name="meeting_link" maxlength="500" required
               placeholder="https://meet.google.com/…"
               class="form-control @error('meeting_link') is-invalid @enderror"
               value="{{ old('meeting_link', $onlineClass?->meeting_link) }}">
        @error('meeting_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="description">Description</label>
        <textarea id="description" name="description" rows="3" maxlength="5000"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $onlineClass?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $onlineClass ? 'Update Class' : 'Schedule Class' }}</button>
    <a href="{{ route('admin.online-classes.index') }}" class="btn btn-light">Cancel</a>
</div>
