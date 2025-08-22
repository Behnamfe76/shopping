<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Enums\EmployeeStatus;
use Fereydooni\Shopping\app\Enums\EmploymentType;
use Fereydooni\Shopping\app\Enums\Gender;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $employee = $this->route('employee');
        return $this->user()->can('update', $employee);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employee->id)
            ],
            'phone' => ['sometimes', 'string', 'max:20'],
            'date_of_birth' => ['sometimes', 'date', 'before:today'],
            'gender' => ['sometimes', 'string', 'in:' . implode(',', array_column(Gender::cases(), 'value'))],
            'status' => ['sometimes', 'string', 'in:' . implode(',', array_column(EmployeeStatus::cases(), 'value'))],
            'employment_type' => ['sometimes', 'string', 'in:' . implode(',', array_column(EmploymentType::cases(), 'value'))],
            'department' => ['sometimes', 'string', 'max:100'],
            'position' => ['sometimes', 'string', 'max:100'],
            'manager_id' => ['sometimes', 'integer', 'exists:employees,id'],
            'salary' => ['sometimes', 'numeric', 'min:0'],
            'hourly_rate' => ['sometimes', 'numeric', 'min:0'],
            'performance_rating' => ['sometimes', 'numeric', 'min:1.0', 'max:5.0'],
            'next_review_date' => ['sometimes', 'date', 'after:today'],
            'address' => ['sometimes', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:100'],
            'state' => ['sometimes', 'string', 'max:100'],
            'postal_code' => ['sometimes', 'string', 'max:20'],
            'country' => ['sometimes', 'string', 'max:100'],
            'emergency_contact_name' => ['sometimes', 'string', 'max:100'],
            'emergency_contact_phone' => ['sometimes', 'string', 'max:20'],
            'emergency_contact_relationship' => ['sometimes', 'string', 'max:50'],
            'vacation_days_total' => ['sometimes', 'integer', 'min:0'],
            'sick_days_total' => ['sometimes', 'integer', 'min:0'],
            'benefits_enrolled' => ['sometimes', 'boolean'],
            'skills' => ['sometimes', 'array'],
            'certifications' => ['sometimes', 'array'],
            'training_completed' => ['sometimes', 'array'],
            'notes' => ['sometimes', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Email must be unique.',
            'performance_rating.min' => 'Performance rating must be at least 1.0.',
            'performance_rating.max' => 'Performance rating cannot exceed 5.0.',
            'next_review_date.after' => 'Next review date must be in the future.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Remove sensitive fields that shouldn't be updated via this endpoint
        $this->except(['user_id', 'employee_number', 'hire_date', 'termination_date']);
    }
}

