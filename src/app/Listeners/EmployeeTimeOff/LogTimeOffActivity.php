<?php

namespace Fereydooni\Shopping\app\Listeners\EmployeeTimeOff;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffCreated;
use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffUpdated;
use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffApproved;
use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffRejected;
use Fereydooni\Shopping\app\Events\EmployeeTimeOff\EmployeeTimeOffCancelled;

class LogTimeOffActivity implements ShouldQueue
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
                    $this->logTimeOffCreated($event);
                    break;
                case $event instanceof EmployeeTimeOffUpdated:
                    $this->logTimeOffUpdated($event);
                    break;
                case $event instanceof EmployeeTimeOffApproved:
                    $this->logTimeOffApproved($event);
                    break;
                case $event instanceof EmployeeTimeOffRejected:
                    $this->logTimeOffRejected($event);
                    break;
                case $event instanceof EmployeeTimeOffCancelled:
                    $this->logTimeOffCancelled($event);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Failed to log time-off activity', [
                'event' => get_class($event),
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function logTimeOffCreated(EmployeeTimeOffCreated $event): void
    {
        $timeOff = $event->timeOff;

        Log::info('Time-off request created', [
            'action' => 'time_off_created',
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id,
            'user_id' => $timeOff->user_id,
            'time_off_type' => $timeOff->time_off_type,
            'start_date' => $timeOff->start_date,
            'end_date' => $timeOff->end_date,
            'total_days' => $timeOff->total_days,
            'total_hours' => $timeOff->total_hours,
            'reason' => $timeOff->reason,
            'status' => $timeOff->status,
            'is_urgent' => $timeOff->is_urgent,
            'created_at' => $timeOff->created_at,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // TODO: Store in activity log table for audit trail
    }

    protected function logTimeOffUpdated(EmployeeTimeOffUpdated $event): void
    {
        $timeOff = $event->timeOff;
        $changes = $event->changes ?? [];

        Log::info('Time-off request updated', [
            'action' => 'time_off_updated',
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id,
            'user_id' => $timeOff->user_id,
            'changes' => $changes,
            'updated_at' => $timeOff->updated_at,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // TODO: Store in activity log table for audit trail
    }

    protected function logTimeOffApproved(EmployeeTimeOffApproved $event): void
    {
        $timeOff = $event->timeOff;

        Log::info('Time-off request approved', [
            'action' => 'time_off_approved',
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id,
            'approved_by' => $event->approvedBy,
            'approved_at' => $timeOff->approved_at,
            'time_off_type' => $timeOff->time_off_type,
            'total_days' => $timeOff->total_days,
            'total_hours' => $timeOff->total_hours,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // TODO: Store in activity log table for audit trail
    }

    protected function logTimeOffRejected(EmployeeTimeOffRejected $event): void
    {
        $timeOff = $event->timeOff;

        Log::info('Time-off request rejected', [
            'action' => 'time_off_rejected',
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id,
            'rejected_by' => $event->rejectedBy,
            'rejected_at' => $timeOff->rejected_at,
            'rejection_reason' => $event->rejectionReason,
            'time_off_type' => $timeOff->time_off_type,
            'total_days' => $timeOff->total_days,
            'total_hours' => $timeOff->total_hours,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // TODO: Store in activity log table for audit trail
    }

    protected function logTimeOffCancelled(EmployeeTimeOffCancelled $event): void
    {
        $timeOff = $event->timeOff;

        Log::info('Time-off request cancelled', [
            'action' => 'time_off_cancelled',
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id,
            'cancellation_reason' => $event->cancellationReason,
            'cancelled_at' => $timeOff->cancelled_at ?? now(),
            'time_off_type' => $timeOff->time_off_type,
            'total_days' => $timeOff->total_days,
            'total_hours' => $timeOff->total_hours,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        // TODO: Store in activity log table for audit trail
    }
}
