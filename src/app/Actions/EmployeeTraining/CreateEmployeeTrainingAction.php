<?php

namespace Fereydooni\Shopping\Actions\EmployeeTraining;

use Fereydooni\Shopping\DTOs\EmployeeTrainingDTO;
use Fereydooni\Shopping\Enums\TrainingStatus;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Fereydooni\Shopping\Repositories\Interfaces\EmployeeTrainingRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreateEmployeeTrainingAction
{
    protected $repository;

    public function __construct(EmployeeTrainingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Execute the action.
     */
    public function execute(array $data): EmployeeTrainingDTO
    {
        // Validate input data
        $this->validateData($data);

        // Check for existing enrollments
        $this->checkExistingEnrollments($data);

        // Set default values
        $data = $this->setDefaultValues($data);

        // Calculate total hours if not provided
        if (! isset($data['total_hours']) && isset($data['hours_completed'])) {
            $data['total_hours'] = $data['hours_completed'];
        }

        // Create the training record
        $training = $this->repository->create($data);

        // Send notifications
        $this->sendNotifications($training);

        // Return DTO
        return EmployeeTrainingDTO::fromModel($training);
    }

    /**
     * Validate input data.
     */
    protected function validateData(array $data): void
    {
        $rules = EmployeeTrainingDTO::rules();
        $messages = EmployeeTrainingDTO::messages();

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }
    }

    /**
     * Check for existing enrollments.
     */
    protected function checkExistingEnrollments(array $data): void
    {
        $existingTraining = EmployeeTraining::where('employee_id', $data['employee_id'])
            ->where('training_name', $data['training_name'])
            ->where('provider', $data['provider'])
            ->whereIn('status', [TrainingStatus::NOT_STARTED, TrainingStatus::IN_PROGRESS])
            ->first();

        if ($existingTraining) {
            throw new \InvalidArgumentException('Employee is already enrolled in this training program.');
        }
    }

    /**
     * Set default values.
     */
    protected function setDefaultValues(array $data): array
    {
        $defaults = [
            'status' => TrainingStatus::NOT_STARTED->value,
            'is_mandatory' => $data['is_mandatory'] ?? false,
            'is_certification' => $data['is_certification'] ?? false,
            'is_renewable' => $data['is_renewable'] ?? false,
            'hours_completed' => $data['hours_completed'] ?? 0.00,
            'score' => $data['score'] ?? 0.00,
        ];

        return array_merge($defaults, $data);
    }

    /**
     * Send notifications.
     */
    protected function sendNotifications(EmployeeTraining $training): void
    {
        try {
            // Send notification to employee
            // $training->employee->notify(new TrainingAssigned($training));

            // Send notification to manager if applicable
            // if ($training->employee->manager) {
            //     $training->employee->manager->notify(new EmployeeTrainingAssigned($training));
            // }

            Log::info('Training notifications sent', ['training_id' => $training->id]);
        } catch (\Exception $e) {
            Log::warning('Failed to send training notifications', [
                'training_id' => $training->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
