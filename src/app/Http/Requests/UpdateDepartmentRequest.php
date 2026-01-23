<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\DTOs\DepartmentDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('department'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = DepartmentDTO::rules();
        $department = $this->route('department');

        // Make all fields optional for updates
        $rules['name'] = 'sometimes|required|string|max:255';
        $rules['code'] = 'sometimes|required|string|max:50|unique:departments,code,'.$department->id;
        $rules['status'] = 'sometimes|required|in:'.implode(',', array_column(\Fereydooni\Shopping\app\Enums\DepartmentStatus::cases(), 'value'));

        return $rules;
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
}
