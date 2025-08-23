<?php

namespace Fereydooni\Shopping\Actions\EmployeeTraining;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Fereydooni\Shopping\DTOs\EmployeeTrainingDTO;
use Fereydooni\Shopping\Repositories\Interfaces\EmployeeTrainingRepositoryInterface;
use Fereydooni\Shopping\Enums\TrainingStatus;

class RenewEmployeeTrainingAction
{
    public function __construct(
        private EmployeeTrainingRepositoryInterface $repository
    ) {}

    public function execute(EmployeeTraining $training, ?string $renewalDate = null): EmployeeTrainingDTO
    {
        // Validate renewal data
        $this->validateRenewalData($training, $renewalDate);

        try {
            DB::beginTransaction();

            // Renew the training
            $renewed = $this->repository->renew($training, $renewalDate);

            if (!$renewed) {
                throw new \Exception('Failed to renew employee training');
            }

            // Refresh the model to get updated data
            $training->refresh();

            // Send renewal notifications
            $this->sendRenewalNotifications($training);

            // Update certificate
            $this->updateCertificate($training);

            DB::commit();

            return EmployeeTrainingDTO::fromModel($training);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to renew employee training', [
                'training_id' => $training->id,
                'error' => $e->getMessage(),
                'renewal_date' => $renewalDate
            ]);
            throw $e;
        }
    }

    private function validateRenewalData(EmployeeTraining $training, ?string $renewalDate): void
    {
        // Check if training is renewable
        if (!$training->is_renewable) {
            throw new \Exception('Training is not renewable');
        }

        // Check if training is completed
        if ($training->status !== TrainingStatus::COMPLETED) {
            throw new \Exception('Only completed trainings can be renewed');
        }

        // Check if training is a certification
        if (!$training->is_certification) {
            throw new \Exception('Only certification trainings can be renewed');
        }

        // Check if renewal date is valid
        if ($renewalDate !== null) {
            $renewalDateTime = \Carbon\Carbon::parse($renewalDate);
            if ($renewalDateTime->isPast()) {
                throw new \Exception('Renewal date must be in the future');
            }
        }

        // Check if training is expiring soon or has expired
        if ($training->expiry_date) {
            $expiryDate = \Carbon\Carbon::parse($training->expiry_date);
            $now = \Carbon\Carbon::now();
            
            // Allow renewal if expiring within 30 days or already expired
            if ($expiryDate->isFuture() && $expiryDate->diffInDays($now) > 30) {
                throw new \Exception('Training can only be renewed if expiring within 30 days or already expired');
            }
        }
    }

    private function sendRenewalNotifications(EmployeeTraining $training): void
    {
        // Notify employee that training has been renewed
        // $training->employee->notify(new EmployeeTrainingRenewed($training));

        // Notify manager that employee has renewed training
        // if ($training->employee->manager) {
        //     $training->employee->manager->notify(new EmployeeTrainingRenewed($training));
        // }

        // Notify HR department
        // $hrUsers = User::role('hr')->get();
        // foreach ($hrUsers as $hrUser) {
        //     $hrUser->notify(new EmployeeTrainingRenewed($training));
        // }

        // Notify certification department
        // $certificationUsers = User::role('certification')->get();
        // foreach ($certificationUsers as $certUser) {
        //     $certUser->notify(new EmployeeTrainingRenewed($training));
        // }
    }

    private function updateCertificate(EmployeeTraining $training): void
    {
        // Generate new certificate number
        $newCertificateNumber = $this->generateRenewalCertificateNumber($training);

        // Update certificate information
        $training->update([
            'certificate_number' => $newCertificateNumber,
            'renewal_date' => now(),
            'expiry_date' => $this->calculateNewExpiryDate($training)
        ]);
    }

    private function generateRenewalCertificateNumber(EmployeeTraining $training): string
    {
        // Generate a new certificate number for renewal
        $prefix = 'RENEW';
        $year = date('Y');
        $trainingId = str_pad($training->id, 6, '0', STR_PAD_LEFT);
        $employeeId = str_pad($training->employee_id, 4, '0', STR_PAD_LEFT);
        $renewalCount = $this->getRenewalCount($training) + 1;
        
        return "{$prefix}-{$year}-{$trainingId}-{$employeeId}-R{$renewalCount}";
    }

    private function getRenewalCount(EmployeeTraining $training): int
    {
        // Count previous renewals for this training
        // This would depend on your renewal tracking system
        // For now, return 0
        return 0;
    }

    private function calculateNewExpiryDate(EmployeeTraining $training): \Carbon\Carbon
    {
        // Calculate new expiry date based on training type and company policy
        $baseExpiryPeriod = $this->getBaseExpiryPeriod($training->training_type);
        
        // Add any additional time for renewals
        $renewalBonus = $this->getRenewalBonus($training);
        
        return now()->addDays($baseExpiryPeriod + $renewalBonus);
    }

    private function getBaseExpiryPeriod(string $trainingType): int
    {
        // Return expiry period in days based on training type
        $expiryPeriods = [
            'technical' => 365, // 1 year
            'soft_skills' => 730, // 2 years
            'compliance' => 365, // 1 year
            'safety' => 365, // 1 year
            'leadership' => 730, // 2 years
            'product' => 545, // 1.5 years
            'other' => 365 // 1 year
        ];

        return $expiryPeriods[$trainingType] ?? 365;
    }

    private function getRenewalBonus(EmployeeTraining $training): int
    {
        // Add bonus days for renewals (e.g., 30 days for each renewal)
        $renewalCount = $this->getRenewalCount($training);
        return $renewalCount * 30;
    }
}
