<?php

namespace Fereydooni\Shopping\Actions\EmployeeTraining;

use Fereydooni\Shopping\DTOs\EmployeeTrainingDTO;
use Fereydooni\Shopping\Enums\TrainingStatus;
use Fereydooni\Shopping\Models\EmployeeTraining;
use Fereydooni\Shopping\Repositories\Interfaces\EmployeeTrainingRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompleteEmployeeTrainingAction
{
    public function __construct(
        private EmployeeTrainingRepositoryInterface $repository
    ) {}

    public function execute(EmployeeTraining $training, ?float $score = null, ?string $grade = null): EmployeeTrainingDTO
    {
        // Validate completion data
        $this->validateCompletionData($training, $score, $grade);

        try {
            DB::beginTransaction();

            // Complete the training
            $completed = $this->repository->complete($training, $score, $grade);

            if (! $completed) {
                throw new \Exception('Failed to complete employee training');
            }

            // Refresh the model to get updated data
            $training->refresh();

            // Generate certificate if applicable
            $this->generateCertificate($training);

            // Send completion notifications
            $this->sendCompletionNotifications($training);

            // Update employee records
            $this->updateEmployeeRecords($training);

            DB::commit();

            return EmployeeTrainingDTO::fromModel($training);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete employee training', [
                'training_id' => $training->id,
                'error' => $e->getMessage(),
                'score' => $score,
                'grade' => $grade,
            ]);
            throw $e;
        }
    }

    private function validateCompletionData(EmployeeTraining $training, ?float $score, ?string $grade): void
    {
        // Check if training is in progress
        if ($training->status !== TrainingStatus::IN_PROGRESS) {
            throw new \Exception('Training can only be completed if it is in progress');
        }

        // Validate score if provided
        if ($score !== null) {
            if ($score < 0 || $score > 100) {
                throw new \Exception('Score must be between 0 and 100');
            }
        }

        // Validate grade if provided
        if ($grade !== null) {
            $validGrades = ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'F', 'P', 'NP'];
            if (! in_array($grade, $validGrades)) {
                throw new \Exception('Invalid grade provided');
            }
        }

        // Check if training has minimum required hours
        if ($training->total_hours > 0 && $training->hours_completed < $training->total_hours) {
            throw new \Exception('Training cannot be completed until all required hours are completed');
        }
    }

    private function generateCertificate(EmployeeTraining $training): void
    {
        // Only generate certificate for certification trainings
        if (! $training->is_certification) {
            return;
        }

        // Generate certificate number
        $certificateNumber = $this->generateCertificateNumber($training);

        // Generate certificate URL (if applicable)
        $certificateUrl = $this->generateCertificateUrl($training, $certificateNumber);

        // Update training with certificate information
        $training->update([
            'certificate_number' => $certificateNumber,
            'certificate_url' => $certificateUrl,
            'completion_date' => now(),
        ]);
    }

    private function generateCertificateNumber(EmployeeTraining $training): string
    {
        // Generate a unique certificate number
        $prefix = 'CERT';
        $year = date('Y');
        $trainingId = str_pad($training->id, 6, '0', STR_PAD_LEFT);
        $employeeId = str_pad($training->employee_id, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$trainingId}-{$employeeId}";
    }

    private function generateCertificateUrl(EmployeeTraining $training, string $certificateNumber): ?string
    {
        // Generate certificate URL if you have a certificate generation service
        // For now, return null
        return null;
    }

    private function sendCompletionNotifications(EmployeeTraining $training): void
    {
        // Notify employee that training has been completed
        // $training->employee->notify(new EmployeeTrainingCompleted($training));

        // Notify manager that employee has completed training
        // if ($training->employee->manager) {
        //     $training->employee->manager->notify(new EmployeeTrainingCompleted($training));
        // }

        // Notify HR department
        // $hrUsers = User::role('hr')->get();
        // foreach ($hrUsers as $hrUser) {
        //     $hrUser->notify(new EmployeeTrainingCompleted($training));
        // }

        // If it's a certification, notify certification department
        if ($training->is_certification) {
            // $certificationUsers = User::role('certification')->get();
            // foreach ($certificationUsers as $certUser) {
            //     $certUser->notify(new EmployeeTrainingCertified($training));
            // }
        }
    }

    private function updateEmployeeRecords(EmployeeTraining $training): void
    {
        // Update employee's training status
        $training->employee->update([
            'current_training_id' => null,
            'training_status' => 'available',
        ]);

        // Update employee's skills based on training
        $this->updateEmployeeSkills($training);

        // Log the training completion in employee history
        // EmployeeTrainingHistory::create([
        //     'employee_id' => $training->employee_id,
        //     'training_id' => $training->id,
        //     'action' => 'completed',
        //     'timestamp' => now(),
        //     'notes' => 'Training completed successfully'
        // ]);
    }

    private function updateEmployeeSkills(EmployeeTraining $training): void
    {
        // Update employee skills based on training type
        // This would depend on your skills system
        // For example:
        // $skill = Skill::where('name', $training->training_name)->first();
        // if ($skill) {
        //     EmployeeSkill::updateOrCreate([
        //         'employee_id' => $training->employee_id,
        //         'skill_id' => $skill->id
        //     ], [
        //         'level' => 'trained',
        //         'certified' => $training->is_certification,
        //         'certification_date' => $training->is_certification ? now() : null,
        //         'expiry_date' => $training->expiry_date
        //     ]);
        // }
    }
}
