<?php

namespace App\Services;

use App\Enums\SalaryChangeType;
use App\Models\Employee;
use App\Models\EmployeeSalaryHistory;
use App\Repositories\EmployeeSalaryHistoryRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeSalaryHistoryService
{
    protected $repository;

    public function __construct(EmployeeSalaryHistoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new salary history record
     */
    public function createSalaryHistory(array $data): ?EmployeeSalaryHistory
    {
        try {
            DB::beginTransaction();

            // Validate the data
            $this->validateSalaryHistoryData($data);

            // Calculate change amounts if not provided
            if (empty($data['change_amount']) && isset($data['old_salary']) && isset($data['new_salary'])) {
                $data['change_amount'] = $data['new_salary'] - $data['old_salary'];
            }

            if (empty($data['change_percentage']) && isset($data['old_salary']) && $data['old_salary'] > 0) {
                $data['change_percentage'] = ($data['change_amount'] / $data['old_salary']) * 100;
            }

            // Set default status
            if (empty($data['status'])) {
                $data['status'] = 'pending';
            }

            // Create the record
            $salaryHistory = $this->repository->create($data);

            DB::commit();

            return $salaryHistory;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating salary history', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing salary history record
     */
    public function updateSalaryHistory(int $id, array $data): ?EmployeeSalaryHistory
    {
        try {
            DB::beginTransaction();

            $salaryHistory = $this->repository->find($id);
            if (! $salaryHistory) {
                throw new Exception('Salary history record not found');
            }

            // Validate the data
            $this->validateSalaryHistoryData($data, $salaryHistory);

            // Update the record
            $updated = $this->repository->update($salaryHistory, $data);
            if (! $updated) {
                throw new Exception('Failed to update salary history record');
            }

            // Refresh the model
            $salaryHistory->refresh();

            DB::commit();

            return $salaryHistory;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating salary history', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Approve a salary change
     */
    public function approveSalaryChange(int $id, int $approvedBy): bool
    {
        try {
            DB::beginTransaction();

            $salaryHistory = $this->repository->find($id);
            if (! $salaryHistory) {
                throw new Exception('Salary history record not found');
            }

            if ($salaryHistory->status !== 'pending') {
                throw new Exception('Only pending salary changes can be approved');
            }

            // Approve the change
            $approved = $this->repository->approve($salaryHistory, $approvedBy);
            if (! $approved) {
                throw new Exception('Failed to approve salary change');
            }

            // Update employee salary if effective date is in the past
            if ($salaryHistory->effective_date->isPast()) {
                $this->updateEmployeeSalary($salaryHistory);
            }

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error approving salary change', [
                'id' => $id,
                'approved_by' => $approvedBy,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Reject a salary change
     */
    public function rejectSalaryChange(int $id, int $rejectedBy, ?string $reason = null): bool
    {
        try {
            DB::beginTransaction();

            $salaryHistory = $this->repository->find($id);
            if (! $salaryHistory) {
                throw new Exception('Salary history record not found');
            }

            if ($salaryHistory->status !== 'pending') {
                throw new Exception('Only pending salary changes can be rejected');
            }

            // Reject the change
            $rejected = $this->repository->reject($salaryHistory, $rejectedBy, $reason);
            if (! $rejected) {
                throw new Exception('Failed to reject salary change');
            }

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting salary change', [
                'id' => $id,
                'rejected_by' => $rejectedBy,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Process a salary change (mark as processed)
     */
    public function processSalaryChange(int $id, int $processedBy): bool
    {
        try {
            DB::beginTransaction();

            $salaryHistory = $this->repository->find($id);
            if (! $salaryHistory) {
                throw new Exception('Salary history record not found');
            }

            if ($salaryHistory->status !== 'approved') {
                throw new Exception('Only approved salary changes can be processed');
            }

            // Update status to processed
            $updated = $this->repository->update($salaryHistory, [
                'status' => 'processed',
                'processed_at' => now(),
            ]);

            if (! $updated) {
                throw new Exception('Failed to process salary change');
            }

            // Update employee salary
            $this->updateEmployeeSalary($salaryHistory);

            // Process retroactive adjustment if applicable
            if ($salaryHistory->is_retroactive) {
                $this->processRetroactiveAdjustment($salaryHistory);
            }

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error processing salary change', [
                'id' => $id,
                'processed_by' => $processedBy,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get salary history for an employee
     */
    public function getEmployeeSalaryHistory(int $employeeId, array $filters = []): Collection
    {
        return $this->repository->findByEmployeeId($employeeId);
    }

    /**
     * Get salary statistics
     */
    public function getSalaryStatistics(?string $startDate = null, ?string $endDate = null): array
    {
        return $this->repository->getSalaryChangeStatistics($startDate, $endDate);
    }

    /**
     * Get salary trends
     */
    public function getSalaryTrends(?string $startDate = null, ?string $endDate = null): array
    {
        return $this->repository->getSalaryTrends($startDate, $endDate);
    }

    /**
     * Search salary history
     */
    public function searchSalaryHistory(string $query, array $filters = []): Collection
    {
        return $this->repository->searchSalaryHistory($query);
    }

    /**
     * Export salary history data
     */
    public function exportSalaryHistory(array $filters = []): string
    {
        return $this->repository->exportSalaryHistoryData($filters);
    }

    /**
     * Import salary history data
     */
    public function importSalaryHistory(string $data): bool
    {
        return $this->repository->importSalaryHistoryData($data);
    }

    /**
     * Validate salary history data
     */
    private function validateSalaryHistoryData(array $data, ?EmployeeSalaryHistory $existing = null): void
    {
        // Basic validation rules
        $rules = [
            'employee_id' => 'required|exists:employees,id',
            'old_salary' => 'required|numeric|min:0',
            'new_salary' => 'required|numeric|min:0',
            'change_type' => 'required|string',
            'effective_date' => 'required|date',
            'reason' => 'nullable|string|max:1000',
        ];

        // Validate change type
        if (isset($data['change_type'])) {
            $validTypes = array_column(SalaryChangeType::cases(), 'value');
            if (! in_array($data['change_type'], $validTypes)) {
                throw new Exception('Invalid change type');
            }
        }

        // Validate retroactive dates if applicable
        if (! empty($data['is_retroactive']) && $data['is_retroactive']) {
            if (empty($data['retroactive_start_date']) || empty($data['retroactive_end_date'])) {
                throw new Exception('Retroactive start and end dates are required for retroactive adjustments');
            }

            if (Carbon::parse($data['retroactive_start_date'])->isAfter(Carbon::parse($data['retroactive_end_date']))) {
                throw new Exception('Retroactive start date must be before end date');
            }
        }
    }

    /**
     * Update employee salary when change is processed
     */
    private function updateEmployeeSalary(EmployeeSalaryHistory $salaryHistory): void
    {
        $employee = $salaryHistory->employee;
        if (! $employee) {
            return;
        }

        $employee->update([
            'current_salary' => $salaryHistory->new_salary,
            'salary_updated_at' => now(),
        ]);
    }

    /**
     * Process retroactive adjustment
     */
    private function processRetroactiveAdjustment(EmployeeSalaryHistory $salaryHistory): void
    {
        if (! $salaryHistory->is_retroactive) {
            return;
        }

        // Calculate retroactive amount
        $retroactiveAmount = $salaryHistory->getRetroactiveAmount();

        // Update employee retroactive records
        $employee = $salaryHistory->employee;
        if ($employee) {
            $employee->update([
                'retroactive_adjustments_total' => ($employee->retroactive_adjustments_total ?? 0) + $retroactiveAmount,
                'last_retroactive_adjustment' => now(),
            ]);
        }

        // Create retroactive payroll entry
        // This would integrate with the payroll system
    }
}
