<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Fereydooni\Shopping\app\Traits\HasEmployeeOperations;
use Fereydooni\Shopping\app\Traits\HasEmployeeStatusManagement;
use Fereydooni\Shopping\app\Traits\HasEmployeePerformanceManagement;
use Fereydooni\Shopping\app\Traits\HasEmployeeTimeOffManagement;
use Fereydooni\Shopping\app\Traits\HasEmployeeBenefitsManagement;
use Fereydooni\Shopping\app\Traits\HasEmployeeHierarchyManagement;
use Fereydooni\Shopping\app\Traits\HasEmployeeAnalytics;
use Fereydooni\Shopping\app\DTOs\EmployeeDTO;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Enums\EmployeeStatus;
use Fereydooni\Shopping\app\Enums\EmploymentType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class EmployeeService
{
    use HasCrudOperations,
        HasSearchOperations,
        HasEmployeeOperations,
        HasEmployeeStatusManagement,
        HasEmployeePerformanceManagement,
        HasEmployeeTimeOffManagement,
        HasEmployeeBenefitsManagement,
        HasEmployeeHierarchyManagement,
        HasEmployeeAnalytics;

    public function __construct(
        private EmployeeRepositoryInterface $repository
    ) {
        $this->model = Employee::class;
        $this->dtoClass = EmployeeDTO::class;
    }

    // Employee onboarding and offboarding
    public function onboardEmployee(array $data): EmployeeDTO
    {
        $data['status'] = EmployeeStatus::PENDING;

        if (!isset($data['employee_number'])) {
            $data['employee_number'] = $this->generateEmployeeNumber();
        }

        $employee = $this->createEmployee($data);

        Log::info('Employee onboarded', [
            'employee_id' => $employee->id,
            'employee_number' => $employee->employee_number
        ]);

        return EmployeeDTO::fromModel($employee);
    }

    public function completeOnboarding(Employee $employee): bool
    {
        if ($employee->status !== EmployeeStatus::PENDING) {
            return false;
        }

        return $this->activateEmployee($employee);
    }

    public function offboardEmployee(Employee $employee, string $reason = null): bool
    {
        return $this->terminateEmployee($employee, $reason);
    }

    // Employee profile management
    public function updateEmployeeProfile(Employee $employee, array $data): EmployeeDTO
    {
        unset($data['user_id'], $data['employee_number'], $data['status'], $data['salary']);

        $this->updateEmployee($employee, $data);

        return EmployeeDTO::fromModel($employee->fresh());
    }

    public function updateContactInfo(Employee $employee, array $contactData): EmployeeDTO
    {
        $allowedFields = ['email', 'phone', 'first_name', 'last_name', 'address', 'city', 'state', 'postal_code', 'country'];
        $data = array_intersect_key($contactData, array_flip($allowedFields));

        $this->updateEmployee($employee, $data);

        return EmployeeDTO::fromModel($employee->fresh());
    }

    // Salary and compensation management
    public function updateSalary(Employee $employee, float $newSalary, string $effectiveDate = null): bool
    {
        return $this->repository->updateSalary($employee, $newSalary, $effectiveDate);
    }

    public function updatePosition(Employee $employee, string $newPosition, string $effectiveDate = null): bool
    {
        return $this->repository->updatePosition($employee, $newPosition, $effectiveDate);
    }

    public function updateDepartment(Employee $employee, string $newDepartment, string $effectiveDate = null): bool
    {
        return $this->repository->updateDepartment($employee, $newDepartment, $effectiveDate);
    }

    // Performance management
    public function updatePerformanceRating(Employee $employee, float $rating, string $reviewDate = null): bool
    {
        if (!$this->isValidPerformanceRating($rating)) {
            return false;
        }

        return $this->updateEmployeePerformanceRating($employee, $rating, $reviewDate);
    }

    public function schedulePerformanceReview(Employee $employee, string $reviewDate): bool
    {
        return $this->schedulePerformanceReview($employee, $reviewDate);
    }

    // Time-off management
    public function requestTimeOff(Employee $employee, string $type, int $days, string $startDate, string $endDate, string $reason = null): bool
    {
        if ($type === 'vacation' && !$this->hasVacationDaysAvailable($employee, $days)) {
            return false;
        }

        if ($type === 'sick' && !$this->hasSickDaysAvailable($employee, $days)) {
            return false;
        }

        return $this->submitTimeOffRequest($employee, $type, $startDate, $endDate, $reason);
    }

    // Benefits administration
    public function enrollInBenefits(Employee $employee): array
    {
        return $this->processBenefitsEnrollment($employee);
    }

    public function unenrollFromBenefits(Employee $employee): array
    {
        return $this->processBenefitsUnenrollment($employee);
    }

    // Employee analytics and reporting
    public function getEmployeeDashboardData(): array
    {
        return [
            'total_employees' => $this->getEmployeeCount(),
            'active_employees' => $this->getActiveEmployeeCount(),
            'average_performance_rating' => $this->getAveragePerformanceRating(),
            'total_salary' => $this->getTotalSalary(),
            'average_salary' => $this->getAverageSalary(),
            'employees_needing_reviews' => $this->getEmployeesNeedingReviews()->count(),
            'employees_with_low_vacation' => $this->getEmployeesWithLowVacationDays(5)->count(),
            'top_performers' => $this->getTopPerformers(5)->toArray()
        ];
    }

    public function generateEmployeeReport(string $department = null, string $period = 'current'): array
    {
        return $this->generateComprehensiveAnalyticsReport($department, $period);
    }

    // Employee hierarchy management
    public function assignManager(Employee $employee, int $managerId): bool
    {
        $validation = $this->validateHierarchyAssignment($employee, $managerId);

        if (!$validation['valid']) {
            return false;
        }

        return $this->assignManager($employee, $managerId);
    }

    public function removeManager(Employee $employee): bool
    {
        return $this->removeManager($employee);
    }

    // Employee training and development
    public function updateTrainingRecords(Employee $employee, array $trainingData): bool
    {
        return $this->repository->update($employee, ['training_completed' => $trainingData]);
    }

    public function updateSkills(Employee $employee, array $skills): bool
    {
        return $this->updateEmployeeSkills($employee, $skills);
    }

    public function updateCertifications(Employee $employee, array $certifications): bool
    {
        return $this->updateEmployeeCertifications($employee, $certifications);
    }

    // Employee retention strategies
    public function identifyAtRiskEmployees(): Collection
    {
        return $this->repository->findActive()
            ->filter(function ($employee) {
                $riskFactors = 0;

                if ($employee->performance_rating && $employee->performance_rating < 3.0) {
                    $riskFactors++;
                }

                if ($employee->vacation_days_used < 5) {
                    $riskFactors++;
                }

                return $riskFactors >= 2;
            });
    }

    public function generateRetentionRecommendations(Employee $employee): array
    {
        $recommendations = [];

        if ($employee->performance_rating && $employee->performance_rating < 3.0) {
            $recommendations[] = 'Schedule performance improvement plan';
        }

        if ($employee->vacation_days_used < 5) {
            $recommendations[] = 'Encourage vacation usage to prevent burnout';
        }

        if (!$employee->skills || count($employee->skills) < 3) {
            $recommendations[] = 'Provide training and skill development opportunities';
        }

        return $recommendations;
    }

    // Employee communication management
    public function sendEmployeeNotification(Employee $employee, string $type, string $message): bool
    {
        Log::info('Employee notification sent', [
            'employee_id' => $employee->id,
            'type' => $type,
            'message' => $message
        ]);

        return true;
    }

    public function sendBulkEmployeeNotifications(Collection $employees, string $type, string $message): array
    {
        $results = [
            'total_employees' => $employees->count(),
            'successful_sends' => 0,
            'failed_sends' => 0
        ];

        foreach ($employees as $employee) {
            $success = $this->sendEmployeeNotification($employee, $type, $message);
            if ($success) {
                $results['successful_sends']++;
            } else {
                $results['failed_sends']++;
            }
        }

        return $results;
    }
}
