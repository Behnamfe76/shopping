<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\DTOs\EmployeeBenefitsDTO;
use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use App\Repositories\EmployeeBenefitsRepository;
use Fereydooni\Shopping\app\Actions\EmployeeBenefits\CreateEmployeeBenefitsAction;
use Fereydooni\Shopping\app\Actions\EmployeeBenefits\UpdateEmployeeBenefitsAction;
use Fereydooni\Shopping\app\Actions\EmployeeBenefits\EnrollEmployeeBenefitsAction;
use Fereydooni\Shopping\app\Actions\EmployeeBenefits\TerminateEmployeeBenefitsAction;
use Fereydooni\Shopping\app\Actions\EmployeeBenefits\CalculateBenefitsCostAction;
use Fereydooni\Shopping\app\Actions\EmployeeBenefits\ProcessBenefitsRenewalAction;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class EmployeeBenefits extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'employee-benefits';
    }

    /**
     * Create a new employee benefit enrollment
     */
    public static function create(array $data): EmployeeBenefitsDTO
    {
        try {
            $action = app(CreateEmployeeBenefitsAction::class);
            return $action->execute($data);
        } catch (Exception $e) {
            Log::error('Failed to create employee benefits via facade', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing employee benefit enrollment
     */
    public static function update(EmployeeBenefits $benefit, array $data): EmployeeBenefitsDTO
    {
        try {
            $action = app(UpdateEmployeeBenefitsAction::class);
            return $action->execute($benefit, $data);
        } catch (Exception $e) {
            Log::error('Failed to update employee benefits via facade', [
                'error' => $e->getMessage(),
                'benefit_id' => $benefit->id,
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Enroll an employee in benefits
     */
    public static function enroll(EmployeeBenefits $benefit, string $effectiveDate = null): EmployeeBenefitsDTO
    {
        try {
            $action = app(EnrollEmployeeBenefitsAction::class);
            return $action->execute($benefit, $effectiveDate);
        } catch (Exception $e) {
            Log::error('Failed to enroll employee in benefits via facade', [
                'error' => $e->getMessage(),
                'benefit_id' => $benefit->id
            ]);
            throw $e;
        }
    }

    /**
     * Terminate employee benefits
     */
    public static function terminate(EmployeeBenefits $benefit, string $endDate = null, string $reason = null): EmployeeBenefitsDTO
    {
        try {
            $action = app(TerminateEmployeeBenefitsAction::class);
            return $action->execute($benefit, $endDate, $reason);
        } catch (Exception $e) {
            Log::error('Failed to terminate employee benefits via facade', [
                'error' => $e->getMessage(),
                'benefit_id' => $benefit->id
            ]);
            throw $e;
        }
    }

    /**
     * Calculate benefit costs
     */
    public static function calculateCosts(EmployeeBenefits $benefit): array
    {
        try {
            $action = app(CalculateBenefitsCostAction::class);
            return $action->execute($benefit);
        } catch (Exception $e) {
            Log::error('Failed to calculate benefit costs via facade', [
                'error' => $e->getMessage(),
                'benefit_id' => $benefit->id
            ]);
            throw $e;
        }
    }

    /**
     * Process benefit renewals
     */
    public static function processRenewals(int $daysAhead = 30): array
    {
        try {
            $action = app(ProcessBenefitsRenewalAction::class);
            return $action->execute($daysAhead);
        } catch (Exception $e) {
            Log::error('Failed to process benefit renewals via facade', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Find benefit by ID
     */
    public static function find(int $id): ?EmployeeBenefits
    {
        try {
            $repository = app(EmployeeBenefitsRepository::class);
            return $repository->find($id);
        } catch (Exception $e) {
            Log::error('Failed to find employee benefits via facade', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            return null;
        }
    }

    /**
     * Find benefit by ID and return DTO
     */
    public static function findDTO(int $id): ?EmployeeBenefitsDTO
    {
        try {
            $repository = app(EmployeeBenefitsRepository::class);
            return $repository->findDTO($id);
        } catch (Exception $e) {
            Log::error('Failed to find employee benefits DTO via facade', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            return null;
        }
    }

    /**
     * Find benefits by employee ID
     */
    public static function findByEmployee(int $employeeId): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $repository = app(EmployeeBenefitsRepository::class);
            return $repository->findByEmployeeId($employeeId);
        } catch (Exception $e) {
            Log::error('Failed to find employee benefits by employee ID via facade', [
                'error' => $e->getMessage(),
                'employee_id' => $employeeId
            ]);
            return collect();
        }
    }

    /**
     * Find active benefits
     */
    public static function active(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            $repository = app(EmployeeBenefitsRepository::class);
            return $repository->findActive();
        } catch (Exception $e) {
            Log::error('Failed to find active employee benefits via facade', [
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    /**
     * Get benefits statistics
     */
    public static function statistics(int $employeeId = null): array
    {
        try {
            $repository = app(EmployeeBenefitsRepository::class);
            return $repository->getBenefitsStatistics($employeeId);
        } catch (Exception $e) {
            Log::error('Failed to get employee benefits statistics via facade', [
                'error' => $e->getMessage(),
                'employee_id' => $employeeId
            ]);
            return [];
        }
    }

    /**
     * Get cost analysis
     */
    public static function costAnalysis(int $employeeId = null): array
    {
        try {
            $repository = app(EmployeeBenefitsRepository::class);
            return $repository->getCostAnalysis($employeeId);
        } catch (Exception $e) {
            Log::error('Failed to get employee benefits cost analysis via facade', [
                'error' => $e->getMessage(),
                'employee_id' => $employeeId
            ]);
            return [];
        }
    }

    /**
     * Clear cache
     */
    public static function clearCache(): void
    {
        try {
            Cache::tags(['employee-benefits'])->flush();
        } catch (Exception $e) {
            Log::error('Failed to clear employee benefits cache via facade', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Export benefits data
     */
    public static function export(array $filters = []): string
    {
        try {
            $repository = app(EmployeeBenefitsRepository::class);
            return $repository->exportBenefitsData($filters);
        } catch (Exception $e) {
            Log::error('Failed to export employee benefits data via facade', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);
            throw $e;
        }
    }

    /**
     * Import benefits data
     */
    public static function import(string $data): bool
    {
        try {
            $repository = app(EmployeeBenefitsRepository::class);
            return $repository->importBenefitsData($data);
        } catch (Exception $e) {
            Log::error('Failed to import employee benefits data via facade', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
