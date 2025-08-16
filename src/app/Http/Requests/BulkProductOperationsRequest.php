<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkProductOperationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('bulkOperations', \Fereydooni\Shopping\app\Models\Product::class);
    }

    public function rules(): array
    {
        return [
            'operation' => ['required', 'string', 'in:delete,update,activate,deactivate,feature,unfeature,publish,unpublish,archive'],
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'data' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'operation.required' => 'Operation type is required.',
            'operation.in' => 'Invalid operation type.',
            'product_ids.required' => 'Product IDs are required.',
            'product_ids.array' => 'Product IDs must be an array.',
            'product_ids.min' => 'At least one product must be selected.',
            'product_ids.*.exists' => 'One or more selected products do not exist.',
        ];
    }
}
