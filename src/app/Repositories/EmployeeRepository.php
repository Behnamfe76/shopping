<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\DTOs\EmployeeDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeRepositoryInterface;
use Fereydooni\Shopping\app\Enums\EmployeeStatus;
use Fereydooni\Shopping\app\Enums\EmploymentType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function all(): Collection
    {
        return Employee::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Employee::paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return Employee::simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return Employee::cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function find(int $id): ?Employee
    {
        return Employee::find($id);
    }

    public function findDTO(int $id): ?EmployeeDTO
    {
        $employee = $this->find($id);
        return $employee ? EmployeeDTO::fromModel($employee) : null;
    }

    public function findByUserId(int $userId): ?Employee
    {
        return Employee::where('user_id', $userId)->first();
    }

    public function findByUserIdDTO(int $userId): ?EmployeeDTO
    {
        $employee = $this->findByUserId($userId);
        return $employee ? EmployeeDTO::fromModel($employee) : null;
    }

    public function findByEmail(string $email): ?Employee
    {
        return Employee::where('email', $email)->first();
    }

    public function findByEmailDTO(string $email): ?EmployeeDTO
    {
        $employee = $this->findByEmail($email);
        return $employee ? EmployeeDTO::fromModel($employee) : null;
    }

    public function findByPhone(string $phone): ?Employee
    {
        return Employee::where('phone', $phone)->first();
    }

    public function findByPhoneDTO(string $phone): ?EmployeeDTO
    {
        $employee = $this->findByPhone($phone);
        return $employee ? EmployeeDTO::fromModel($employee) : null;
    }

    public function findByEmployeeNumber(string $employeeNumber): ?Employee
    {
        return Employee::where('employee_number', $employeeNumber)->first();
    }

    public function findByEmployeeNumberDTO(string $employeeNumber): ?EmployeeDTO
    {
        $employee = $this->findByEmployeeNumber($employeeNumber);
        return $employee ? EmployeeDTO::fromModel($employee) : null;
    }

    public function findByStatus(string $status): Collection
    {
        return Employee::where('status', $status)->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        return Employee::where('status', $status)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function findActive(): Collection
    {
        return Employee::active()->get();
    }

    public function findActiveDTO(): Collection
    {
        return Employee::active()->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function findInactive(): Collection
    {
        return Employee::inactive()->get();
    }

    public function findInactiveDTO(): Collection
    {
        return Employee::inactive()->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function findTerminated(): Collection
    {
        return Employee::terminated()->get();
    }

    public function findTerminatedDTO(): Collection
    {
        return Employee::terminated()->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function findByEmploymentType(string $employmentType): Collection
    {
        return Employee::where('employment_type', $employmentType)->get();
    }

    public function findByEmploymentTypeDTO(string $employmentType): Collection
    {
        return Employee::where('employment_type', $employmentType)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function findByDepartment(string $department): Collection
    {
        return Employee::byDepartment($department)->get();
    }

    public function findByDepartmentDTO(string $department): Collection
    {
        return Employee::byDepartment($department)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function findByPosition(string $position): Collection
    {
        return Employee::byPosition($position)->get();
    }

    public function findByPositionDTO(string $position): Collection
    {
        return Employee::byPosition($position)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function findByManagerId(int $managerId): Collection
    {
        return Employee::byManager($managerId)->get();
    }

    public function findByManagerIdDTO(int $managerId): Collection
    {
        return Employee::byManager($managerId)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function findByHireDateRange(string $startDate, string $endDate): Collection
    {
        return Employee::byHireDateRange($startDate, $endDate)->get();
    }

    public function findByHireDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return Employee::byHireDateRange($startDate, $endDate)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function findBySalaryRange(float $minSalary, float $maxSalary): Collection
    {
        return Employee::bySalaryRange($minSalary, $maxSalary)->get();
    }

    public function findBySalaryRangeDTO(float $minSalary, float $maxSalary): Collection
    {
        return Employee::bySalaryRange($minSalary, $maxSalary)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function findByPerformanceRating(float $minRating, float $maxRating): Collection
    {
        return Employee::byPerformanceRange($minRating, $maxRating)->get();
    }

    public function findByPerformanceRatingDTO(float $minRating, float $maxRating): Collection
    {
        return Employee::byPerformanceRange($minRating, $maxRating)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function create(array $data): Employee
    {
        // Generate employee number if not provided
        if (!isset($data['employee_number'])) {
            $data['employee_number'] = $this->generateEmployeeNumber();
        }

        // Set default values
        $data['status'] = $data['status'] ?? EmployeeStatus::PENDING;
        $data['benefits_enrolled'] = $data['benefits_enrolled'] ?? false;
        $data['vacation_days_used'] = $data['vacation_days_used'] ?? 0;
        $data['sick_days_used'] = $data['sick_days_used'] ?? 0;

        // Set default vacation and sick days based on employment type
        if (!isset($data['vacation_days_total'])) {
            $employmentType = EmploymentType::from($data['employment_type']);
            $data['vacation_days_total'] = $employmentType->getDefaultVacationDays();
        }

        if (!isset($data['sick_days_total'])) {
            $employmentType = EmploymentType::from($data['employment_type']);
            $data['sick_days_total'] = $employmentType->getDefaultSickDays();
        }

        return Employee::create($data);
    }

    public function createAndReturnDTO(array $data): EmployeeDTO
    {
        $employee = $this->create($data);
        return EmployeeDTO::fromModel($employee);
    }

    public function update(Employee $employee, array $data): bool
    {
        return $employee->update($data);
    }

    public function updateAndReturnDTO(Employee $employee, array $data): ?EmployeeDTO
    {
        $updated = $this->update($employee, $data);
        return $updated ? EmployeeDTO::fromModel($employee->fresh()) : null;
    }

    public function delete(Employee $employee): bool
    {
        return $employee->delete();
    }

    public function activate(Employee $employee): bool
    {
        return $employee->update(['status' => EmployeeStatus::ACTIVE]);
    }

    public function deactivate(Employee $employee): bool
    {
        return $employee->update(['status' => EmployeeStatus::INACTIVE]);
    }

    public function terminate(Employee $employee, string $reason = null, string $terminationDate = null): bool
    {
        $data = ['status' => EmployeeStatus::TERMINATED];

        if ($terminationDate) {
            $data['termination_date'] = $terminationDate;
        } else {
            $data['termination_date'] = now();
        }

        if ($reason) {
            $data['notes'] = ($employee->notes ? $employee->notes . "\n" : '') . "Termination reason: {$reason}";
        }

        return $employee->update($data);
    }

    public function rehire(Employee $employee, string $hireDate = null): bool
    {
        $data = [
            'status' => EmployeeStatus::ACTIVE,
            'termination_date' => null
        ];

        if ($hireDate) {
            $data['hire_date'] = $hireDate;
        }

        return $employee->update($data);
    }

    public function updateSalary(Employee $employee, float $newSalary, string $effectiveDate = null): bool
    {
        $data = ['salary' => $newSalary];

        if ($effectiveDate) {
            $data['notes'] = ($employee->notes ? $employee->notes . "\n" : '') . "Salary updated to {$newSalary} effective {$effectiveDate}";
        }

        return $employee->update($data);
    }

    public function updatePosition(Employee $employee, string $newPosition, string $effectiveDate = null): bool
    {
        $data = ['position' => $newPosition];

        if ($effectiveDate) {
            $data['notes'] = ($employee->notes ? $employee->notes . "\n" : '') . "Position updated to {$newPosition} effective {$effectiveDate}";
        }

        return $employee->update($data);
    }

    public function updateDepartment(Employee $employee, string $newDepartment, string $effectiveDate = null): bool
    {
        $data = ['department' => $newDepartment];

        if ($effectiveDate) {
            $data['notes'] = ($employee->notes ? $employee->notes . "\n" : '') . "Department updated to {$newDepartment} effective {$effectiveDate}";
        }

        return $employee->update($data);
    }

    public function assignManager(Employee $employee, int $managerId): bool
    {
        return $employee->update(['manager_id' => $managerId]);
    }

    public function removeManager(Employee $employee): bool
    {
        return $employee->update(['manager_id' => null]);
    }

    public function addVacationDays(Employee $employee, int $days): bool
    {
        return $employee->update(['vacation_days_total' => $employee->vacation_days_total + $days]);
    }

    public function useVacationDays(Employee $employee, int $days): bool
    {
        if ($employee->remaining_vacation_days < $days) {
            return false;
        }

        return $employee->update(['vacation_days_used' => $employee->vacation_days_used + $days]);
    }

    public function addSickDays(Employee $employee, int $days): bool
    {
        return $employee->update(['sick_days_total' => $employee->sick_days_total + $days]);
    }

    public function useSickDays(Employee $employee, int $days): bool
    {
        if ($employee->remaining_sick_days < $days) {
            return false;
        }

        return $employee->update(['sick_days_used' => $employee->sick_days_used + $days]);
    }

    public function updatePerformanceRating(Employee $employee, float $rating, string $reviewDate = null): bool
    {
        $data = [
            'performance_rating' => $rating,
            'last_review_date' => $reviewDate ?: now(),
            'next_review_date' => now()->addYear()
        ];

        return $employee->update($data);
    }

    public function getEmployeeCount(): int
    {
        return Employee::count();
    }

    public function getEmployeeCountByStatus(string $status): int
    {
        return Employee::where('status', $status)->count();
    }

    public function getEmployeeCountByDepartment(string $department): int
    {
        return Employee::byDepartment($department)->count();
    }

    public function getEmployeeCountByEmploymentType(string $employmentType): int
    {
        return Employee::where('employment_type', $employmentType)->count();
    }

    public function getActiveEmployeeCount(): int
    {
        return Employee::active()->count();
    }

    public function getInactiveEmployeeCount(): int
    {
        return Employee::inactive()->count();
    }

    public function getTerminatedEmployeeCount(): int
    {
        return Employee::terminated()->count();
    }

    public function getTotalSalary(): float
    {
        return Employee::whereNotNull('salary')->sum('salary');
    }

    public function getAverageSalary(): float
    {
        return Employee::whereNotNull('salary')->avg('salary') ?? 0;
    }

    public function getTotalSalaryByDepartment(string $department): float
    {
        return Employee::byDepartment($department)->whereNotNull('salary')->sum('salary');
    }

    public function getAverageSalaryByDepartment(string $department): float
    {
        return Employee::byDepartment($department)->whereNotNull('salary')->avg('salary') ?? 0;
    }

    public function getAveragePerformanceRating(): float
    {
        return Employee::whereNotNull('performance_rating')->avg('performance_rating') ?? 0;
    }

    public function getAveragePerformanceRatingByDepartment(string $department): float
    {
        return Employee::byDepartment($department)->whereNotNull('performance_rating')->avg('performance_rating') ?? 0;
    }

    public function search(string $query): Collection
    {
        return Employee::search($query)->get();
    }

    public function searchDTO(string $query): Collection
    {
        return Employee::search($query)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function searchByDepartment(string $department, string $query): Collection
    {
        return Employee::byDepartment($department)->search($query)->get();
    }

    public function searchByDepartmentDTO(string $department, string $query): Collection
    {
        return Employee::byDepartment($department)->search($query)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function searchByPosition(string $position, string $query): Collection
    {
        return Employee::byPosition($position)->search($query)->get();
    }

    public function searchByPositionDTO(string $position, string $query): Collection
    {
        return Employee::byPosition($position)->search($query)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getTopPerformers(int $limit = 10): Collection
    {
        return Employee::topPerformers()->orderBy('performance_rating', 'desc')->limit($limit)->get();
    }

    public function getTopPerformersDTO(int $limit = 10): Collection
    {
        return Employee::topPerformers()->orderBy('performance_rating', 'desc')->limit($limit)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getLongestServing(int $limit = 10): Collection
    {
        return Employee::orderBy('hire_date', 'asc')->limit($limit)->get();
    }

    public function getLongestServingDTO(int $limit = 10): Collection
    {
        return Employee::orderBy('hire_date', 'asc')->limit($limit)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getNewestHires(int $limit = 10): Collection
    {
        return Employee::orderBy('hire_date', 'desc')->limit($limit)->get();
    }

    public function getNewestHiresDTO(int $limit = 10): Collection
    {
        return Employee::orderBy('hire_date', 'desc')->limit($limit)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesBySalaryRange(float $minSalary, float $maxSalary): Collection
    {
        return Employee::bySalaryRange($minSalary, $maxSalary)->get();
    }

    public function getEmployeesBySalaryRangeDTO(float $minSalary, float $maxSalary): Collection
    {
        return Employee::bySalaryRange($minSalary, $maxSalary)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesWithUpcomingReviews(int $daysAhead = 30): Collection
    {
        return Employee::withUpcomingReviews($daysAhead)->get();
    }

    public function getEmployeesWithUpcomingReviewsDTO(int $daysAhead = 30): Collection
    {
        return Employee::withUpcomingReviews($daysAhead)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesWithLowVacationDays(int $threshold = 5): Collection
    {
        return Employee::withLowVacationDays($threshold)->get();
    }

    public function getEmployeesWithLowVacationDaysDTO(int $threshold = 5): Collection
    {
        return Employee::withLowVacationDays($threshold)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesByCertification(string $certification): Collection
    {
        return Employee::whereJsonContains('certifications', $certification)->get();
    }

    public function getEmployeesByCertificationDTO(string $certification): Collection
    {
        return Employee::whereJsonContains('certifications', $certification)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeesBySkill(string $skill): Collection
    {
        return Employee::whereJsonContains('skills', $skill)->get();
    }

    public function getEmployeesBySkillDTO(string $skill): Collection
    {
        return Employee::whereJsonContains('skills', $skill)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function validateEmployee(array $data): bool
    {
        // Basic validation logic
        $required = ['user_id', 'first_name', 'last_name', 'email', 'employee_number', 'position', 'department', 'employment_type'];

        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return false;
            }
        }

        // Email validation
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Employee number uniqueness
        if (!$this->isEmployeeNumberUnique($data['employee_number'])) {
            return false;
        }

        return true;
    }

    public function generateEmployeeNumber(): string
    {
        do {
            $number = 'EMP' . strtoupper(Str::random(6));
        } while (!$this->isEmployeeNumberUnique($number));

        return $number;
    }

    public function isEmployeeNumberUnique(string $employeeNumber): bool
    {
        return !Employee::where('employee_number', $employeeNumber)->exists();
    }

    public function getEmployeeStats(): array
    {
        return [
            'total' => $this->getEmployeeCount(),
            'active' => $this->getActiveEmployeeCount(),
            'inactive' => $this->getInactiveEmployeeCount(),
            'terminated' => $this->getTerminatedEmployeeCount(),
            'average_salary' => $this->getAverageSalary(),
            'total_salary' => $this->getTotalSalary(),
            'average_performance' => $this->getAveragePerformanceRating(),
        ];
    }

    public function getEmployeeStatsByStatus(): array
    {
        $stats = [];
        foreach (EmployeeStatus::cases() as $status) {
            $stats[$status->value] = $this->getEmployeeCountByStatus($status->value);
        }
        return $stats;
    }

    public function getEmployeeStatsByDepartment(): array
    {
        $departments = Employee::distinct()->pluck('department')->filter();
        $stats = [];

        foreach ($departments as $department) {
            $stats[$department] = [
                'count' => $this->getEmployeeCountByDepartment($department),
                'average_salary' => $this->getAverageSalaryByDepartment($department),
                'average_performance' => $this->getAveragePerformanceRatingByDepartment($department),
            ];
        }

        return $stats;
    }

    public function getEmployeeStatsByEmploymentType(): array
    {
        $stats = [];
        foreach (EmploymentType::cases() as $type) {
            $stats[$type->value] = $this->getEmployeeCountByEmploymentType($type->value);
        }
        return $stats;
    }

    public function getEmployeeGrowthStats(string $period = 'monthly'): array
    {
        // Implementation for employee growth statistics
        return [];
    }

    public function getEmployeeTurnoverStats(): array
    {
        // Implementation for employee turnover statistics
        return [];
    }

    public function getEmployeeRetentionStats(): array
    {
        // Implementation for employee retention statistics
        return [];
    }

    public function getEmployeePerformanceStats(): array
    {
        // Implementation for employee performance statistics
        return [];
    }

    public function getEmployeeSalaryStats(): array
    {
        // Implementation for employee salary statistics
        return [];
    }

    public function getEmployeeTimeOffStats(): array
    {
        // Implementation for employee time-off statistics
        return [];
    }

    public function getEmployeeHierarchy(int $employeeId): array
    {
        $employee = $this->find($employeeId);
        if (!$employee) {
            return [];
        }

        $hierarchy = [
            'employee' => $employee,
            'manager' => $employee->manager,
            'subordinates' => $employee->subordinates,
        ];

        return $hierarchy;
    }

    public function getEmployeeSubordinates(int $employeeId): Collection
    {
        return Employee::byManager($employeeId)->get();
    }

    public function getEmployeeSubordinatesDTO(int $employeeId): Collection
    {
        return Employee::byManager($employeeId)->get()->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function getEmployeeManagers(int $employeeId): Collection
    {
        $employee = $this->find($employeeId);
        if (!$employee || !$employee->manager_id) {
            return collect();
        }

        $managers = collect();
        $currentManager = $employee->manager;

        while ($currentManager) {
            $managers->push($currentManager);
            $currentManager = $currentManager->manager;
        }

        return $managers;
    }

    public function getEmployeeManagersDTO(int $employeeId): Collection
    {
        return $this->getEmployeeManagers($employeeId)->map(fn($employee) => EmployeeDTO::fromModel($employee));
    }

    public function addEmployeeNote(Employee $employee, string $note, string $type = 'general'): bool
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $formattedNote = "[{$timestamp}] [{$type}] {$note}";

        $currentNotes = $employee->notes ?: '';
        $newNotes = $currentNotes ? $currentNotes . "\n" . $formattedNote : $formattedNote;

        return $employee->update(['notes' => $newNotes]);
    }

    public function getEmployeeNotes(Employee $employee): Collection
    {
        // This would typically return a separate notes table
        // For now, we'll return the notes as a collection
        if (!$employee->notes) {
            return collect();
        }

        $notes = explode("\n", $employee->notes);
        return collect($notes)->filter()->map(function ($note) {
            // Parse note format: [timestamp] [type] content
            if (preg_match('/^\[(.*?)\] \[(.*?)\] (.*)$/', $note, $matches)) {
                return [
                    'timestamp' => $matches[1],
                    'type' => $matches[2],
                    'content' => $matches[3],
                ];
            }

            return [
                'timestamp' => null,
                'type' => 'general',
                'content' => $note,
            ];
        });
    }

    public function updateEmployeeBenefits(Employee $employee, array $benefits): bool
    {
        return $employee->update(['benefits_enrolled' => true]);
    }

    public function getEmployeeBenefits(int $employeeId): array
    {
        $employee = $this->find($employeeId);
        return $employee && $employee->benefits_enrolled ? ['enrolled' => true] : [];
    }

    public function updateEmployeeSkills(Employee $employee, array $skills): bool
    {
        return $employee->update(['skills' => $skills]);
    }

    public function getEmployeeSkills(int $employeeId): array
    {
        $employee = $this->find($employeeId);
        return $employee ? ($employee->skills ?? []) : [];
    }

    public function updateEmployeeCertifications(Employee $employee, array $certifications): bool
    {
        return $employee->update(['certifications' => $certifications]);
    }

    public function getEmployeeCertifications(int $employeeId): array
    {
        $employee = $this->find($employeeId);
        return $employee ? ($employee->certifications ?? []) : [];
    }
}

