<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Models\Address;
use Illuminate\Foundation\Http\FormRequest;

class SetDefaultAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $address = $this->route('address');

        return $this->user()->can('setDefault', $address);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // No additional validation rules needed for setting default
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // No custom messages needed
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $address = $this->route('address');

            // Check if address is already default
            if ($address->is_default) {
                $validator->errors()->add('address', 'This address is already set as default.');
            }

            // Check if address is complete
            if (empty($address->first_name) || empty($address->last_name) || empty($address->address_line_1)) {
                $validator->errors()->add('address', 'Address must have complete information to be set as default.');
            }
        });
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization(): void
    {
        abort(403, 'You are not authorized to set this address as default.');
    }
}
