<?php

namespace Fereydooni\Shopping\Actions\EmployeeTraining;

use Fereydooni\Shopping\DTOs\EmployeeTrainingDTO;
use Fereydooni\Shopping\Enums\TrainingStatus;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Fereydooni\Shopping\Repositories\Interfaces\EmployeeTrainingRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FailEmployeeTrainingAction
{
    public function __construct(
        private EmployeeTrainingRepositoryInterface $repository
    ) {}

    public function execute(EmployeeTraining $training, ?string $reason = null): EmployeeTrainingDTO
    {
        // Validate failure data
        $this->validateFailureData($training, $reason);

        try {
            DB::beginTransaction();

            // Mark training as failed
            $failed = $this->repository->fail($training, $reason);

            if (! $failed) {
                throw new \Exception('Failed to mark employee training as failed');
            }

            // Refresh the model to get updated data
            $training->refresh();

            // Send failure notifications
            $this->sendFailureNotifications($training, $reason);

            // Handle retake logic
            $this->handleRetakeLogic($training);

            DB::commit();

            return EmployeeTrainingDTO::fromModel($training);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark employee training as failed', [
                'training_id' => $training->id,
                'error' => $e->getMessage(),
                'reason' => $reason,
            ]);
            throw $e;
        }
    }

    private function validateFailureData(EmployeeTraining $training, ?string $reason): void
    {
        // Check if training is in progress
        if ($training->status !== TrainingStatus::IN_PROGRESS) {
            throw new \Exception('Training can only be marked as failed if it is in progress');
        }

        // Validate reason if provided
        if ($reason !== null && strlen($reason) > 1000) {
            throw new \Exception('Failure reason cannot exceed 1000 characters');
        }
    }

    private function sendFailureNotifications(EmployeeTraining $training, ?string $reason): void
    {
        // Notify employee that training has failed
        // $training->employee->notify(new EmployeeTrainingFailed($training, $reason));

        // Notify manager that employee has failed training
        // if ($training->employee->manager) {
        //     $training->employee->manager->notify(new EmployeeTrainingFailed($training, $reason));
        // }

        // Notify HR department
        // $hrUsers = User::role('hr')->get();
        // foreach ($hrUsers as $hrUser) {
        //     $hrUser->notify(new EmployeeTrainingFailed($training, $reason));
        // }

        // If it's mandatory training, notify compliance department
        if ($training->is_mandatory) {
            // $complianceUsers = User::role('compliance')->get();
            // foreach ($complianceUsers as $compUser) {
            //     $compUser->notify(new MandatoryTrainingFailed($training, $reason));
            // }
        }
    }

    private function handleRetakeLogic(EmployeeTraining $training): void
    {
        // Check if training allows retakes
        if (! $this->allowsRetake($training)) {
            return;
        }

        // Create a new training record for retake
        $retakeData = [
            'employee_id' => $training->employee_id,
            'training_type' => $training->training_type,
            'training_name' => $training->training_name,
            'provider' => $training->provider,
            'description' => $training->description.' (Retake)',
            'start_date' => now()->addDays(7), // Allow 7 days before retake
            'end_date' => now()->addDays(30), // 30 days to complete retake
            'total_hours' => $training->total_hours,
            'cost' => $training->cost * 0.5, // 50% cost for retake
            'is_mandatory' => $training->is_mandatory,
            'is_certification' => $training->is_certification,
            'is_renewable' => $training->is_renewable,
            'instructor' => $training->instructor,
            'location' => $training->location,
            'training_method' => $training->training_method,
            'materials' => $training->materials,
            'notes' => "Retake of training ID: {$training->id}. Original failure reason: {$training->failure_reason}",
            'status' => TrainingStatus::NOT_STARTED,
        ];

        // Create retake training
        // $retakeTraining = EmployeeTraining::create($retakeData);

        // Notify about retake opportunity
        // $training->employee->notify(new TrainingRetakeAvailable($retakeTraining));
    }

    private function allowsRetake(EmployeeTraining $training): bool
    {
        // Check if training allows retakes
        // This could be based on:
        // - Training type
        // - Company policy
        // - Previous retake attempts
        // - Time since last attempt

        // For now, allow retakes for most trainings except some certifications
        $noRetakeTypes = ['compliance', 'safety'];

        return ! in_array($training->training_type, $noRetakeTypes);
    }
}
