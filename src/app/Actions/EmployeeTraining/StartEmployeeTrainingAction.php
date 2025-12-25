<?php

namespace Fereydooni\Shopping\Actions\EmployeeTraining;

use Fereydooni\Shopping\DTOs\EmployeeTrainingDTO;
use Fereydooni\Shopping\Enums\TrainingStatus;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Fereydooni\Shopping\Repositories\Interfaces\EmployeeTrainingRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StartEmployeeTrainingAction
{
    public function __construct(
        private EmployeeTrainingRepositoryInterface $repository
    ) {}

    public function execute(EmployeeTraining $training): EmployeeTrainingDTO
    {
        // Validate that training can be started
        $this->validateStartPermissions($training);

        try {
            DB::beginTransaction();

            // Start the training
            $started = $this->repository->start($training);

            if (! $started) {
                throw new \Exception('Failed to start employee training');
            }

            // Refresh the model to get updated data
            $training->refresh();

            // Send start notifications
            $this->sendStartNotifications($training);

            // Update employee records
            $this->updateEmployeeRecords($training);

            DB::commit();

            return EmployeeTrainingDTO::fromModel($training);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to start employee training', [
                'training_id' => $training->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function validateStartPermissions(EmployeeTraining $training): void
    {
        // Check if training is in a state that can be started
        if ($training->status !== TrainingStatus::NOT_STARTED) {
            throw new \Exception('Training can only be started if it is in NOT_STARTED status');
        }

        // Check if training has required dates
        if (! $training->start_date || ! $training->end_date) {
            throw new \Exception('Training must have start and end dates before it can be started');
        }

        // Check if current date is within training period
        $now = now();
        if ($now < $training->start_date) {
            throw new \Exception('Training cannot be started before the scheduled start date');
        }

        if ($now > $training->end_date) {
            throw new \Exception('Training cannot be started after the scheduled end date');
        }

        // Check if employee is available for training
        if (! $this->isEmployeeAvailable($training)) {
            throw new \Exception('Employee is not available for training at this time');
        }
    }

    private function isEmployeeAvailable(EmployeeTraining $training): bool
    {
        // Add your logic to check if employee is available
        // This could include checking:
        // - Employee is active
        // - Employee is not on leave
        // - Employee is not already in another training
        // - Employee has required prerequisites

        return true; // For now, assume employee is available
    }

    private function sendStartNotifications(EmployeeTraining $training): void
    {
        // Notify employee that training has started
        // $training->employee->notify(new EmployeeTrainingStarted($training));

        // Notify manager that employee has started training
        // if ($training->employee->manager) {
        //     $training->employee->manager->notify(new EmployeeTrainingStarted($training));
        // }

        // Notify HR department
        // $hrUsers = User::role('hr')->get();
        // foreach ($hrUsers as $hrUser) {
        //     $hrUser->notify(new EmployeeTrainingStarted($training));
        // }
    }

    private function updateEmployeeRecords(EmployeeTraining $training): void
    {
        // Update employee's current training status
        $training->employee->update([
            'current_training_id' => $training->id,
            'training_status' => 'in_progress',
        ]);

        // Log the training start in employee history
        // EmployeeTrainingHistory::create([
        //     'employee_id' => $training->employee_id,
        //     'training_id' => $training->id,
        //     'action' => 'started',
        //     'timestamp' => now(),
        //     'notes' => 'Training started by system'
        // ]);
    }
}
