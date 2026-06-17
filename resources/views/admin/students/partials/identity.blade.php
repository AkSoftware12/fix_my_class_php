<div class="d-flex align-items-center gap-2">
    <img src="{{ $student->photo_url }}" class="avatar-sm" alt="">
    <div>
        <a href="{{ route('admin.students.show', $student) }}" class="fw-semibold text-decoration-none">{{ $student->user?->name }}</a>
        <div class="text-muted small">{{ $student->user?->email }}</div>
    </div>
</div>
