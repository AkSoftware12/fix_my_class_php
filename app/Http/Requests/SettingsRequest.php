<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->route('group')) {
            'general' => [
                'app_name' => ['required', 'string', 'max:120'],
                'app_tagline' => ['nullable', 'string', 'max:200'],
                'contact_email' => ['nullable', 'email:rfc', 'max:180'],
                'contact_phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:500'],
                'timezone' => ['required', 'string', 'timezone:all'],
                'date_format' => ['required', 'string', 'max:20'],
            ],
            'smtp' => [
                'smtp_host' => ['required', 'string', 'max:180'],
                'smtp_port' => ['required', 'integer', 'between:1,65535'],
                'smtp_username' => ['nullable', 'string', 'max:180'],
                'smtp_password' => ['nullable', 'string', 'max:180'],
                'smtp_encryption' => ['required', Rule::in(['none', 'tls', 'ssl'])],
                'smtp_from_address' => ['required', 'email:rfc', 'max:180'],
                'smtp_from_name' => ['required', 'string', 'max:120'],
            ],
            'sms' => [
                'sms_provider' => ['required', Rule::in(['none', 'twilio', 'msg91', 'textlocal'])],
                'sms_api_key' => ['nullable', 'string', 'max:255'],
                'sms_sender_id' => ['nullable', 'string', 'max:20'],
            ],
            'notifications' => [
                'notify_email_enabled' => ['required', 'boolean'],
                'notify_sms_enabled' => ['required', 'boolean'],
                'notify_homework' => ['required', 'boolean'],
                'notify_notices' => ['required', 'boolean'],
                'notify_exam_results' => ['required', 'boolean'],
            ],
            'theme' => [
                'theme_primary_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
                'theme_default_mode' => ['required', Rule::in(['light', 'dark', 'system'])],
                'theme_sidebar_compact' => ['required', 'boolean'],
            ],
            'branding' => [
                'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
                'logo_dark' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
                'favicon' => ['nullable', 'image', 'mimes:png,ico', 'max:512'],
            ],
            default => [],
        };
    }

    protected function prepareForValidation(): void
    {
        $booleans = [
            'notify_email_enabled', 'notify_sms_enabled', 'notify_homework',
            'notify_notices', 'notify_exam_results', 'theme_sidebar_compact',
        ];

        $merge = [];
        foreach ($booleans as $field) {
            if ($this->has($field) || in_array($this->route('group'), ['notifications', 'theme'])) {
                $merge[$field] = $this->boolean($field);
            }
        }

        if ($merge) {
            $this->merge($merge);
        }
    }
}
