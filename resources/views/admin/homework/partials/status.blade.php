@php($map = [
    'pending' => 'text-bg-warning-subtle',
    'submitted' => 'text-bg-info-subtle',
    'reviewed' => 'text-bg-primary-subtle',
    'completed' => 'text-bg-success-subtle',
])
<span class="badge rounded-pill {{ $map[$status] ?? 'text-bg-secondary-subtle' }}">{{ ucfirst($status) }}</span>
