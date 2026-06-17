{{-- $title, $subtitle (optional), slot-style $actions html via @section or inline --}}
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <div class="me-auto">
        <h1 class="h4 fw-bold mb-0">{{ $title }}</h1>
        @isset($subtitle)
            <div class="text-muted small">{{ $subtitle }}</div>
        @endisset
    </div>
    {!! $actions ?? '' !!}
</div>
