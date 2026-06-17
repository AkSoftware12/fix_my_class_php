@php($map = [
    'new' => 'text-bg-info-subtle',
    'follow_up' => 'text-bg-warning-subtle',
    'demo_class' => 'text-bg-primary-subtle',
    'admission_done' => 'text-bg-success-subtle',
    'rejected' => 'text-bg-danger-subtle',
])
<span class="badge rounded-pill {{ $map[$stage] ?? 'text-bg-secondary-subtle' }}">
    {{ \App\Models\AdmissionLead::STAGES[$stage] ?? ucfirst($stage) }}
</span>
