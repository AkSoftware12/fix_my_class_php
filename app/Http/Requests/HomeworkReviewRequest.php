<?php

namespace App\Http\Requests;

use App\Models\HomeworkSubmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HomeworkReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(HomeworkSubmission::STATUSES)],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'feedback' => ['nullable', 'string', 'max:2000'],
            'marks' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }
}
