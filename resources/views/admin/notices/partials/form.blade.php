@php($notice = $notice ?? null)

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label required" for="title">Title</label>
        <input type="text" id="title" name="title" maxlength="200" required
               class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $notice?->title) }}">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label required" for="type">Notice Type</label>
        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
            @foreach (\App\Models\Notice::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $notice?->type ?? 'general') === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label required" for="body">Notice Body</label>
        <textarea id="body" name="body" rows="5" required
                  class="form-control @error('body') is-invalid @enderror">{{ old('body', $notice?->body) }}</textarea>
        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label required" for="visibility">Visibility</label>
        <select id="visibility" name="visibility" class="form-select @error('visibility') is-invalid @enderror" required>
            <option value="private" @selected(old('visibility', $notice?->visibility ?? 'private') === 'private')>Private (targeted)</option>
            <option value="public" @selected(old('visibility', $notice?->visibility) === 'public')>Public (everyone)</option>
        </select>
        @error('visibility')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label required" for="audience">Audience</label>
        <select id="audience" name="audience" class="form-select @error('audience') is-invalid @enderror" required>
            <option value="all" @selected(old('audience', $notice?->audience ?? 'all') === 'all')>Everyone</option>
            <option value="teachers" @selected(old('audience', $notice?->audience) === 'teachers')>Teachers only</option>
            <option value="students" @selected(old('audience', $notice?->audience) === 'students')>Students only</option>
        </select>
        @error('audience')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="publish_at">Schedule (publish at)</label>
        <input type="datetime-local" id="publish_at" name="publish_at"
               class="form-control @error('publish_at') is-invalid @enderror"
               value="{{ old('publish_at', $notice?->publish_at?->format('Y-m-d\TH:i')) }}">
        @error('publish_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Blank = publish immediately.</div>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="expires_at">Expiry Date</label>
        <input type="date" id="expires_at" name="expires_at"
               class="form-control @error('expires_at') is-invalid @enderror"
               value="{{ old('expires_at', $notice?->expires_at?->format('Y-m-d')) }}">
        @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="attachment">Attachment <span class="text-muted">(PDF/Image, max 10&nbsp;MB)</span></label>
        <input type="file" id="attachment" name="attachment" accept=".pdf,image/*"
               class="form-control @error('attachment') is-invalid @enderror">
        @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($notice?->attachment_url)
            <a href="{{ $notice->attachment_url }}" target="_blank" class="small">Current attachment</a>
        @endif
    </div>

    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                   @checked(old('is_active', $notice?->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>

    @if (isset($coachings) && $coachings->isNotEmpty())
    <div class="col-12" id="coachingWrapper">
        <label class="form-label required">Coaching</label>
        <select class="form-select select2" name="coaching_id" id="noticeCoaching">
            <option value="">Select coaching…</option>
            @foreach ($coachings as $c)
                <option value="{{ $c->id }}" @selected(old('coaching_id', $notice?->coaching_id) == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        @error('coaching_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
    @endif

    <div class="col-12" id="targetsWrapper">
        @include('admin.partials.targets-field', [
            'selectedTargets' => $notice?->targets->map(fn ($t) => $t->target_type.':'.$t->target_id)->all() ?? [],
        ])
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ $notice ? 'Update Notice' : 'Publish Notice' }}</button>
    <a href="{{ route('admin.notices.index') }}" class="btn btn-light">Cancel</a>
</div>

@push('scripts')
<script>
    $('#visibility').on('change', function () {
        const isPrivate = this.value === 'private';
        $('#targetsWrapper').toggle(isPrivate);
        $('#coachingWrapper').toggle(!isPrivate);
    }).trigger('change');
</script>
@endpush
