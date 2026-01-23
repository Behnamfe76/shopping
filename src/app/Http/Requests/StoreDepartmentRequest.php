<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\DTOs\DepartmentDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \Fereydooni\Shopping\app\Models\Department::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return DepartmentDTO::rules();
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return DepartmentDTO::messages();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'department name',
            'code' => 'department code',
            'description' => 'department description',
            'parent_id' => 'parent department',
            'manager_id' => 'department manager',
            'location' => 'department location',
            'budget' => 'department budget',
            'headcount_limit' => 'headcount limit',
            'is_active' => 'active status',
            'status' => 'department status',
            'metadata' => 'metadata',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'budget' => $this->input('budget', 0),
            'headcount_limit' => $this->integer('headcount_limit', 0),
        ]);
    }
}
