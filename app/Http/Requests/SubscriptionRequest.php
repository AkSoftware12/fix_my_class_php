<?php

namespace App\Http\Requests;

use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coaching_id' => ['required', 'integer', Rule::exists('coachings', 'id')->whereNull('deleted_at')],
            'subscription_plan_id' => ['required', 'integer', Rule::exists('subscription_plans', 'id')->whereNull('deleted_at')],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(Subscription::STATUSES)],
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
