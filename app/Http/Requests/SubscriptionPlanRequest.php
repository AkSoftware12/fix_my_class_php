<?php

namespace App\Http\Requests;

use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $planId = $this->route('plan')?->id;

        return [
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('subscription_plans', 'name')->ignore($planId)->withoutTrashed(),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_cycle' => ['required', Rule::in(SubscriptionPlan::BILLING_CYCLES)],
            'max_branches' => ['required', 'integer', 'min:1'],
            'max_teachers' => ['required', 'integer', 'min:1'],
            'max_students' => ['required', 'integer', 'min:1'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:200'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);

        if (is_string($this->input('features'))) {
            $this->merge([
                'features' => array_values(array_filter(array_map('trim', explode("\n", $this->input('features'))))),
            ]);
        }
    }
}
