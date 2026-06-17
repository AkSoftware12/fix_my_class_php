@php($material = $material ?? null)

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label required" for="title">Title</label>
        <input type="text" id="title" name="title" maxlength="200" required
               class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $material?->title) }}">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="subject_id">Subject</label>
        <select id="subject_id" name="subject_id" class="form-select select2 @error('subject_id') is-invalid @enderror">
            <option value="">Select subject…</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(old('subject_id', $material?->subject_id) == $subject->id)>{{ $subject->name }}</option>
            @endforeach
        </select>
        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="description">Description</label>
        <textarea id="description" name="description" rows="3" maxlength="5000"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $material?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label {{ $material ? '' : 'required' }}" for="file">
            File {{ $material ? '(leave blank to keep current)' : '' }}
            <span class="text-muted">(PDF, DOC, PPT, ZIP, image, video — max 100&nbsp;MB)</span>
        </label>
        <input type="file" id="file" name="file"
               accept=".pdf,.doc,.docx,.txt,.rtf,.ppt,.pptx,.zip,.rar,.7z,image/*,video/*"
               class="form-control @error('file') is-invalid @enderror" {{ $material ? '' : 'required' }}>
        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($material)
            <div class="form-text">Current: {{ strtoupper($material->file_type) }} · {{ $material->human_size }}</div>
        @endif
    </div>

    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   @checked(old('is_active', $material?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>

    <div class="col-12">
        @include('admin.partials.targets-field', [
            'selectedTargets' => $material?->targets->map(fn ($t) => $t->target_type.':'.$t->target_id)->all() ?? [],
        ])
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $material ? 'Update Material' : 'Upload Material' }}</button>
    <a href="{{ route('admin.study-materials.index') }}" class="btn btn-light">Cancel</a>
</div>
