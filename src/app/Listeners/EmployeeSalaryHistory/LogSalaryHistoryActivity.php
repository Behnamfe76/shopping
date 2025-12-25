<?php

namespace App\Listeners\EmployeeSalaryHistory;

use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryApproved;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryCreated;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryProcessed;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryRejected;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogSalaryHistoryActivity implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        $this->logActivity($event);
    }

    private function logActivity($event): void
    {
        $eventType = $this->getEventType($event);
        $salaryHistory = $event->salaryHistory;

        $logData = [
            'event' => $eventType,
            'employee_id' => $salaryHistory->employee_id,
            'change_type' => $salaryHistory->change_type->value,
            'change_amount' => $salaryHistory->change_amount,
            'effective_date' => $salaryHistory->effective_date->format('Y-m-d'),
            'status' => $salaryHistory->status,
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ];

        if ($event instanceof EmployeeSalaryHistoryApproved) {
            $logData['approved_by'] = $event->approvedBy;
            $logData['approved_at'] = $event->approvedAt;
        } elseif ($event instanceof EmployeeSalaryHistoryRejected) {
            $logData['rejected_by'] = $event->rejectedBy;
            $logData['rejection_reason'] = $event->reason;
        }

        Log::channel('salary_history')->info("Salary History Activity: {$eventType}", $logData);
    }

    private function getEventType($event): string
    {
        return match (true) {
            $event instanceof EmployeeSalaryHistoryCreated => 'created',
            $event instanceof EmployeeSalaryHistoryUpdated => 'updated',
            $event instanceof EmployeeSalaryHistoryApproved => 'approved',
            $event instanceof EmployeeSalaryHistoryRejected => 'rejected',
            $event instanceof EmployeeSalaryHistoryProcessed => 'processed',
            default => 'unknown',
        };
    }
}
