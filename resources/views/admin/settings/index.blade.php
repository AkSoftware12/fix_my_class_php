@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Settings',
        'subtitle' => 'Platform configuration',
    ])

    @php($tabs = [
        'general' => ['General', 'bi-sliders'],
        'smtp' => ['SMTP / Email', 'bi-envelope'],
        'sms' => ['SMS', 'bi-chat-left-text'],
        'notifications' => ['Notifications', 'bi-bell'],
        'theme' => ['Theme', 'bi-palette'],
        'branding' => ['Logo & Branding', 'bi-image'],
    ])

    <div class="row g-3">
        <div class="col-lg-3">
            <div class="card">
                <div class="list-group list-group-flush">
                    @foreach ($tabs as $key => [$label, $icon])
                        <a href="{{ route('admin.settings.index', $key) }}"
                           class="list-group-item list-group-item-action {{ $group === $key ? 'active' : '' }}">
                            <i class="bi {{ $icon }} me-2"></i>{{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">{{ $tabs[$group][0] }} Settings</h2>

                    <form method="POST" action="{{ route('admin.settings.update', $group) }}" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')

                        @if ($group === 'general')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Application Name</label>
                                    <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror"
                                           value="{{ old('app_name', setting('app_name')) }}" required>
                                    @error('app_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tagline</label>
                                    <input type="text" name="app_tagline" class="form-control @error('app_tagline') is-invalid @enderror"
                                           value="{{ old('app_tagline', setting('app_tagline')) }}">
                                    @error('app_tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                                           value="{{ old('contact_email', setting('contact_email')) }}">
                                    @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="text" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror"
                                           value="{{ old('contact_phone', setting('contact_phone')) }}">
                                    @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Timezone</label>
                                    <select name="timezone" class="form-select select2 @error('timezone') is-invalid @enderror" required>
                                        @foreach (timezone_identifiers_list() as $tz)
                                            <option value="{{ $tz }}" @selected(old('timezone', setting('timezone', 'Asia/Kolkata')) === $tz)>{{ $tz }}</option>
                                        @endforeach
                                    </select>
                                    @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Date Format</label>
                                    <select name="date_format" class="form-select @error('date_format') is-invalid @enderror" required>
                                        @foreach (['d M Y' => now()->format('d M Y'), 'd/m/Y' => now()->format('d/m/Y'), 'm/d/Y' => now()->format('m/d/Y'), 'Y-m-d' => now()->format('Y-m-d')] as $format => $sample)
                                            <option value="{{ $format }}" @selected(old('date_format', setting('date_format', 'd M Y')) === $format)>{{ $sample }}</option>
                                        @endforeach
                                    </select>
                                    @error('date_format')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', setting('address')) }}</textarea>
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                        @elseif ($group === 'smtp')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">SMTP Host</label>
                                    <input type="text" name="smtp_host" class="form-control @error('smtp_host') is-invalid @enderror"
                                           value="{{ old('smtp_host', setting('smtp_host')) }}" required>
                                    @error('smtp_host')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Port</label>
                                    <input type="number" name="smtp_port" class="form-control @error('smtp_port') is-invalid @enderror"
                                           value="{{ old('smtp_port', setting('smtp_port', 587)) }}" required>
                                    @error('smtp_port')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Encryption</label>
                                    <select name="smtp_encryption" class="form-select @error('smtp_encryption') is-invalid @enderror" required>
                                        @foreach (['tls', 'ssl', 'none'] as $enc)
                                            <option value="{{ $enc }}" @selected(old('smtp_encryption', setting('smtp_encryption', 'tls')) === $enc)>{{ strtoupper($enc) }}</option>
                                        @endforeach
                                    </select>
                                    @error('smtp_encryption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="smtp_username" class="form-control @error('smtp_username') is-invalid @enderror"
                                           value="{{ old('smtp_username', setting('smtp_username')) }}" autocomplete="off">
                                    @error('smtp_username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="smtp_password" class="form-control @error('smtp_password') is-invalid @enderror"
                                           value="{{ old('smtp_password', setting('smtp_password')) }}" autocomplete="new-password">
                                    @error('smtp_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">From Address</label>
                                    <input type="email" name="smtp_from_address" class="form-control @error('smtp_from_address') is-invalid @enderror"
                                           value="{{ old('smtp_from_address', setting('smtp_from_address')) }}" required>
                                    @error('smtp_from_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">From Name</label>
                                    <input type="text" name="smtp_from_name" class="form-control @error('smtp_from_name') is-invalid @enderror"
                                           value="{{ old('smtp_from_name', setting('smtp_from_name')) }}" required>
                                    @error('smtp_from_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                        @elseif ($group === 'sms')
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label required">SMS Provider</label>
                                    <select name="sms_provider" class="form-select @error('sms_provider') is-invalid @enderror" required>
                                        @foreach (['none' => 'Disabled', 'twilio' => 'Twilio', 'msg91' => 'MSG91', 'textlocal' => 'Textlocal'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('sms_provider', setting('sms_provider', 'none')) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('sms_provider')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">API Key</label>
                                    <input type="text" name="sms_api_key" class="form-control @error('sms_api_key') is-invalid @enderror"
                                           value="{{ old('sms_api_key', setting('sms_api_key')) }}" autocomplete="off">
                                    @error('sms_api_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Sender ID</label>
                                    <input type="text" name="sms_sender_id" class="form-control @error('sms_sender_id') is-invalid @enderror"
                                           value="{{ old('sms_sender_id', setting('sms_sender_id')) }}">
                                    @error('sms_sender_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                        @elseif ($group === 'notifications')
                            @php($switches = [
                                'notify_email_enabled' => 'Enable email notifications',
                                'notify_sms_enabled' => 'Enable SMS notifications',
                                'notify_homework' => 'Notify students when homework is assigned',
                                'notify_notices' => 'Notify users when a notice is published',
                                'notify_exam_results' => 'Notify students when exam results are published',
                            ])
                            @foreach ($switches as $key => $label)
                                <div class="form-check form-switch mb-3">
                                    <input type="hidden" name="{{ $key }}" value="0">
                                    <input class="form-check-input" type="checkbox" id="{{ $key }}" name="{{ $key }}" value="1"
                                           @checked(old($key, setting($key, '1')) == '1')>
                                    <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                                </div>
                            @endforeach

                        @elseif ($group === 'theme')
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label required">Primary Color</label>
                                    <input type="color" name="theme_primary_color" class="form-control form-control-color w-100 @error('theme_primary_color') is-invalid @enderror"
                                           value="{{ old('theme_primary_color', setting('theme_primary_color', '#2563EB')) }}">
                                    @error('theme_primary_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Default Mode</label>
                                    <select name="theme_default_mode" class="form-select @error('theme_default_mode') is-invalid @enderror" required>
                                        @foreach (['light' => 'Light', 'dark' => 'Dark', 'system' => 'Follow system'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('theme_default_mode', setting('theme_default_mode', 'light')) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('theme_default_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="theme_sidebar_compact" value="0">
                                        <input class="form-check-input" type="checkbox" id="theme_sidebar_compact" name="theme_sidebar_compact" value="1"
                                               @checked(old('theme_sidebar_compact', setting('theme_sidebar_compact', '0')) == '1')>
                                        <label class="form-check-label" for="theme_sidebar_compact">Compact sidebar</label>
                                    </div>
                                </div>
                            </div>

                        @elseif ($group === 'branding')
                            <div class="row g-3">
                                @foreach (['logo' => 'branding_logo', 'logo_dark' => 'branding_logo_dark', 'favicon' => 'branding_favicon'] as $field => $key)
                                    <div class="col-md-4">
                                        <label class="form-label text-capitalize">{{ str_replace('_', ' ', $field) }}</label>
                                        <input type="file" name="{{ $field }}" accept="image/*"
                                               class="form-control @error($field) is-invalid @enderror">
                                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        @if (setting($key))
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url(setting($key)) }}" alt="" class="mt-2 rounded border p-1" style="height:48px">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
