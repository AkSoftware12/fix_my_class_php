<?php

namespace App\Http\Requests;

use App\Models\Notice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coaching_id' => ['nullable', 'integer', 'exists:coachings,id'],
            'title' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string', 'max:50000'],
            'type' => ['required', Rule::in(Notice::TYPES)],
            'visibility' => ['required', Rule::in(['public', 'private'])],
            'audience' => ['required', Rule::in(Notice::AUDIENCES)],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'publish_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:publish_at'],
            'targets' => [Rule::requiredIf($this->input('visibility') === 'private'), 'nullable', 'array'],
            'targets.*' => ['string', 'regex:/^(coaching|branch|class|batch|student):\d+$/'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function messages(): array
    {
        return [
            'targets.required' => 'Select at least one target for a private notice.',
        ];
    }
}
