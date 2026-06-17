@php($map = [
    'scheduled' => 'text-bg-info-subtle',
    'live' => 'text-bg-danger-subtle',
    'completed' => 'text-bg-success-subtle',
    'cancelled' => 'text-bg-secondary-subtle',
])
<span class="badge rounded-pill {{ $map[$status] ?? 'text-bg-secondary-subtle' }}">{{ ucfirst($status) }}</span>
