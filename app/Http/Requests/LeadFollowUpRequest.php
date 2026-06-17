<?php

namespace App\Http\Requests;

use App\Models\AdmissionLead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadFollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stage' => ['required', Rule::in(array_keys(AdmissionLead::STAGES))],
            'remarks' => ['required', 'string', 'max:1000'],
            'next_follow_up_at' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}
