<div class="d-flex align-items-center gap-2">
    <img src="{{ $teacher->user?->avatar_url }}" class="avatar-sm" alt="">
    <div>
        <a href="{{ route('admin.teachers.show', $teacher) }}" class="fw-semibold text-decoration-none">{{ $teacher->user?->name }}</a>
        <div class="text-muted small">{{ $teacher->user?->email }}</div>
    </div>
</div>
