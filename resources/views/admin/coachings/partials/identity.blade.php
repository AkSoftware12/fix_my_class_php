<div class="d-flex align-items-center gap-2">
    @if ($coaching->logo_url)
        <img src="{{ $coaching->logo_url }}" class="avatar-sm" alt="">
    @else
        <span class="avatar-sm d-grid place-items-center text-bg-primary-subtle" style="display:grid;place-items:center"><i class="bi bi-building"></i></span>
    @endif
    <div>
        <a href="{{ route('admin.coachings.show', $coaching) }}" class="fw-semibold text-decoration-none">{{ $coaching->name }}</a>
    </div>
</div>
