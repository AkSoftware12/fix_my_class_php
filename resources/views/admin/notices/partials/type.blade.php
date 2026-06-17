@php($map = [
    'general' => ['text-bg-secondary-subtle', 'bi-info-circle'],
    'holiday' => ['text-bg-success-subtle', 'bi-sun'],
    'event' => ['text-bg-info-subtle', 'bi-calendar-event'],
    'exam' => ['text-bg-warning-subtle', 'bi-clipboard-check'],
    'urgent' => ['text-bg-danger-subtle', 'bi-exclamation-triangle'],
])
@php([$class, $icon] = $map[$type] ?? $map['general'])
<span class="badge rounded-pill {{ $class }}"><i class="bi {{ $icon }} me-1"></i>{{ ucfirst($type) }}</span>
