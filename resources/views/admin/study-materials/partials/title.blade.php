@php($icons = ['pdf' => 'bi-file-earmark-pdf', 'doc' => 'bi-file-earmark-word', 'ppt' => 'bi-file-earmark-slides', 'zip' => 'bi-file-earmark-zip', 'image' => 'bi-file-earmark-image', 'video' => 'bi-file-earmark-play'])
<div class="d-flex align-items-center gap-2">
    <i class="bi {{ $icons[$material->file_type] ?? 'bi-file-earmark' }} fs-5 text-primary"></i>
    <div>
        <div class="fw-semibold">{{ $material->title }}</div>
        <div class="text-muted small">
            {{ $material->targets->take(2)->map(fn ($t) => $t->target_name)->join(', ') }}
            @if ($material->targets->count() > 2) +{{ $material->targets->count() - 2 }} more @endif
        </div>
    </div>
</div>
