@php($map = [
    'draft' => 'text-bg-secondary-subtle',
    'published' => 'text-bg-info-subtle',
    'completed' => 'text-bg-success-subtle',
    'cancelled' => 'text-bg-danger-subtle',
])
<span class="badge rounded-pill {{ $map[$status] ?? 'text-bg-secondary-subtle' }}">{{ ucfirst($status) }}</span>
