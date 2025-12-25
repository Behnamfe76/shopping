<?php

namespace Fereydooni\Shopping\app\Actions\EmployeeTimeOff;

use Fereydooni\Shopping\app\DTOs\EmployeeTimeOffDTO;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeTimeOffRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveEmployeeTimeOffAction
{
    protected $repository;

    public function __construct(EmployeeTimeOffRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(EmployeeTimeOff $timeOff, int $approvedBy): EmployeeTimeOffDTO
    {
        try {
            DB::beginTransaction();

            // Validate approval permissions
            $this->validateApprovalPermissions($timeOff, $approvedBy);

            // Check if request can be approved
            $this->validateApprovalStatus($timeOff);

            // Approve the time-off request
            $this->repository->approve($timeOff, $approvedBy);

            // Update employee balance
            $this->updateEmployeeBalance($timeOff);

            // Send approval notifications
            $this->sendApprovalNotifications($timeOff, $approvedBy);

            DB::commit();

            Log::info('Time-off request approved successfully', [
                'id' => $timeOff->id,
                'employee_id' => $timeOff->employee_id,
                'approved_by' => $approvedBy,
            ]);

            return EmployeeTimeOffDTO::fromModel($timeOff->fresh());

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve time-off request', [
                'error' => $e->getMessage(),
                'time_off_id' => $timeOff->id,
                'approved_by' => $approvedBy,
            ]);
            throw $e;
        }
    }

    protected function validateApprovalPermissions(EmployeeTimeOff $timeOff, int $approvedBy): void
    {
        // This would integrate with permission system
        // For now, just check if the approver is not the same as the requester
        if ($timeOff->user_id === $approvedBy) {
            throw new \InvalidArgumentException('Users cannot approve their own time-off requests.');
        }
    }

    protected function validateApprovalStatus(EmployeeTimeOff $timeOff): void
    {
        if (! $timeOff->isPending()) {
            throw new \InvalidArgumentException('Only pending time-off requests can be approved.');
        }
    }

    protected function updateEmployeeBalance(EmployeeTimeOff $timeOff): void
    {
        // This would integrate with employee benefits/entitlements system
        // For now, just log the balance update
        Log::info('Employee balance updated for approved time-off', [
            'employee_id' => $timeOff->employee_id,
            'days_used' => $timeOff->total_days,
            'hours_used' => $timeOff->total_hours,
        ]);
    }

    protected function sendApprovalNotifications(EmployeeTimeOff $timeOff, int $approvedBy): void
    {
        // This would integrate with notification system
        // For now, just log the notification
        Log::info('Approval notification sent', [
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id,
            'approved_by' => $approvedBy,
        ]);
    }
}
