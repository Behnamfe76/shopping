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

class RejectEmployeeTimeOffAction
{
    protected $repository;

    public function __construct(EmployeeTimeOffRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(EmployeeTimeOff $timeOff, array $data): EmployeeTimeOffDTO
    {
        try {
            DB::beginTransaction();

            // Validate rejection data
            $this->validateRejectionData($data);

            // Check if time-off can be rejected
            $this->validateRejectionEligibility($timeOff);

            // Reject the time-off request
            $rejected = $this->repository->reject($timeOff, $data['rejected_by'], $data['rejection_reason'] ?? null);

            if (!$rejected) {
                throw new \RuntimeException('Failed to reject time-off request');
            }

            // Send rejection notifications
            $this->sendRejectionNotifications($timeOff);

            DB::commit();

            Log::info('Time-off request rejected successfully', [
                'id' => $timeOff->id,
                'employee_id' => $timeOff->employee_id,
                'rejected_by' => $data['rejected_by'],
                'reason' => $data['rejection_reason'] ?? null
            ]);

            return EmployeeTimeOffDTO::fromModel($timeOff->fresh());

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject time-off request', [
                'error' => $e->getMessage(),
                'time_off_id' => $timeOff->id,
                'data' => $data
            ]);
            throw $e;
        }
    }

    protected function validateRejectionData(array $data): void
    {
        $rules = [
            'rejected_by' => 'required|integer|exists:users,id',
            'rejection_reason' => 'nullable|string|min:3|max:500',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }
    }

    protected function validateRejectionEligibility(EmployeeTimeOff $timeOff): void
    {
        if ($timeOff->status !== TimeOffStatus::PENDING) {
            throw new \InvalidArgumentException('Only pending time-off requests can be rejected.');
        }

        if ($timeOff->approved_at !== null) {
            throw new \InvalidArgumentException('Approved time-off requests cannot be rejected.');
        }
    }

    protected function sendRejectionNotifications(EmployeeTimeOff $timeOff): void
    {
        // This would typically dispatch events or send notifications
        // Implementation depends on your notification system
        Log::info('Rejection notifications would be sent here', [
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id
        ]);
    }
}
