<?php

namespace App\Traits;

use App\Models\EmployeeSalaryHistory;
use App\DTOs\EmployeeSalaryHistoryDTO;
use App\Repositories\Interfaces\EmployeeSalaryHistoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

trait HasEmployeeSalaryHistoryApprovalManagement
{
    /**
     * Approve salary change
     */
    public function approveSalaryChange(EmployeeSalaryHistory $salaryHistory, int $approvedBy, string $notes = null): bool
    {
        try {
            // Check if already approved
            if ($salaryHistory->isApproved()) {
                throw new \InvalidArgumentException('Salary change is already approved');
            }

            // Update approval status
            $updated = app(EmployeeSalaryHistoryRepositoryInterface::class)->approve($salaryHistory, $approvedBy);

            if ($updated) {
                // Add approval notes if provided
                if ($notes) {
                    $currentNotes = $salaryHistory->notes ?? '';
                    $approvalNotes = "\nApproval Notes: " . $notes;
                    $salaryHistory->update(['notes' => $currentNotes . $approvalNotes]);
                }

                // Log approval
                Log::info('Salary change approved via trait', [
                    'id' => $salaryHistory->id,
                    'employee_id' => $salaryHistory->employee_id,
                    'approved_by' => $approvedBy,
                    'notes' => $notes
                ]);

                // Trigger approval events (if using events)
                $this->triggerSalaryChangeApprovedEvent($salaryHistory, $approvedBy);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to approve salary change via trait', [
                'error' => $e->getMessage(),
                'id' => $salaryHistory->id,
                'approved_by' => $approvedBy
            ]);
            throw $e;
        }
    }

    /**
     * Reject salary change
     */
    public function rejectSalaryChange(EmployeeSalaryHistory $salaryHistory, int $rejectedBy, string $reason = null): bool
    {
        try {
            // Check if already approved
            if ($salaryHistory->isApproved()) {
                throw new \InvalidArgumentException('Cannot reject already approved salary change');
            }

            // Update rejection status
            $updated = app(EmployeeSalaryHistoryRepositoryInterface::class)->reject($salaryHistory, $rejectedBy, $reason);

            if ($updated) {
                // Log rejection
                Log::info('Salary change rejected via trait', [
                    'id' => $salaryHistory->id,
                    'employee_id' => $salaryHistory->employee_id,
                    'rejected_by' => $rejectedBy,
                    'reason' => $reason
                ]);

                // Trigger rejection events (if using events)
                $this->triggerSalaryChangeRejectedEvent($salaryHistory, $rejectedBy, $reason);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to reject salary change via trait', [
                'error' => $e->getMessage(),
                'id' => $salaryHistory->id,
                'rejected_by' => $rejectedBy
            ]);
            throw $e;
        }
    }

    /**
     * Get pending approval salary changes
     */
    public function getPendingApprovalSalaryChanges(): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findByApproverId(null);
    }

    /**
     * Get pending approval salary changes by employee
     */
    public function getPendingApprovalSalaryChangesByEmployee(int $employeeId): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findByEmployeeId($employeeId)
            ->filter(function ($salaryHistory) {
                return $salaryHistory->isPendingApproval();
            });
    }

    /**
     * Get approved salary changes
     */
    public function getApprovedSalaryChanges(): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->all()
            ->filter(function ($salaryHistory) {
                return $salaryHistory->isApproved();
            });
    }

    /**
     * Get approved salary changes by employee
     */
    public function getApprovedSalaryChangesByEmployee(int $employeeId): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findByEmployeeId($employeeId)
            ->filter(function ($salaryHistory) {
                return $salaryHistory->isApproved();
            });
    }

    /**
     * Get approved salary changes by approver
     */
    public function getApprovedSalaryChangesByApprover(int $approverId): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findByApproverId($approverId);
    }

    /**
     * Get salary changes pending approval by date range
     */
    public function getPendingApprovalSalaryChangesByDateRange(string $startDate, string $endDate): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findByDateRange($startDate, $endDate)
            ->filter(function ($salaryHistory) {
                return $salaryHistory->isPendingApproval();
            });
    }

    /**
     * Get salary changes approved by date range
     */
    public function getApprovedSalaryChangesByDateRange(string $startDate, string $endDate): Collection
    {
        return app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findByDateRange($startDate, $endDate)
            ->filter(function ($salaryHistory) {
                return $salaryHistory->isApproved();
            });
    }

    /**
     * Check if employee has pending salary change approvals
     */
    public function hasPendingSalaryChangeApprovals(int $employeeId): bool
    {
        return $this->getPendingApprovalSalaryChangesByEmployee($employeeId)->count() > 0;
    }

    /**
     * Get count of pending salary change approvals
     */
    public function getPendingSalaryChangeApprovalsCount(): int
    {
        return $this->getPendingApprovalSalaryChanges()->count();
    }

    /**
     * Get count of pending salary change approvals by employee
     */
    public function getPendingSalaryChangeApprovalsCountByEmployee(int $employeeId): int
    {
        return $this->getPendingApprovalSalaryChangesByEmployee($employeeId)->count();
    }

    /**
     * Get count of approved salary changes
     */
    public function getApprovedSalaryChangesCount(): int
    {
        return $this->getApprovedSalaryChanges()->count();
    }

    /**
     * Get count of approved salary changes by employee
     */
    public function getApprovedSalaryChangesCountByEmployee(int $employeeId): int
    {
        return $this->getApprovedSalaryChangesByEmployee($employeeId)->count();
    }

    /**
     * Get count of approved salary changes by approver
     */
    public function getApprovedSalaryChangesCountByApprover(int $approverId): int
    {
        return $this->getApprovedSalaryChangesByApprover($approverId)->count();
    }

    /**
     * Get approval statistics
     */
    public function getApprovalStatistics(): array
    {
        $allChanges = app(EmployeeSalaryHistoryRepositoryInterface::class)->all();

        return [
            'total_changes' => $allChanges->count(),
            'pending_approval' => $allChanges->filter(fn($change) => $change->isPendingApproval())->count(),
            'approved' => $allChanges->filter(fn($change) => $change->isApproved())->count(),
            'approval_rate' => $allChanges->count() > 0 ?
                ($allChanges->filter(fn($change) => $change->isApproved())->count() / $allChanges->count()) * 100 : 0,
            'by_type' => $allChanges->groupBy('change_type')->map(function ($changes) {
                return [
                    'total' => $changes->count(),
                    'pending' => $changes->filter(fn($change) => $change->isPendingApproval())->count(),
                    'approved' => $changes->filter(fn($change) => $change->isApproved())->count(),
                ];
            })->toArray(),
        ];
    }

    /**
     * Get approval statistics by date range
     */
    public function getApprovalStatisticsByDateRange(string $startDate, string $endDate): array
    {
        $changes = app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findByDateRange($startDate, $endDate);

        return [
            'total_changes' => $changes->count(),
            'pending_approval' => $changes->filter(fn($change) => $change->isPendingApproval())->count(),
            'approved' => $changes->filter(fn($change) => $change->isApproved())->count(),
            'approval_rate' => $changes->count() > 0 ?
                ($changes->filter(fn($change) => $change->isApproved())->count() / $changes->count()) * 100 : 0,
        ];
    }

    /**
     * Get approval statistics by employee
     */
    public function getApprovalStatisticsByEmployee(int $employeeId): array
    {
        $changes = app(EmployeeSalaryHistoryRepositoryInterface::class)
            ->findByEmployeeId($employeeId);

        return [
            'total_changes' => $changes->count(),
            'pending_approval' => $changes->filter(fn($change) => $change->isPendingApproval())->count(),
            'approved' => $changes->filter(fn($change) => $change->isApproved())->count(),
            'approval_rate' => $changes->count() > 0 ?
                ($changes->filter(fn($change) => $change->isApproved())->count() / $changes->count()) * 100 : 0,
        ];
    }

    /**
     * Check if salary change can be approved
     */
    public function canApproveSalaryChange(EmployeeSalaryHistory $salaryHistory): bool
    {
        return !$salaryHistory->isApproved() &&
               $salaryHistory->isPendingApproval() &&
               $salaryHistory->effective_date <= now()->toDateString();
    }

    /**
     * Check if salary change can be rejected
     */
    public function canRejectSalaryChange(EmployeeSalaryHistory $salaryHistory): bool
    {
        return !$salaryHistory->isApproved() &&
               $salaryHistory->isPendingApproval();
    }

    /**
     * Get approval workflow status
     */
    public function getApprovalWorkflowStatus(EmployeeSalaryHistory $salaryHistory): string
    {
        if ($salaryHistory->isApproved()) {
            return 'approved';
        }

        if ($salaryHistory->isPendingApproval()) {
            if ($salaryHistory->effective_date <= now()->toDateString()) {
                return 'pending_approval';
            } else {
                return 'pending_future_approval';
            }
        }

        return 'unknown';
    }

    /**
     * Trigger salary change approved event
     */
    protected function triggerSalaryChangeApprovedEvent(EmployeeSalaryHistory $salaryHistory, int $approvedBy): void
    {
        // This method can be overridden to trigger custom events
        // For now, it's a placeholder for future event implementation
    }

    /**
     * Trigger salary change rejected event
     */
    protected function triggerSalaryChangeRejectedEvent(EmployeeSalaryHistory $salaryHistory, int $rejectedBy, string $reason = null): void
    {
        // This method can be overridden to trigger custom events
        // For now, it's a placeholder for future event implementation
    }
}
