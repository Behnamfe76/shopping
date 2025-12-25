<?php

namespace Fereydooni\Shopping\app\Actions\EmployeeBenefits;

use App\Repositories\EmployeeBenefitsRepository;
use Exception;
use Fereydooni\Shopping\app\DTOs\EmployeeBenefitsDTO;
use Fereydooni\Shopping\app\Events\EmployeeBenefits\EmployeeBenefitsTerminated;
use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TerminateEmployeeBenefitsAction
{
    public function __construct(
        private EmployeeBenefitsRepository $repository
    ) {}

    public function execute(EmployeeBenefits $benefit, ?string $endDate = null, ?string $reason = null): EmployeeBenefitsDTO
    {
        try {
            DB::beginTransaction();

            // Validate termination permissions
            $this->validateTerminationPermissions($benefit);

            // Set end date
            $endDate = $endDate ?? \now()->format('Y-m-d');

            // Update status to terminated
            $updated = $this->repository->update($benefit, [
                'status' => 'terminated',
                'end_date' => $endDate,
                'is_active' => false,
                'notes' => $reason ? ($benefit->notes."\nTermination Reason: ".$reason) : $benefit->notes,
            ]);

            if (! $updated) {
                throw new Exception('Failed to terminate employee benefits');
            }

            // Record termination reason
            $this->recordTerminationReason($benefit, $reason);

            // Send termination notifications
            $this->sendTerminationNotifications($benefit, $reason);

            // Update related records
            $this->updateRelatedRecords($benefit);

            // Fire event
            \event(new EmployeeBenefitsTerminated($benefit, $reason));

            DB::commit();

            return $this->repository->findDTO($benefit->id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to terminate employee benefits', [
                'error' => $e->getMessage(),
                'benefit_id' => $benefit->id,
            ]);
            throw $e;
        }
    }

    private function validateTerminationPermissions(EmployeeBenefits $benefit): void
    {
        // Check if benefit is active and enrolled
        if ($benefit->status->value !== 'enrolled' || ! $benefit->is_active) {
            throw new Exception('Only active and enrolled benefits can be terminated');
        }

        // Check if termination date is not in the past
        if (strtotime($benefit->effective_date) > time()) {
            throw new Exception('Cannot terminate benefits that are not yet effective');
        }
    }

    private function recordTerminationReason(EmployeeBenefits $benefit, ?string $reason = null): void
    {
        if ($reason) {
            $currentNotes = $benefit->notes ?? '';
            $terminationNote = "\nTermination Date: ".\now()->format('Y-m-d')."\nReason: ".$reason;

            $this->repository->update($benefit, [
                'notes' => $currentNotes.$terminationNote,
            ]);
        }
    }

    private function sendTerminationNotifications(EmployeeBenefits $benefit, ?string $reason = null): void
    {
        // Send notification to employee
        // Send notification to HR
        // Send notification to payroll department
        // Send notification to benefits provider if needed
    }

    private function updateRelatedRecords(EmployeeBenefits $benefit): void
    {
        // Update employee benefits count
        // Update payroll deductions
        // Update benefits statistics
        // Update cost calculations
    }
}
