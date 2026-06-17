<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CoachingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $coachingId = $this->route('coaching')?->id;

        return [
            'city_id' => [
                'required', 'integer',
                Rule::exists('cities', 'id')->whereNull('deleted_at'),
                Rule::unique('coachings', 'city_id')->ignore($coachingId)->withoutTrashed(),
            ],
            'name' => ['required', 'string', 'max:180'],
            'owner_name' => ['required', 'string', 'max:120'],
            'email' => [
                'required', 'email:rfc', 'max:180',
                Rule::unique('coachings', 'email')->ignore($coachingId)->withoutTrashed(),
            ],
            'mobile' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'address' => ['nullable', 'string', 'max:1000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
