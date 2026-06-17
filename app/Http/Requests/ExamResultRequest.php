<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'results' => ['required', 'array', 'min:1'],
            'results.*.student_id' => ['required', 'integer', Rule::exists('students', 'id')->whereNull('deleted_at')],
            'results.*.marks_obtained' => ['nullable', 'numeric', 'min:0'],
            'results.*.remarks' => ['nullable', 'string', 'max:500'],
        ];
    }
}
