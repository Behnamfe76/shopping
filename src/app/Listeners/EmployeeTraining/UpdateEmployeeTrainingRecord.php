<?php

namespace Fereydooni\Shopping\Listeners\EmployeeTraining;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Fereydooni\Shopping\Events\EmployeeTraining\EmployeeTrainingCreated;
use Fereydooni\Shopping\Events\EmployeeTraining\EmployeeTrainingUpdated;
use Fereydooni\Shopping\Events\EmployeeTraining\EmployeeTrainingStarted;
use Fereydooni\Shopping\Events\EmployeeTraining\EmployeeTrainingCompleted;
use Fereydooni\Shopping\Events\EmployeeTraining\EmployeeTrainingFailed;
use Fereydooni\Shopping\Events\EmployeeTraining\EmployeeTrainingRenewed;

class UpdateEmployeeTrainingRecord implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        switch (get_class($event)) {
            case EmployeeTrainingCreated::class:
                $this->handleTrainingCreated($event);
                break;
            case EmployeeTrainingStarted::class:
                $this->handleTrainingStarted($event);
                break;
            case EmployeeTrainingCompleted::class:
                $this->handleTrainingCompleted($event);
                break;
            case EmployeeTrainingFailed::class:
                $this->handleTrainingFailed($event);
                break;
            case EmployeeTrainingRenewed::class:
                $this->handleTrainingRenewed($event);
                break;
        }
    }

    private function handleTrainingCreated(EmployeeTrainingCreated $event): void
    {
        $training = $event->training;

        // Update employee's current training status
        if ($training->employee) {
            $training->employee->update([
                'current_training_id' => $training->id,
                'training_status' => 'assigned'
            ]);
        }

        // Create training history record
        $this->createTrainingHistory($training, 'created', 'Training assigned to employee');
    }

    private function handleTrainingStarted(EmployeeTrainingStarted $event): void
    {
        $training = $event->training;

        // Update employee's current training status
        if ($training->employee) {
            $training->employee->update([
                'current_training_id' => $training->id,
                'training_status' => 'in_progress'
            ]);
        }

        // Create training history record
        $this->createTrainingHistory($training, 'started', 'Training started');
    }

    private function handleTrainingCompleted(EmployeeTrainingCompleted $event): void
    {
        $training = $event->training;
        $score = $event->score;
        $grade = $event->grade;

        // Update employee's current training status
        if ($training->employee) {
            $training->employee->update([
                'current_training_id' => null,
                'training_status' => 'available'
            ]);

            // Update employee's training statistics
            $this->updateEmployeeTrainingStats($training->employee, $training, $score);
        }

        // Create training history record
        $this->createTrainingHistory($training, 'completed', 'Training completed successfully', [
            'score' => $score,
            'grade' => $grade
        ]);

        // Update employee skills if applicable
        $this->updateEmployeeSkills($training);
    }

    private function handleTrainingFailed(EmployeeTrainingFailed $event): void
    {
        $training = $event->training;
        $reason = $event->reason;

        // Update employee's current training status
        if ($training->employee) {
            $training->employee->update([
                'current_training_id' => null,
                'training_status' => 'available'
            ]);
        }

        // Create training history record
        $this->createTrainingHistory($training, 'failed', 'Training failed', [
            'reason' => $reason
        ]);
    }

    private function handleTrainingRenewed(EmployeeTrainingRenewed $event): void
    {
        $training = $event->training;
        $renewalDate = $event->renewalDate;

        // Create training history record
        $this->createTrainingHistory($training, 'renewed', 'Training certification renewed', [
            'renewal_date' => $renewalDate
        ]);

        // Update employee's certification status
        $this->updateEmployeeCertificationStatus($training);
    }

    private function createTrainingHistory($training, string $action, string $notes, array $metadata = []): void
    {
        // Create training history record
        // EmployeeTrainingHistory::create([
        //     'employee_id' => $training->employee_id,
        //     'training_id' => $training->id,
        //     'action' => $action,
        //     'notes' => $notes,
        //     'metadata' => $metadata,
        //     'timestamp' => now(),
        //     'user_id' => auth()->id()
        // ]);
    }

    private function updateEmployeeTrainingStats($employee, $training, ?float $score): void
    {
        // Update employee's training statistics
        $stats = [
            'total_trainings_completed' => $employee->total_trainings_completed + 1,
            'total_training_hours' => $employee->total_training_hours + $training->total_hours,
            'total_training_cost' => $employee->total_training_cost + $training->cost,
        ];

        // Update average score if score is provided
        if ($score !== null) {
            $currentTotalScore = $employee->average_training_score * $employee->total_trainings_completed;
            $newTotalScore = $currentTotalScore + $score;
            $stats['average_training_score'] = $newTotalScore / ($employee->total_trainings_completed + 1);
        }

        $employee->update($stats);
    }

    private function updateEmployeeSkills($training): void
    {
        // Update employee skills based on training
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
        //         'expiry_date' => $training->expiry_date,
        //         'last_updated' => now()
        //     ]);
        // }
    }

    private function updateEmployeeCertificationStatus($training): void
    {
        // Update employee's certification status
        if ($training->employee && $training->is_certification) {
            $training->employee->update([
                'certifications_count' => $training->employee->certifications_count + 1,
                'last_certification_date' => now()
            ]);
        }
    }
}
