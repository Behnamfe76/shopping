<?php

namespace Fereydooni\Shopping\Actions\EmployeeTraining;

use Fereydooni\Shopping\Enums\TrainingStatus;
use Fereydooni\Shopping\Models\EmployeeTraining;

class CalculateTrainingProgressAction
{
    public function execute(EmployeeTraining $training): array
    {
        return [
            'completion_percentage' => $this->calculateCompletionPercentage($training),
            'remaining_hours' => $this->calculateRemainingHours($training),
            'time_to_completion' => $this->calculateTimeToCompletion($training),
            'progress_status' => $this->getProgressStatus($training),
            'estimated_completion_date' => $this->getEstimatedCompletionDate($training),
            'hours_per_day_needed' => $this->calculateHoursPerDayNeeded($training),
            'is_on_track' => $this->isOnTrack($training),
            'risk_level' => $this->getRiskLevel($training),
        ];
    }

    private function calculateCompletionPercentage(EmployeeTraining $training): float
    {
        if ($training->total_hours <= 0) {
            return 0.0;
        }

        $percentage = ($training->hours_completed / $training->total_hours) * 100;

        return round($percentage, 2);
    }

    private function calculateRemainingHours(EmployeeTraining $training): float
    {
        return max(0, $training->total_hours - $training->hours_completed);
    }

    private function calculateTimeToCompletion(EmployeeTraining $training): ?int
    {
        if ($training->status !== TrainingStatus::IN_PROGRESS) {
            return null;
        }

        if (! $training->end_date) {
            return null;
        }

        $endDate = \Carbon\Carbon::parse($training->end_date);
        $now = \Carbon\Carbon::now();

        if ($endDate->isPast()) {
            return 0; // Overdue
        }

        return $endDate->diffInDays($now);
    }

    private function getProgressStatus(EmployeeTraining $training): string
    {
        if ($training->status !== TrainingStatus::IN_PROGRESS) {
            return $training->status;
        }

        $completionPercentage = $this->calculateCompletionPercentage($training);
        $timeToCompletion = $this->calculateTimeToCompletion($training);

        if ($completionPercentage >= 100) {
            return 'ready_for_completion';
        }

        if ($timeToCompletion === 0) {
            return 'overdue';
        }

        if ($timeToCompletion <= 7) {
            return 'urgent';
        }

        if ($timeToCompletion <= 14) {
            return 'due_soon';
        }

        return 'in_progress';
    }

    private function getEstimatedCompletionDate(EmployeeTraining $training): ?string
    {
        if ($training->status !== TrainingStatus::IN_PROGRESS) {
            return null;
        }

        $remainingHours = $this->calculateRemainingHours($training);
        $hoursPerDay = $this->getAverageHoursPerDay($training);

        if ($hoursPerDay <= 0) {
            return $training->end_date;
        }

        $daysNeeded = ceil($remainingHours / $hoursPerDay);
        $estimatedDate = \Carbon\Carbon::now()->addDays($daysNeeded);

        return $estimatedDate->format('Y-m-d');
    }

    private function calculateHoursPerDayNeeded(EmployeeTraining $training): float
    {
        if ($training->status !== TrainingStatus::IN_PROGRESS) {
            return 0.0;
        }

        $remainingHours = $this->calculateRemainingHours($training);
        $timeToCompletion = $this->calculateTimeToCompletion($training);

        if ($timeToCompletion <= 0) {
            return $remainingHours; // Need to complete immediately
        }

        return round($remainingHours / $timeToCompletion, 2);
    }

    private function isOnTrack(EmployeeTraining $training): bool
    {
        if ($training->status !== TrainingStatus::IN_PROGRESS) {
            return true;
        }

        $completionPercentage = $this->calculateCompletionPercentage($training);
        $timeToCompletion = $this->calculateTimeToCompletion($training);

        if ($timeToCompletion === null) {
            return true;
        }

        // Calculate expected progress based on time elapsed
        $totalDays = $this->getTotalTrainingDays($training);
        $elapsedDays = $totalDays - $timeToCompletion;
        $expectedProgress = ($elapsedDays / $totalDays) * 100;

        return $completionPercentage >= $expectedProgress;
    }

    private function getRiskLevel(EmployeeTraining $training): string
    {
        if ($training->status !== TrainingStatus::IN_PROGRESS) {
            return 'none';
        }

        $completionPercentage = $this->calculateCompletionPercentage($training);
        $timeToCompletion = $this->calculateTimeToCompletion($training);
        $hoursPerDayNeeded = $this->calculateHoursPerDayNeeded($training);

        if ($timeToCompletion === 0) {
            return 'critical';
        }

        if ($timeToCompletion <= 3 || $hoursPerDayNeeded > 8) {
            return 'high';
        }

        if ($timeToCompletion <= 7 || $hoursPerDayNeeded > 4) {
            return 'medium';
        }

        if ($completionPercentage < 25 && $timeToCompletion <= 14) {
            return 'low';
        }

        return 'none';
    }

    private function getAverageHoursPerDay(EmployeeTraining $training): float
    {
        if (! $training->start_date) {
            return 0.0;
        }

        $startDate = \Carbon\Carbon::parse($training->start_date);
        $now = \Carbon\Carbon::now();
        $elapsedDays = max(1, $startDate->diffInDays($now));

        return round($training->hours_completed / $elapsedDays, 2);
    }

    private function getTotalTrainingDays(EmployeeTraining $training): int
    {
        if (! $training->start_date || ! $training->end_date) {
            return 0;
        }

        $startDate = \Carbon\Carbon::parse($training->start_date);
        $endDate = \Carbon\Carbon::parse($training->end_date);

        return max(1, $startDate->diffInDays($endDate));
    }
}
