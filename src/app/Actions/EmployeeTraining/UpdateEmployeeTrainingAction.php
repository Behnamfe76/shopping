<?php

namespace Fereydooni\Shopping\Actions\EmployeeTraining;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Fereydooni\Shopping\DTOs\EmployeeTrainingDTO;
use Fereydooni\Shopping\Repositories\Interfaces\EmployeeTrainingRepositoryInterface;
use Fereydooni\Shopping\Enums\TrainingStatus;
use Fereydooni\Shopping\Enums\TrainingType;
use Fereydooni\Shopping\Enums\TrainingMethod;

class UpdateEmployeeTrainingAction
{
    public function __construct(
        private EmployeeTrainingRepositoryInterface $repository
    ) {}

    public function execute(EmployeeTraining $training, array $data): EmployeeTrainingDTO
    {
        // Validate the data
        $this->validateData($data, $training);

        // Check if training can be modified
        $this->checkModificationPermissions($training);

        try {
            DB::beginTransaction();

            // Update the training record
            $updated = $this->repository->update($training, $data);

            if (!$updated) {
                throw new \Exception('Failed to update employee training');
            }

            // Refresh the model to get updated data
            $training->refresh();

            // Handle status changes
            if (isset($data['status']) && $data['status'] !== $training->getOriginal('status')) {
                $this->handleStatusChange($training, $data['status']);
            }

            // Handle progress updates
            if (isset($data['hours_completed'])) {
                $this->updateProgressCalculations($training, $data['hours_completed']);
            }

            // Send notifications if needed
            $this->sendNotifications($training, $data);

            DB::commit();

            return EmployeeTrainingDTO::fromModel($training);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update employee training', [
                'training_id' => $training->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    private function validateData(array $data, EmployeeTraining $training): void
    {
        $rules = [
            'training_type' => 'sometimes|string|in:' . implode(',', TrainingType::values()),
            'training_name' => 'sometimes|string|max:255',
            'provider' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'start_date' => 'sometimes|date|before_or_equal:end_date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'completion_date' => 'sometimes|date|after_or_equal:start_date|before_or_equal:end_date',
            'status' => 'sometimes|string|in:' . implode(',', TrainingStatus::values()),
            'score' => 'sometimes|numeric|min:0|max:100',
            'grade' => 'sometimes|string|max:10',
            'certificate_number' => 'sometimes|string|max:255',
            'certificate_url' => 'sometimes|url|max:500',
            'hours_completed' => 'sometimes|numeric|min:0|max:total_hours',
            'total_hours' => 'sometimes|numeric|min:0',
            'cost' => 'sometimes|numeric|min:0',
            'is_mandatory' => 'sometimes|boolean',
            'is_certification' => 'sometimes|boolean',
            'is_renewable' => 'sometimes|boolean',
            'renewal_date' => 'sometimes|date|after:today',
            'expiry_date' => 'sometimes|date|after:today',
            'instructor' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'training_method' => 'sometimes|string|in:' . implode(',', TrainingMethod::values()),
            'materials' => 'sometimes|string|max:1000',
            'notes' => 'sometimes|string|max:1000',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'sometimes|string|max:500'
        ];

        $messages = [
            'training_type.in' => 'Invalid training type provided.',
            'status.in' => 'Invalid training status provided.',
            'training_method.in' => 'Invalid training method provided.',
            'start_date.before_or_equal' => 'Start date must be before or equal to end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'completion_date.after_or_equal' => 'Completion date must be after or equal to start date.',
            'completion_date.before_or_equal' => 'Completion date must be before or equal to end date.',
            'score.min' => 'Score must be at least 0.',
            'score.max' => 'Score cannot exceed 100.',
            'hours_completed.max' => 'Hours completed cannot exceed total hours.',
            'total_hours.min' => 'Total hours must be at least 0.',
            'cost.min' => 'Cost must be at least 0.',
            'renewal_date.after' => 'Renewal date must be in the future.',
            'expiry_date.after' => 'Expiry date must be in the future.',
            'certificate_url.url' => 'Certificate URL must be a valid URL.',
            'attachments.array' => 'Attachments must be an array.'
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }
    }

    private function checkModificationPermissions(EmployeeTraining $training): void
    {
        // Check if training is in a state that can be modified
        if ($training->status === TrainingStatus::COMPLETED && !$this->canModifyCompletedTraining()) {
            throw new \Exception('Cannot modify completed training without proper permissions');
        }

        if ($training->status === TrainingStatus::FAILED && !$this->canModifyFailedTraining()) {
            throw new \Exception('Cannot modify failed training without proper permissions');
        }
    }

    private function handleStatusChange(EmployeeTraining $training, string $newStatus): void
    {
        $oldStatus = $training->getOriginal('status');

        // Validate status transition
        $this->validateStatusTransition($oldStatus, $newStatus);

        // Handle specific status changes
        switch ($newStatus) {
            case TrainingStatus::IN_PROGRESS:
                if ($oldStatus === TrainingStatus::NOT_STARTED) {
                    $training->start_date = now();
                }
                break;

            case TrainingStatus::COMPLETED:
                if (!$training->completion_date) {
                    $training->completion_date = now();
                }
                break;

            case TrainingStatus::FAILED:
                // Handle failure logic
                break;

            case TrainingStatus::CANCELLED:
                // Handle cancellation logic
                break;
        }
    }

    private function validateStatusTransition(string $oldStatus, string $newStatus): void
    {
        $validTransitions = [
            TrainingStatus::NOT_STARTED => [TrainingStatus::IN_PROGRESS, TrainingStatus::CANCELLED],
            TrainingStatus::IN_PROGRESS => [TrainingStatus::COMPLETED, TrainingStatus::FAILED, TrainingStatus::CANCELLED],
            TrainingStatus::COMPLETED => [TrainingStatus::IN_PROGRESS], // Allow retaking
            TrainingStatus::FAILED => [TrainingStatus::IN_PROGRESS, TrainingStatus::CANCELLED], // Allow retaking
            TrainingStatus::CANCELLED => [TrainingStatus::NOT_STARTED] // Allow reactivation
        ];

        if (!isset($validTransitions[$oldStatus]) || !in_array($newStatus, $validTransitions[$oldStatus])) {
            throw new \Exception("Invalid status transition from {$oldStatus} to {$newStatus}");
        }
    }

    private function updateProgressCalculations(EmployeeTraining $training, float $hoursCompleted): void
    {
        if ($training->total_hours > 0) {
            $progressPercentage = ($hoursCompleted / $training->total_hours) * 100;
            
            // Update progress-related fields
            $training->hours_completed = $hoursCompleted;
            
            // Auto-complete if all hours are done
            if ($hoursCompleted >= $training->total_hours && $training->status === TrainingStatus::IN_PROGRESS) {
                $training->status = TrainingStatus::COMPLETED;
                $training->completion_date = now();
            }
        }
    }

    private function sendNotifications(EmployeeTraining $training, array $data): void
    {
        // Send notifications based on what was updated
        if (isset($data['status'])) {
            // Status change notification
            // $training->employee->notify(new EmployeeTrainingStatusChanged($training));
        }

        if (isset($data['score']) || isset($data['grade'])) {
            // Score/grade update notification
            // $training->employee->notify(new EmployeeTrainingScoreUpdated($training));
        }

        if (isset($data['completion_date'])) {
            // Completion notification
            // $training->employee->notify(new EmployeeTrainingCompleted($training));
        }
    }

    private function canModifyCompletedTraining(): bool
    {
        // Add your permission logic here
        return true; // For now, allow modification
    }

    private function canModifyFailedTraining(): bool
    {
        // Add your permission logic here
        return true; // For now, allow modification
    }
}
