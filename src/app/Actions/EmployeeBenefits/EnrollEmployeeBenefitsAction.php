<?php

namespace Fereydooni\Shopping\app\Actions\EmployeeBenefits;

use Fereydooni\Shopping\app\DTOs\EmployeeBenefitsDTO;
use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use App\Repositories\EmployeeBenefitsRepository;
use Fereydooni\Shopping\app\Events\EmployeeBenefits\EmployeeBenefitsEnrolled;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class EnrollEmployeeBenefitsAction
{
    public function __construct(
        private EmployeeBenefitsRepository $repository
    ) {}

    public function execute(EmployeeBenefits $benefit, string $effectiveDate = null): EmployeeBenefitsDTO
    {
        try {
            DB::beginTransaction();

            // Validate enrollment permissions
            $this->validateEnrollmentPermissions($benefit);

            // Set effective date
            $effectiveDate = $effectiveDate ?? \now()->addDays(30)->format('Y-m-d');

            // Update status to enrolled
            $updated = $this->repository->update($benefit, [
                'status' => 'enrolled',
                'effective_date' => $effectiveDate,
                'is_active' => true
            ]);

            if (!$updated) {
                throw new Exception('Failed to enroll employee in benefits');
            }

            // Calculate contributions
            $this->calculateContributions($benefit);

            // Send enrollment notifications
            $this->sendEnrollmentNotifications($benefit);

            // Update employee records
            $this->updateEmployeeRecords($benefit);

            // Fire event
            \event(new EmployeeBenefitsEnrolled($benefit));

            DB::commit();

            return $this->repository->findDTO($benefit->id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to enroll employee in benefits', [
                'error' => $e->getMessage(),
                'benefit_id' => $benefit->id
            ]);
            throw $e;
        }
    }

    private function validateEnrollmentPermissions(EmployeeBenefits $benefit): void
    {
        // Check if benefit is in pending status
        if ($benefit->status->value !== 'pending') {
            throw new Exception('Only pending benefits can be enrolled');
        }

        // Check if employee is active
        if (!$benefit->employee || !$benefit->employee->is_active) {
            throw new Exception('Employee must be active to enroll in benefits');
        }

        // Check if benefit is not expired
        if ($benefit->end_date && strtotime($benefit->end_date) <= time()) {
            throw new Exception('Cannot enroll in expired benefits');
        }
    }

    private function calculateContributions(EmployeeBenefits $benefit): void
    {
        // Recalculate contributions based on current rates
        $costData = [
            'premium_amount' => $benefit->premium_amount,
            'employee_contribution' => $benefit->employee_contribution,
            'employer_contribution' => $benefit->employer_contribution,
        ];

        $this->repository->updateCosts($benefit, $costData);
    }

    private function sendEnrollmentNotifications(EmployeeBenefits $benefit): void
    {
        // Send notification to employee
        // Send notification to HR
        // Send notification to payroll department
    }

    private function updateEmployeeRecords(EmployeeBenefits $benefit): void
    {
        // Update employee benefits count
        // Update employee cost records
        // Update payroll deductions
    }
}
