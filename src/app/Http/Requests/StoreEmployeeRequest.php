<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Fereydooni\Shopping\app\Enums\EmployeeStatus;
use Fereydooni\Shopping\app\Enums\EmploymentType;
use Fereydooni\Shopping\app\Enums\Gender;
use Fereydooni\Shopping\app\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Employee::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:employees,user_id'],
            'employee_number' => ['nullable', 'string', 'max:50', 'unique:employees,employee_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:'.implode(',', array_column(Gender::cases(), 'value'))],
            'hire_date' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['nullable', 'string', 'in:'.implode(',', array_column(EmployeeStatus::cases(), 'value'))],
            'employment_type' => ['required', 'string', 'in:'.implode(',', array_column(EmploymentType::cases(), 'value'))],
            'department' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'max:100'],
            'manager_id' => ['nullable', 'integer', 'exists:employees,id'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:50'],
            'vacation_days_total' => ['nullable', 'integer', 'min:0'],
            'sick_days_total' => ['nullable', 'integer', 'min:0'],
            'benefits_enrolled' => ['nullable', 'boolean'],
            'skills' => ['nullable', 'array'],
            'certifications' => ['nullable', 'array'],
            'training_completed' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'User is required.',
            'user_id.unique' => 'This user is already an employee.',
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email must be unique.',
            'hire_date.required' => 'Hire date is required.',
            'employment_type.required' => 'Employment type is required.',
            'department.required' => 'Department is required.',
            'position.required' => 'Position is required.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('status')) {
            $this->merge(['status' => EmployeeStatus::PENDING->value]);
        }

        if (! $this->has('hire_date')) {
            $this->merge(['hire_date' => now()->format('Y-m-d')]);
        }

        if (! $this->has('employment_type')) {
            $this->merge(['employment_type' => EmploymentType::FULL_TIME->value]);
        }

        if (! $this->has('benefits_enrolled')) {
            $this->merge(['benefits_enrolled' => false]);
        }
    }
}
