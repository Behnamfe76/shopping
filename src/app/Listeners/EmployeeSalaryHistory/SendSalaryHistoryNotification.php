<?php

namespace App\Listeners\EmployeeSalaryHistory;

use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryApproved;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryCreated;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSalaryHistoryNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        // Handle different event types
        if ($event instanceof EmployeeSalaryHistoryCreated) {
            $this->handleCreated($event);
        } elseif ($event instanceof EmployeeSalaryHistoryApproved) {
            $this->handleApproved($event);
        } elseif ($event instanceof EmployeeSalaryHistoryRejected) {
            $this->handleRejected($event);
        }
    }

    private function handleCreated($event): void
    {
        // Send notification for salary change request
        // Implementation would go here
    }

    private function handleApproved($event): void
    {
        // Send notification for approved salary change
        // Implementation would go here
    }

    private function handleRejected($event): void
    {
        // Send notification for rejected salary change
        // Implementation would go here
    }
}
