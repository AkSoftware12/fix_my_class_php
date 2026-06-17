@php($map = [
    'active' => 'text-bg-success-subtle',
    'expired' => 'text-bg-secondary-subtle',
    'cancelled' => 'text-bg-danger-subtle',
    'pending' => 'text-bg-warning-subtle',
])
<span class="badge rounded-pill {{ $map[$status] ?? 'text-bg-secondary-subtle' }}">{{ ucfirst($status) }}</span>
