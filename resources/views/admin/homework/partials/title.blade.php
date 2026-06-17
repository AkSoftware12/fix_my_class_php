<div>
    <a href="{{ route('admin.homework.show', $homework) }}" class="fw-semibold text-decoration-none">{{ $homework->title }}</a>
    <div class="small text-muted">
        {{ ucfirst($homework->visibility) }} ·
        {{ $homework->targets->take(2)->map(fn ($t) => $t->target_name)->join(', ') }}
        @if ($homework->targets->count() > 2)
            +{{ $homework->targets->count() - 2 }} more
        @endif
    </div>
</div>
