<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\DTOs\EmployeeDTO;
use Illuminate\Database\Eloquent\Collection;

trait HasEmployeeBenefitsManagement
{
    // Benefits enrollment management
    public function enrollEmployeeInBenefits(Employee $employee): bool
    {
        return $this->repository->update($employee, ['benefits_enrolled' => true]);
    }

    public function unenrollEmployeeFromBenefits(Employee $employee): bool
    {
        return $this->repository->update($employee, ['benefits_enrolled' => false]);
    }

    public function isEmployeeEnrolledInBenefits(Employee $employee): bool
    {
        return $employee->benefits_enrolled;
    }

    public function isEmployeeEnrolledInBenefitsById(int $employeeId): bool
    {
        $employee = $this->repository->find($employeeId);
        return $employee ? $employee->benefits_enrolled : false;
    }

    // Benefits queries
    public function getEmployeesEnrolledInBenefits(): Collection
    {
        return $this->repository->findActive()
            ->filter(fn($employee) => $employee->benefits_enrolled);
    }

    public function getEmployeesEnrolledInBenefitsDTO(): Collection
    {
        return $this->getEmployeesEnrolledInBenefits()
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesNotEnrolledInBenefits(): Collection
    {
        return $this->repository->findActive()
            ->filter(fn($employee) => !$employee->benefits_enrolled);
    }

    public function getEmployeesNotEnrolledInBenefitsDTO(): Collection
    {
        return $this->getEmployeesNotEnrolledInBenefits()
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesEnrolledInBenefitsByDepartment(string $department): Collection
    {
        return $this->repository->findByDepartment($department)
            ->filter(fn($employee) => $employee->benefits_enrolled);
    }

    public function getEmployeesEnrolledInBenefitsByDepartmentDTO(string $department): Collection
    {
        return $this->getEmployeesEnrolledInBenefitsByDepartment($department)
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesEnrolledInBenefitsByEmploymentType(string $employmentType): Collection
    {
        return $this->repository->findByEmploymentType($employmentType)
            ->filter(fn($employee) => $employee->benefits_enrolled);
    }

    public function getEmployeesEnrolledInBenefitsByEmploymentTypeDTO(string $employmentType): Collection
    {
        return $this->getEmployeesEnrolledInBenefitsByEmploymentType($employmentType)
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    // Benefits eligibility
    public function isEmployeeEligibleForBenefits(Employee $employee): bool
    {
        // Check if employee is active and meets eligibility criteria
        if (!$employee->isActive()) {
            return false;
        }

        // Check employment type eligibility
        if (!$employee->employment_type->isPermanent()) {
            return false;
        }

        // Check minimum service requirement (e.g., 90 days)
        $daysOfService = $employee->days_of_service;
        if ($daysOfService < 90) {
            return false;
        }

        return true;
    }

    public function getEmployeesEligibleForBenefits(): Collection
    {
        return $this->repository->findActive()
            ->filter(fn($employee) => $this->isEmployeeEligibleForBenefits($employee));
    }

    public function getEmployeesEligibleForBenefitsDTO(): Collection
    {
        return $this->getEmployeesEligibleForBenefits()
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesNotEligibleForBenefits(): Collection
    {
        return $this->repository->findActive()
            ->filter(fn($employee) => !$this->isEmployeeEligibleForBenefits($employee));
    }

    public function getEmployeesNotEligibleForBenefitsDTO(): Collection
    {
        return $this->getEmployeesNotEligibleForBenefits()
            ->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    // Benefits analytics
    public function getBenefitsEnrollmentStats(): array
    {
        $employees = $this->repository->findActive();
        $totalEmployees = $employees->count();

        if ($totalEmployees === 0) {
            return [
                'total_employees' => 0,
                'enrolled_count' => 0,
                'not_enrolled_count' => 0,
                'enrollment_rate' => 0,
                'eligible_count' => 0,
                'not_eligible_count' => 0,
                'eligible_enrollment_rate' => 0
            ];
        }

        $enrolledCount = $employees->filter(fn($e) => $e->benefits_enrolled)->count();
        $eligibleCount = $employees->filter(fn($e) => $this->isEmployeeEligibleForBenefits($e))->count();
        $eligibleEnrolledCount = $employees->filter(fn($e) => $this->isEmployeeEligibleForBenefits($e) && $e->benefits_enrolled)->count();

        return [
            'total_employees' => $totalEmployees,
            'enrolled_count' => $enrolledCount,
            'not_enrolled_count' => $totalEmployees - $enrolledCount,
            'enrollment_rate' => round(($enrolledCount / $totalEmployees) * 100, 2),
            'eligible_count' => $eligibleCount,
            'not_eligible_count' => $totalEmployees - $eligibleCount,
            'eligible_enrollment_rate' => $eligibleCount > 0 ? round(($eligibleEnrolledCount / $eligibleCount) * 100, 2) : 0
        ];
    }

    public function getBenefitsEnrollmentStatsByDepartment(): array
    {
        $departments = $this->repository->findActive()
            ->pluck('department')
            ->unique()
            ->filter();

        $stats = [];

        foreach ($departments as $department) {
            $employees = $this->repository->findByDepartment($department);
            $totalEmployees = $employees->count();

            if ($totalEmployees === 0) {
                continue;
            }

            $enrolledCount = $employees->filter(fn($e) => $e->benefits_enrolled)->count();
            $eligibleCount = $employees->filter(fn($e) => $this->isEmployeeEligibleForBenefits($e))->count();
            $eligibleEnrolledCount = $employees->filter(fn($e) => $this->isEmployeeEligibleForBenefits($e) && $e->benefits_enrolled)->count();

            $stats[$department] = [
                'total_employees' => $totalEmployees,
                'enrolled_count' => $enrolledCount,
                'not_enrolled_count' => $totalEmployees - $enrolledCount,
                'enrollment_rate' => round(($enrolledCount / $totalEmployees) * 100, 2),
                'eligible_count' => $eligibleCount,
                'not_eligible_count' => $totalEmployees - $eligibleCount,
                'eligible_enrollment_rate' => $eligibleCount > 0 ? round(($eligibleEnrolledCount / $eligibleCount) * 100, 2) : 0
            ];
        }

        return $stats;
    }

    public function getBenefitsEnrollmentStatsByEmploymentType(): array
    {
        $employmentTypes = $this->repository->findActive()
            ->pluck('employment_type')
            ->unique()
            ->filter();

        $stats = [];

        foreach ($employmentTypes as $employmentType) {
            $employees = $this->repository->findByEmploymentType($employmentType->value);
            $totalEmployees = $employees->count();

            if ($totalEmployees === 0) {
                continue;
            }

            $enrolledCount = $employees->filter(fn($e) => $e->benefits_enrolled)->count();
            $eligibleCount = $employees->filter(fn($e) => $this->isEmployeeEligibleForBenefits($e))->count();
            $eligibleEnrolledCount = $employees->filter(fn($e) => $this->isEmployeeEligibleForBenefits($e) && $e->benefits_enrolled)->count();

            $stats[$employmentType->value] = [
                'employment_type' => $employmentType->label(),
                'total_employees' => $totalEmployees,
                'enrolled_count' => $enrolledCount,
                'not_enrolled_count' => $totalEmployees - $enrolledCount,
                'enrollment_rate' => round(($enrolledCount / $totalEmployees) * 100, 2),
                'eligible_count' => $eligibleCount,
                'not_eligible_count' => $totalEmployees - $eligibleCount,
                'eligible_enrollment_rate' => $eligibleCount > 0 ? round(($eligibleEnrolledCount / $eligibleCount) * 100, 2) : 0
            ];
        }

        return $stats;
    }

    // Benefits policy management
    public function getBenefitsPolicy(Employee $employee): array
    {
        $policy = [
            'employee_id' => $employee->id,
            'employee_name' => $employee->full_name,
            'employment_type' => $employee->employment_type->value,
            'department' => $employee->department,
            'hire_date' => $employee->hire_date->format('Y-m-d'),
            'days_of_service' => $employee->days_of_service,
            'is_eligible' => $this->isEmployeeEligibleForBenefits($employee),
            'is_enrolled' => $employee->benefits_enrolled,
            'eligibility_criteria' => [
                'minimum_service_days' => 90,
                'employment_type_requirement' => 'permanent',
                'status_requirement' => 'active'
            ],
            'available_benefits' => [
                'health_insurance' => [
                    'available' => $this->isEmployeeEligibleForBenefits($employee),
                    'enrolled' => $employee->benefits_enrolled,
                    'coverage_start_date' => $employee->benefits_enrolled ? $employee->hire_date->addDays(90)->format('Y-m-d') : null
                ],
                'dental_insurance' => [
                    'available' => $this->isEmployeeEligibleForBenefits($employee),
                    'enrolled' => $employee->benefits_enrolled,
                    'coverage_start_date' => $employee->benefits_enrolled ? $employee->hire_date->addDays(90)->format('Y-m-d') : null
                ],
                'vision_insurance' => [
                    'available' => $this->isEmployeeEligibleForBenefits($employee),
                    'enrolled' => $employee->benefits_enrolled,
                    'coverage_start_date' => $employee->benefits_enrolled ? $employee->hire_date->addDays(90)->format('Y-m-d') : null
                ],
                'life_insurance' => [
                    'available' => $this->isEmployeeEligibleForBenefits($employee),
                    'enrolled' => $employee->benefits_enrolled,
                    'coverage_amount' => $employee->benefits_enrolled ? '1x annual salary' : null
                ],
                'retirement_plan' => [
                    'available' => $this->isEmployeeEligibleForBenefits($employee),
                    'enrolled' => $employee->benefits_enrolled,
                    'employer_match' => $employee->benefits_enrolled ? '3%' : null
                ]
            ]
        ];

        return $policy;
    }

    // Benefits enrollment workflow
    public function processBenefitsEnrollment(Employee $employee): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'enrollment_date' => null,
            'coverage_start_date' => null
        ];

        if (!$this->isEmployeeEligibleForBenefits($employee)) {
            $result['message'] = 'Employee is not eligible for benefits';
            return $result;
        }

        if ($employee->benefits_enrolled) {
            $result['message'] = 'Employee is already enrolled in benefits';
            return $result;
        }

        $enrollmentSuccess = $this->enrollEmployeeInBenefits($employee);

        if ($enrollmentSuccess) {
            $result['success'] = true;
            $result['message'] = 'Employee successfully enrolled in benefits';
            $result['enrollment_date'] = now()->format('Y-m-d');
            $result['coverage_start_date'] = $employee->hire_date->addDays(90)->format('Y-m-d');
        } else {
            $result['message'] = 'Failed to enroll employee in benefits';
        }

        return $result;
    }

    public function processBenefitsUnenrollment(Employee $employee): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'unenrollment_date' => null
        ];

        if (!$employee->benefits_enrolled) {
            $result['message'] = 'Employee is not enrolled in benefits';
            return $result;
        }

        $unenrollmentSuccess = $this->unenrollEmployeeFromBenefits($employee);

        if ($unenrollmentSuccess) {
            $result['success'] = true;
            $result['message'] = 'Employee successfully unenrolled from benefits';
            $result['unenrollment_date'] = now()->format('Y-m-d');
        } else {
            $result['message'] = 'Failed to unenroll employee from benefits';
        }

        return $result;
    }

    // Benefits reporting
    public function generateBenefitsReport(string $department = null, string $period = 'current'): array
    {
        $employees = $department
            ? $this->repository->findByDepartment($department)
            : $this->repository->findActive();

        $report = [
            'period' => $period,
            'department' => $department,
            'total_employees' => $employees->count(),
            'enrollment_stats' => $this->getBenefitsEnrollmentStats(),
            'eligible_employees' => $employees->filter(fn($e) => $this->isEmployeeEligibleForBenefits($e))->count(),
            'enrolled_employees' => $employees->filter(fn($e) => $e->benefits_enrolled)->count(),
            'not_enrolled_employees' => $employees->filter(fn($e) => !$e->benefits_enrolled)->count(),
            'enrollment_rate' => $employees->count() > 0
                ? round(($employees->filter(fn($e) => $e->benefits_enrolled)->count() / $employees->count()) * 100, 2)
                : 0,
            'eligible_enrollment_rate' => $employees->filter(fn($e) => $this->isEmployeeEligibleForBenefits($e))->count() > 0
                ? round(($employees->filter(fn($e) => $this->isEmployeeEligibleForBenefits($e) && $e->benefits_enrolled)->count() / $employees->filter(fn($e) => $this->isEmployeeEligibleForBenefits($e))->count()) * 100, 2)
                : 0
        ];

        return $report;
    }
}

