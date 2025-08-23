<?php

namespace Fereydooni\Shopping\app\Actions\EmployeeTimeOff;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Fereydooni\Shopping\app\DTOs\EmployeeTimeOffDTO;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeTimeOffRepositoryInterface;
use Fereydooni\Shopping\app\Enums\TimeOffStatus;
use Carbon\Carbon;

class CancelEmployeeTimeOffAction
{
    protected $repository;

    public function __construct(EmployeeTimeOffRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(EmployeeTimeOff $timeOff, array $data = []): EmployeeTimeOffDTO
    {
        try {
            DB::beginTransaction();

            // Check if time-off can be cancelled
            $this->validateCancellationEligibility($timeOff);

            // Cancel the time-off request
            $cancelled = $this->repository->cancel($timeOff, $data['cancellation_reason'] ?? null);

            if (!$cancelled) {
                throw new \RuntimeException('Failed to cancel time-off request');
            }

            // Send cancellation notifications
            $this->sendCancellationNotifications($timeOff);

            DB::commit();

            Log::info('Time-off request cancelled successfully', [
                'id' => $timeOff->id,
                'employee_id' => $timeOff->employee_id,
                'reason' => $data['cancellation_reason'] ?? null
            ]);

            return EmployeeTimeOffDTO::fromModel($timeOff->fresh());

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel time-off request', [
                'error' => $e->getMessage(),
                'time_off_id' => $timeOff->id,
                'data' => $data
            ]);
            throw $e;
        }
    }

    protected function validateCancellationEligibility(EmployeeTimeOff $timeOff): void
    {
        if ($timeOff->status === TimeOffStatus::CANCELLED) {
            throw new \InvalidArgumentException('Time-off request is already cancelled.');
        }

        if ($timeOff->status === TimeOffStatus::REJECTED) {
            throw new \InvalidArgumentException('Rejected time-off requests cannot be cancelled.');
        }

        // Check if the start date has already passed
        if (Carbon::parse($timeOff->start_date)->isPast()) {
            throw new \InvalidArgumentException('Cannot cancel time-off requests that have already started.');
        }
    }

    protected function sendCancellationNotifications(EmployeeTimeOff $timeOff): void
    {
        // This would typically dispatch events or send notifications
        // Implementation depends on your notification system
        Log::info('Cancellation notifications would be sent here', [
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id
        ]);
    }
}
