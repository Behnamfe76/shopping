<?php

namespace Fereydooni\Shopping\app\Actions\EmployeeTimeOff;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Fereydooni\Shopping\app\DTOs\EmployeeTimeOffDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeTimeOffRepositoryInterface;
use Fereydooni\Shopping\app\Enums\TimeOffStatus;
use Fereydooni\Shopping\app\Enums\TimeOffType;
use Carbon\Carbon;

class CreateEmployeeTimeOffAction
{
    protected $repository;

    public function __construct(EmployeeTimeOffRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data): EmployeeTimeOffDTO
    {
        try {
            DB::beginTransaction();

            // Validate input data
            $this->validateData($data);

            // Check for overlapping requests
            $this->checkOverlappingRequests($data['employee_id'], $data['start_date'], $data['end_date']);

            // Calculate total days and hours
            $data = $this->calculateDurations($data);

            // Set default status
            $data['status'] = $data['status'] ?? TimeOffStatus::PENDING->value;

            // Create time-off request
            $timeOff = $this->repository->create($data);

            // Send notifications
            $this->sendNotifications($timeOff);

            DB::commit();

            Log::info('Time-off request created successfully', [
                'id' => $timeOff->id,
                'employee_id' => $timeOff->employee_id,
                'type' => $timeOff->time_off_type->value
            ]);

            return EmployeeTimeOffDTO::fromModel($timeOff);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create time-off request', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    protected function validateData(array $data): void
    {
        $rules = [
            'employee_id' => 'required|integer|exists:employees,id',
            'user_id' => 'required|integer|exists:users,id',
            'time_off_type' => 'required|string|in:' . implode(',', array_column(TimeOffType::cases(), 'value')),
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s|after:start_time',
            'reason' => 'required|string|min:3|max:500',
            'description' => 'nullable|string|max:1000',
            'is_half_day' => 'boolean',
            'is_urgent' => 'boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string|max:255',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }
    }

    protected function checkOverlappingRequests(int $employeeId, string $startDate, string $endDate): void
    {
        $overlapping = $this->repository->findOverlapping($employeeId, $startDate, $endDate);

        if ($overlapping->isNotEmpty()) {
            throw new \InvalidArgumentException('Time-off request overlaps with existing approved or pending requests.');
        }
    }

    protected function calculateDurations(array $data): array
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        $totalDays = $startDate->diffInDays($endDate) + 1;

        if ($data['is_half_day'] ?? false) {
            $totalDays = $totalDays * 0.5;
        }

        $data['total_days'] = $totalDays;
        $data['total_hours'] = $totalDays * 8; // Assuming 8-hour workday

        return $data;
    }

    protected function sendNotifications($timeOff): void
    {
        // This would integrate with notification system
        // For now, just log the notification
        Log::info('Notification sent for new time-off request', [
            'time_off_id' => $timeOff->id,
            'employee_id' => $timeOff->employee_id,
            'type' => $timeOff->time_off_type->value
        ]);
    }
}

