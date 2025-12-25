<?php

namespace Fereydooni\Shopping\app\Listeners\EmployeeTimeOff;

use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffApproved;
use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffCancelled;
use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffCreated;
use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffRejected;
use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTimeOffNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            switch (true) {
                case $event instanceof EmployeeTimeOffCreated:
                    $this->handleTimeOffCreated($event);
                    break;
                case $event instanceof EmployeeTimeOffUpdated:
                    $this->handleTimeOffUpdated($event);
                    break;
                case $event instanceof EmployeeTimeOffApproved:
                    $this->handleTimeOffApproved($event);
                    break;
                case $event instanceof EmployeeTimeOffRejected:
                    $this->handleTimeOffRejected($event);
                    break;
                case $event instanceof EmployeeTimeOffCancelled:
                    $this->handleTimeOffCancelled($event);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to send time-off notification', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function handleTimeOffCreated(EmployeeTimeOffCreated $event): void
    {
        Log::info('Sending time-off created notification', [
            'time_off_id' => $event->timeOff->id,
            'employee_id' => $event->timeOff->employee_id,
        ]);

        // TODO: Implement actual notification sending
        // This would typically send notifications to approvers
    }

    protected function handleTimeOffUpdated(EmployeeTimeOffUpdated $event): void
    {
        Log::info('Sending time-off updated notification', [
            'time_off_id' => $event->timeOff->id,
            'employee_id' => $event->timeOff->employee_id,
            'changes' => $event->changes,
        ]);

        // TODO: Implement actual notification sending
        // This would typically send notifications to relevant parties
    }

    protected function handleTimeOffApproved(EmployeeTimeOffApproved $event): void
    {
        Log::info('Sending time-off approved notification', [
            'time_off_id' => $event->timeOff->id,
            'employee_id' => $event->timeOff->employee_id,
            'approved_by' => $event->approvedBy,
        ]);

        // TODO: Implement actual notification sending
        // This would typically send notifications to the employee
    }

    protected function handleTimeOffRejected(EmployeeTimeOffRejected $event): void
    {
        Log::info('Sending time-off rejected notification', [
            'time_off_id' => $event->timeOff->id,
            'employee_id' => $event->timeOff->employee_id,
            'rejected_by' => $event->rejectedBy,
            'reason' => $event->rejectionReason,
        ]);

        // TODO: Implement actual notification sending
        // This would typically send notifications to the employee
    }

    protected function handleTimeOffCancelled(EmployeeTimeOffCancelled $event): void
    {
        Log::info('Sending time-off cancelled notification', [
            'time_off_id' => $event->timeOff->id,
            'employee_id' => $event->timeOff->employee_id,
            'reason' => $event->cancellationReason,
        ]);

        // TODO: Implement actual notification sending
        // This would typically send notifications to approvers
    }
}
