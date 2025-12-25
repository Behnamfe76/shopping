<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\DTOs\ProductDiscountDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductDiscountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('discount'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = ProductDiscountDTO::rules();

        // Make some fields optional for updates
        $rules['product_id'] = 'sometimes|required|integer|min:1|exists:products,id';
        $rules['discount_type'] = 'sometimes|required|string|in:percent,fixed';
        $rules['amount'] = 'sometimes|required|numeric|min:0';
        $rules['start_date'] = 'sometimes|required|date';
        $rules['end_date'] = 'sometimes|required|date|after:start_date';

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return ProductDiscountDTO::messages();
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'updated_by' => $this->user()->id,
        ]);
    }
}
