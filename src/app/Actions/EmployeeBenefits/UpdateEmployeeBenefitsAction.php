<?php

namespace Fereydooni\Shopping\app\Actions\EmployeeBenefits;

use App\Repositories\EmployeeBenefitsRepository;
use Exception;
use Fereydooni\Shopping\app\DTOs\EmployeeBenefitsDTO;
use Fereydooni\Shopping\app\Events\EmployeeBenefits\EmployeeBenefitsUpdated;
use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateEmployeeBenefitsAction
{
    public function __construct(
        private EmployeeBenefitsRepository $repository
    ) {}

    public function execute(EmployeeBenefits $benefit, array $data): EmployeeBenefitsDTO
    {
        try {
            DB::beginTransaction();

            // Validate update data
            $this->validateUpdateData($data);

            // Check if enrollment can be modified
            $this->checkModificationPermissions($benefit, $data);

            // Update cost calculations if needed
            if ($this->costsChanged($benefit, $data)) {
                $data = $this->recalculateCosts($data);
            }

            // Handle status changes
            if (isset($data['status']) && $data['status'] !== $benefit->status->value) {
                $data = $this->handleStatusChange($benefit, $data);
            }

            // Update the benefit enrollment
            $updated = $this->repository->update($benefit, $data);

            if (! $updated) {
                throw new Exception('Failed to update employee benefits');
            }

            // Send notifications
            $this->sendNotifications($benefit, $data);

            // Fire event
            \event(new EmployeeBenefitsUpdated($benefit));

            DB::commit();

            return $this->repository->findDTO($benefit->id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update employee benefits', [
                'error' => $e->getMessage(),
                'benefit_id' => $benefit->id,
                'data' => $data,
            ]);
            throw $e;
        }
    }

    private function validateUpdateData(array $data): void
    {
        // Validate benefit type if provided
        if (isset($data['benefit_type'])) {
            $validTypes = ['health', 'dental', 'vision', 'life', 'disability', 'retirement', 'other'];
            if (! in_array($data['benefit_type'], $validTypes)) {
                throw new Exception('Invalid benefit type');
            }
        }

        // Validate status if provided
        if (isset($data['status'])) {
            $validStatuses = ['enrolled', 'pending', 'terminated', 'cancelled'];
            if (! in_array($data['status'], $validStatuses)) {
                throw new Exception('Invalid status');
            }
        }

        // Validate dates
        if (isset($data['effective_date']) && isset($data['end_date'])) {
            if (strtotime($data['effective_date']) >= strtotime($data['end_date'])) {
                throw new Exception('Effective date must be before end date');
            }
        }
    }

    private function checkModificationPermissions(EmployeeBenefits $benefit, array $data): void
    {
        // Check if benefit is terminated or cancelled
        if (in_array($benefit->status->value, ['terminated', 'cancelled'])) {
            throw new Exception('Cannot modify terminated or cancelled benefits');
        }

        // Check if effective date has passed
        if (strtotime($benefit->effective_date) <= time()) {
            throw new Exception('Cannot modify benefits that are already effective');
        }
    }

    private function costsChanged(EmployeeBenefits $benefit, array $data): bool
    {
        $costFields = ['premium_amount', 'employee_contribution', 'employer_contribution'];

        foreach ($costFields as $field) {
            if (isset($data[$field]) && $data[$field] != $benefit->$field) {
                return true;
            }
        }

        return false;
    }

    private function recalculateCosts(array $data): array
    {
        $premiumAmount = $data['premium_amount'] ?? 0;
        $employeeContribution = $data['employee_contribution'] ?? 0;
        $employerContribution = $data['employer_contribution'] ?? 0;

        // Calculate total cost
        $totalCost = $premiumAmount;

        // Calculate employee contribution if not specified
        if ($employeeContribution == 0 && $employerContribution > 0) {
            $employeeContribution = $totalCost - $employerContribution;
        }

        // Calculate employer contribution if not specified
        if ($employerContribution == 0 && $employeeContribution > 0) {
            $employerContribution = $totalCost - $employeeContribution;
        }

        return array_merge($data, [
            'total_cost' => $totalCost,
            'employee_contribution' => $employeeContribution,
            'employer_contribution' => $employerContribution,
        ]);
    }

    private function handleStatusChange(EmployeeBenefits $benefit, array $data): array
    {
        $newStatus = $data['status'];
        $oldStatus = $benefit->status->value;

        // Handle specific status transitions
        if ($oldStatus === 'pending' && $newStatus === 'enrolled') {
            $data['effective_date'] = $data['effective_date'] ?? \now()->format('Y-m-d');
        }

        if ($newStatus === 'terminated') {
            $data['end_date'] = $data['end_date'] ?? \now()->format('Y-m-d');
        }

        if ($newStatus === 'cancelled') {
            $data['end_date'] = $data['end_date'] ?? \now()->format('Y-m-d');
        }

        return $data;
    }

    private function sendNotifications(EmployeeBenefits $benefit, array $data): void
    {
        // Send notification for status changes
        if (isset($data['status']) && $data['status'] !== $benefit->status->value) {
            // Send status change notification
        }

        // Send notification for cost changes
        if ($this->costsChanged($benefit, $data)) {
            // Send cost change notification
        }
    }
}
