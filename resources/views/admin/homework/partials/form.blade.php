@php($homework = $homework ?? null)

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label required" for="title">Title</label>
        <input type="text" id="title" name="title" maxlength="200" required
               class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $homework?->title) }}">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="subject_id">Subject</label>
        <select id="subject_id" name="subject_id" class="form-select select2 @error('subject_id') is-invalid @enderror">
            <option value="">Select subject…</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(old('subject_id', $homework?->subject_id) == $subject->id)>{{ $subject->name }}</option>
            @endforeach
        </select>
        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="description">Description / Instructions</label>
        <textarea id="description" name="description" rows="4"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $homework?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label required" for="type">Homework Type</label>
        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
            @foreach (\App\Models\Homework::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $homework?->type ?? 'text') === $type)>{{ strtoupper($type) }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="attachment" id="attachmentLabel">Attachment</label>
        <input type="file" id="attachment" name="attachment"
               class="form-control @error('attachment') is-invalid @enderror">
        @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($homework?->attachment_url)
            <a href="{{ $homework->attachment_url }}" target="_blank" class="small">Current attachment</a>
        @endif
    </div>

    <div class="col-md-3">
        <label class="form-label required" for="visibility">Visibility</label>
        <select id="visibility" name="visibility" class="form-select @error('visibility') is-invalid @enderror" required>
            <option value="private" @selected(old('visibility', $homework?->visibility ?? 'private') === 'private')>Private</option>
            <option value="public" @selected(old('visibility', $homework?->visibility) === 'public')>Public</option>
        </select>
        @error('visibility')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="due_date">Due Date</label>
        <input type="date" id="due_date" name="due_date"
               class="form-control @error('due_date') is-invalid @enderror"
               value="{{ old('due_date', $homework?->due_date?->format('Y-m-d')) }}">
        @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if ($homework)
        <div class="col-md-3">
            <label class="form-label required" for="status">Status</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                @foreach (\App\Models\Homework::STATUSES as $status)
                    <option value="{{ $status }}" @selected(old('status', $homework->status) === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endif

    <div class="col-12">
        @include('admin.partials.targets-field', [
            'selectedTargets' => $homework?->targets->map(fn ($t) => $t->target_type.':'.$t->target_id)->all() ?? [],
        ])
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $homework ? 'Update Homework' : 'Assign Homework' }}</button>
    <a href="{{ route('admin.homework.index') }}" class="btn btn-light">Cancel</a>
</div>

@push('scripts')
<script>
    // Hint accepted file types based on chosen homework type.
    $('#type').on('change', function () {
        const map = { pdf: '.pdf', image: 'image/*', video: 'video/*', text: '' };
        $('#attachment').attr('accept', map[this.value] || '');
        $('#attachmentLabel').toggleClass('required', this.value !== 'text' && !{{ $homework ? 'true' : 'false' }});
    }).trigger('change');
</script>
@endpush
