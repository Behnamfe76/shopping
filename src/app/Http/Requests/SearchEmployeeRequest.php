<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Enums\EmployeeStatus;
use Fereydooni\Shopping\app\Enums\EmploymentType;
use Fereydooni\Shopping\app\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;

class SearchEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', 'App\Models\Employee');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:'.implode(',', array_column(EmployeeStatus::cases(), 'value'))],
            'employment_type' => ['nullable', 'string', 'in:'.implode(',', array_column(EmploymentType::cases(), 'value'))],
            'department' => ['nullable', 'string', 'max:100'],
            'position' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'in:'.implode(',', array_column(Gender::cases(), 'value'))],
            'manager_id' => ['nullable', 'integer', 'exists:employees,id'],
            'min_salary' => ['nullable', 'numeric', 'min:0'],
            'max_salary' => ['nullable', 'numeric', 'min:0', 'gte:min_salary'],
            'min_performance_rating' => ['nullable', 'numeric', 'min:1.0', 'max:5.0'],
            'max_performance_rating' => ['nullable', 'numeric', 'min:1.0', 'max:5.0', 'gte:min_performance_rating'],
            'hire_date_from' => ['nullable', 'date'],
            'hire_date_to' => ['nullable', 'date', 'after_or_equal:hire_date_from'],
            'benefits_enrolled' => ['nullable', 'boolean'],
            'has_manager' => ['nullable', 'boolean'],
            'is_manager' => ['nullable', 'boolean'],
            'sort_by' => ['nullable', 'string', 'in:first_name,last_name,email,department,position,salary,hire_date,performance_rating'],
            'sort_direction' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'max_salary.gte' => 'Maximum salary must be greater than or equal to minimum salary.',
            'max_performance_rating.gte' => 'Maximum performance rating must be greater than or equal to minimum performance rating.',
            'hire_date_to.after_or_equal' => 'Hire date to must be after or equal to hire date from.',
            'sort_by.in' => 'Invalid sort field.',
            'sort_direction.in' => 'Sort direction must be asc or desc.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
            'page.min' => 'Page must be at least 1.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        if (! $this->has('sort_by')) {
            $this->merge(['sort_by' => 'first_name']);
        }

        if (! $this->has('sort_direction')) {
            $this->merge(['sort_direction' => 'asc']);
        }

        if (! $this->has('per_page')) {
            $this->merge(['per_page' => 15]);
        }

        if (! $this->has('page')) {
            $this->merge(['page' => 1]);
        }
    }
}
