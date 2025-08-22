<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Fereydooni\Shopping\app\DTOs\EmployeeDTO;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $employee = $this->resource instanceof EmployeeDTO ? $this->resource : EmployeeDTO::fromModel($this->resource);

        return [
            'id' => $employee->id,
            'user_id' => $employee->user_id,
            'employee_number' => $employee->employee_number,
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'full_name' => $employee->full_name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'date_of_birth' => $employee->date_of_birth?->toISOString(),
            'gender' => $employee->gender,
            'gender_label' => $employee->gender?->label(),
            'hire_date' => $employee->hire_date?->toISOString(),
            'termination_date' => $employee->termination_date?->toISOString(),
            'status' => $employee->status,
            'status_label' => $employee->status?->label(),
            'status_color' => $employee->status?->getColor(),
            'employment_type' => $employee->employment_type,
            'employment_type_label' => $employee->employment_type?->label(),
            'department' => $employee->department,
            'position' => $employee->position,
            'manager_id' => $employee->manager_id,
            'salary' => $employee->salary,
            'hourly_rate' => $employee->hourly_rate,
            'performance_rating' => $employee->performance_rating,
            'next_review_date' => $employee->next_review_date?->toISOString(),
            'address' => $employee->address,
            'city' => $employee->city,
            'state' => $employee->state,
            'postal_code' => $employee->postal_code,
            'country' => $employee->country,
            'emergency_contact_name' => $employee->emergency_contact_name,
            'emergency_contact_phone' => $employee->emergency_contact_phone,
            'emergency_contact_relationship' => $employee->emergency_contact_relationship,
            'bank_name' => $employee->bank_name,
            'bank_account_number' => $employee->bank_account_number,
            'bank_routing_number' => $employee->bank_routing_number,
            'tax_id' => $employee->tax_id,
            'social_security_number' => $employee->social_security_number,
            'vacation_days_total' => $employee->vacation_days_total,
            'vacation_days_used' => $employee->vacation_days_used,
            'remaining_vacation_days' => $employee->remaining_vacation_days,
            'sick_days_total' => $employee->sick_days_total,
            'sick_days_used' => $employee->sick_days_used,
            'remaining_sick_days' => $employee->remaining_sick_days,
            'benefits_enrolled' => $employee->benefits_enrolled,
            'skills' => $employee->skills,
            'certifications' => $employee->certifications,
            'training_completed' => $employee->training_completed,
            'notes' => $employee->notes,
            'created_at' => $employee->created_at?->toISOString(),
            'updated_at' => $employee->updated_at?->toISOString(),

            // Calculated fields
            'is_active' => $employee->isActive(),
            'is_manager' => $employee->isManager(),
            'has_manager' => $employee->hasManager(),
            'has_performance_rating' => $employee->hasPerformanceRating(),
            'is_top_performer' => $employee->isTopPerformer(),
            'needs_performance_review' => $employee->needsPerformanceReview(),
            'has_vacation_days' => $employee->hasVacationDays(),
            'has_sick_days' => $employee->hasSickDays(),
            'has_low_vacation_days' => $employee->hasLowVacationDays(),
            'has_low_sick_days' => $employee->hasLowSickDays(),
            'days_of_service' => $employee->days_of_service,
            'years_of_service' => $employee->years_of_service,

            // Formatted fields
            'formatted_hire_date' => $employee->getFormattedHireDate(),
            'formatted_termination_date' => $employee->getFormattedTerminationDate(),
            'formatted_next_review_date' => $employee->getFormattedNextReviewDate(),
            'formatted_salary' => $employee->getFormattedSalary(),
            'formatted_hourly_rate' => $employee->getFormattedHourlyRate(),
            'formatted_performance_rating' => $employee->getFormattedPerformanceRating(),

            // Relationships (conditional inclusion)
            'user' => $this->when($employee->user, function () use ($employee) {
                return [
                    'id' => $employee->user->id,
                    'name' => $employee->user->name,
                    'email' => $employee->user->email,
                ];
            }),

            'manager' => $this->when($employee->manager, function () use ($employee) {
                return [
                    'id' => $employee->manager->id,
                    'employee_number' => $employee->manager->employee_number,
                    'full_name' => $employee->manager->full_name,
                    'email' => $employee->manager->email,
                    'position' => $employee->manager->position,
                    'department' => $employee->manager->department,
                ];
            }),

            'subordinates' => $this->when($employee->subordinates, function () use ($employee) {
                return $employee->subordinates->map(function ($subordinate) {
                    return [
                        'id' => $subordinate->id,
                        'employee_number' => $subordinate->employee_number,
                        'full_name' => $subordinate->full_name,
                        'email' => $subordinate->email,
                        'position' => $subordinate->position,
                        'department' => $subordinate->department,
                        'status' => $subordinate->status,
                    ];
                });
            }),

            'employee_notes' => $this->when($employee->employee_notes, function () use ($employee) {
                return $employee->employee_notes->map(function ($note) {
                    return [
                        'id' => $note->id,
                        'note' => $note->note,
                        'type' => $note->type,
                        'created_at' => $note->created_at?->toISOString(),
                        'updated_at' => $note->updated_at?->toISOString(),
                    ];
                });
            }),

            // Links
            'links' => [
                'self' => route('api.v1.employees.show', $employee->id),
                'edit' => route('api.v1.employees.update', $employee->id),
                'delete' => route('api.v1.employees.destroy', $employee->id),
                'activate' => route('api.v1.employees.activate', $employee->id),
                'deactivate' => route('api.v1.employees.deactivate', $employee->id),
                'terminate' => route('api.v1.employees.terminate', $employee->id),
                'rehire' => route('api.v1.employees.rehire', $employee->id),
                'update_salary' => route('api.v1.employees.update-salary', $employee->id),
                'update_position' => route('api.v1.employees.update-position', $employee->id),
                'update_department' => route('api.v1.employees.update-department', $employee->id),
                'update_performance' => route('api.v1.employees.update-performance', $employee->id),
                'schedule_review' => route('api.v1.employees.schedule-review', $employee->id),
                'request_time_off' => route('api.v1.employees.request-time-off', $employee->id),
                'enroll_benefits' => route('api.v1.employees.enroll-benefits', $employee->id),
                'unenroll_benefits' => route('api.v1.employees.unenroll-benefits', $employee->id),
                'assign_manager' => route('api.v1.employees.assign-manager', $employee->id),
                'remove_manager' => route('api.v1.employees.remove-manager', $employee->id),
                'add_note' => route('api.v1.employees.add-note', $employee->id),
                'update_skills' => route('api.v1.employees.update-skills', $employee->id),
                'update_certifications' => route('api.v1.employees.update-certifications', $employee->id),
                'update_training' => route('api.v1.employees.update-training', $employee->id),
                'subordinates' => route('api.v1.employees.subordinates', $employee->id),
                'managers' => route('api.v1.employees.managers', $employee->id),
                'hierarchy' => route('api.v1.employees.hierarchy', $employee->id),
            ],

            // Meta information
            'meta' => [
                'can_edit' => auth()->user()?->can('employees.update', $employee),
                'can_delete' => auth()->user()?->can('employees.delete', $employee),
                'can_activate' => auth()->user()?->can('employees.activate', $employee),
                'can_deactivate' => auth()->user()?->can('employees.deactivate', $employee),
                'can_terminate' => auth()->user()?->can('employees.terminate', $employee),
                'can_manage_salary' => auth()->user()?->can('employees.manageSalary', $employee),
                'can_manage_performance' => auth()->user()?->can('employees.managePerformance', $employee),
                'can_manage_time_off' => auth()->user()?->can('employees.manageTimeOff', $employee),
                'can_manage_benefits' => auth()->user()?->can('employees.manageBenefits', $employee),
                'can_manage_hierarchy' => auth()->user()?->can('employees.manageHierarchy', $employee),
                'can_view_sensitive_data' => auth()->user()?->can('employees.viewSensitiveData', $employee),
            ],
        ];
    }
}

