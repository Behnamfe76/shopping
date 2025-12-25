<?php

namespace Fereydooni\Shopping\app\Actions\EmployeeBenefits;

use App\Repositories\EmployeeBenefitsRepository;
use Exception;
use Fereydooni\Shopping\app\DTOs\EmployeeBenefitsDTO;
use Fereydooni\Shopping\app\Events\EmployeeBenefits\EmployeeBenefitsCreated;
use Fereydooni\Shopping\app\Models\EmployeeBenefits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateEmployeeBenefitsAction
{
    public function __construct(
        private EmployeeBenefitsRepository $repository
    ) {}

    public function execute(array $data): EmployeeBenefitsDTO
    {
        try {
            DB::beginTransaction();

            // Validate benefit enrollment data
            $this->validateEnrollmentData($data);

            // Check for existing enrollments
            $this->checkExistingEnrollments($data);

            // Calculate costs
            $costData = $this->calculateCosts($data);

            // Set effective dates
            $data = $this->setEffectiveDates($data);

            // Handle documents
            $data = $this->handleDocuments($data);

            // Create the benefit enrollment
            $benefit = $this->repository->create(array_merge($data, $costData));

            // Send notifications
            $this->sendNotifications($benefit);

            // Fire event
            \event(new EmployeeBenefitsCreated($benefit));

            DB::commit();

            return $this->repository->findDTO($benefit->id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create employee benefits', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    private function validateEnrollmentData(array $data): void
    {
        // Basic validation
        if (empty($data['employee_id']) || empty($data['benefit_type']) || empty($data['benefit_name'])) {
            throw new Exception('Required fields are missing');
        }

        // Validate benefit type
        $validTypes = ['health', 'dental', 'vision', 'life', 'disability', 'retirement', 'other'];
        if (! in_array($data['benefit_type'], $validTypes)) {
            throw new Exception('Invalid benefit type');
        }

        // Validate status
        $validStatuses = ['enrolled', 'pending', 'terminated', 'cancelled'];
        if (! empty($data['status']) && ! in_array($data['status'], $validStatuses)) {
            throw new Exception('Invalid status');
        }
    }

    private function checkExistingEnrollments(array $data): void
    {
        $existing = $this->repository->findByEmployeeAndType(
            $data['employee_id'],
            $data['benefit_type']
        );

        if ($existing->isNotEmpty()) {
            throw new Exception('Employee already has an active enrollment for this benefit type');
        }
    }

    private function calculateCosts(array $data): array
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

        return [
            'total_cost' => $totalCost,
            'employee_contribution' => $employeeContribution,
            'employer_contribution' => $employerContribution,
        ];
    }

    private function setEffectiveDates(array $data): array
    {
        // Set default effective date if not provided
        if (empty($data['effective_date'])) {
            $data['effective_date'] = \now()->addDays(30)->format('Y-m-d');
        }

        // Set default enrollment date if not provided
        if (empty($data['enrollment_date'])) {
            $data['enrollment_date'] = \now()->format('Y-m-d');
        }

        // Set default status if not provided
        if (empty($data['status'])) {
            $data['status'] = 'pending';
        }

        return $data;
    }

    private function handleDocuments(array $data): array
    {
        // Handle document uploads if provided
        if (! empty($data['documents'])) {
            // Process and store documents
            $data['documents'] = json_encode($data['documents']);
        }

        return $data;
    }

    private function sendNotifications(EmployeeBenefits $benefit): void
    {
        // Send notification to HR
        // Send notification to employee
        // This would be implemented with actual notification classes
    }
}
