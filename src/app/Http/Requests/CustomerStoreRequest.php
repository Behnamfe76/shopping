<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Enums\CustomerStatus;
use Fereydooni\Shopping\app\Enums\CustomerType;
use Fereydooni\Shopping\app\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'company_name' => 'nullable|string|max:255|required_if:customer_type,business,wholesale',
            'tax_id' => 'nullable|string|max:50',
            'customer_type' => ['required', Rule::enum(CustomerType::class)],
            'status' => ['nullable', Rule::enum(CustomerStatus::class)],
            'preferred_payment_method' => 'nullable|string|max:50',
            'preferred_shipping_method' => 'nullable|string|max:50',
            'marketing_consent' => 'nullable|boolean',
            'newsletter_subscription' => 'nullable|boolean',
            'notes' => 'nullable|string|max:5000',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email address is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email address is already registered',
            'customer_type.required' => 'Customer type is required',
            'company_name.required_if' => 'Company name is required for business and wholesale customers',
            'date_of_birth.before' => 'Date of birth must be in the past',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default customer type if not provided
        if (! $this->has('customer_type')) {
            $this->merge([
                'customer_type' => CustomerType::INDIVIDUAL->value,
            ]);
        }

        // Set default status if not provided
        if (! $this->has('status')) {
            $this->merge([
                'status' => CustomerStatus::PENDING->value,
            ]);
        }

        $this->merge([
            'marketing_consent' => $this->boolean('marketing_consent'),
            'newsletter_subscription' => $this->boolean('newsletter_subscription'),
        ]);
    }
}
