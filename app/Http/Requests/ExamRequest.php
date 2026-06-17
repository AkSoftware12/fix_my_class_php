<?php

namespace App\Http\Requests;

use App\Models\Exam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'type' => ['required', Rule::in(Exam::TYPES)],
            'branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')->whereNull('deleted_at')],
            'school_class_id' => ['nullable', 'integer', Rule::exists('school_classes', 'id')->whereNull('deleted_at')],
            'batch_id' => ['nullable', 'integer', Rule::exists('batches', 'id')->whereNull('deleted_at')],
            'subject_id' => ['nullable', 'integer', Rule::exists('subjects', 'id')->whereNull('deleted_at')],
            'instructions' => ['nullable', 'string', 'max:10000'],
            'exam_date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'duration_minutes' => ['nullable', 'integer', 'min:5', 'max:600'],
            'total_marks' => ['required', 'integer', 'min:1', 'max:1000'],
            'passing_marks' => ['required', 'integer', 'min:0', 'lte:total_marks'],
            'status' => ['required', Rule::in(Exam::STATUSES)],
            'questions' => ['nullable', 'array'],
            'questions.*.id' => ['nullable', 'integer'],
            'questions.*.question' => ['required_with:questions.*', 'string', 'max:5000'],
            'questions.*.type' => ['nullable', Rule::in(['mcq', 'subjective'])],
            'questions.*.options' => ['nullable', 'array', 'max:6'],
            'questions.*.options.*' => ['nullable', 'string', 'max:500'],
            'questions.*.correct_option' => ['nullable', 'string', 'max:10'],
            'questions.*.marks' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
