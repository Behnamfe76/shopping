<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Enums\AddressType;
use Illuminate\Foundation\Http\FormRequest;

class SearchAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('search', \Fereydooni\Shopping\app\Models\Address::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => 'required|string|min:2|max:255',
            'type' => 'nullable|in:'.implode(',', array_column(AddressType::cases(), 'value')),
            'pagination' => 'nullable|in:regular,simple,cursor',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'cursorPaginate' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required.',
            'query.min' => 'Search query must be at least 2 characters.',
            'query.max' => 'Search query cannot exceed 255 characters.',
            'type.in' => 'Invalid address type selected.',
            'pagination.in' => 'Invalid pagination type. Must be regular, simple, or cursor.',
            'per_page.integer' => 'Per page must be a number.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
            'page.integer' => 'Page must be a number.',
            'page.min' => 'Page must be at least 1.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'query' => 'search query',
            'per_page' => 'per page',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check type-specific search permissions
            if ($this->has('type')) {
                $type = $this->get('type');

                if ($type === 'billing' && ! $this->user()->can('address.view.billing')) {
                    $validator->errors()->add('type', 'You do not have permission to search billing addresses.');
                }

                if ($type === 'shipping' && ! $this->user()->can('address.view.shipping')) {
                    $validator->errors()->add('type', 'You do not have permission to search shipping addresses.');
                }
            }

            // Validate cursor pagination parameters
            if ($this->get('pagination') === 'cursor' && ! $this->has('cursor')) {
                // Cursor pagination requires a cursor parameter
                // This is optional for the first page
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default pagination type if not provided
        if (! $this->has('pagination')) {
            $this->merge(['pagination' => 'regular']);
        }

        // Set default per_page if not provided
        if (! $this->has('per_page')) {
            $this->merge(['per_page' => 15]);
        }
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization(): void
    {
        abort(403, 'You are not authorized to search addresses.');
    }
}
