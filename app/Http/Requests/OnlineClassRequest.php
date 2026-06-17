<?php

namespace App\Http\Requests;

use App\Models\OnlineClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OnlineClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'teacher_id' => ['required', 'integer', Rule::exists('teachers', 'id')->whereNull('deleted_at')],
            'branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')->whereNull('deleted_at')],
            'batch_id' => ['nullable', 'integer', Rule::exists('batches', 'id')->whereNull('deleted_at')],
            'class_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'meeting_link' => ['required', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(OnlineClass::STATUSES)],
        ];
    }
}
