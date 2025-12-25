<?php

namespace Fereydooni\Shopping\app\Actions\EmployeeTimeOff;

use Exception;
use Fereydooni\Shopping\app\DTOs\EmployeeTimeOffDTO;
use Fereydooni\Shopping\app\Models\EmployeeTimeOff;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeTimeOffRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateEmployeeTimeOffAction
{
    public function __construct(
        private EmployeeTimeOffRepositoryInterface $repository
    ) {}

    public function execute(EmployeeTimeOff $timeOff, array $data): EmployeeTimeOffDTO
    {
        try {
            DB::beginTransaction();

            // Check if request can be modified
            if (! $this->canBeModified($timeOff)) {
                throw new Exception('Time-off request cannot be modified in its current status.');
            }

            // Validate update data
            $this->validateUpdateData($data, $timeOff);

            // Check for overlapping requests if dates are being changed
            if (isset($data['start_date']) || isset($data['end_date'])) {
                $this->checkForOverlappingRequests($timeOff, $data);
            }

            // Update the time-off request
            $updated = $this->repository->update($timeOff, $data);

            if (! $updated) {
                throw new Exception('Failed to update time-off request.');
            }

            // Get updated DTO
            $updatedDTO = $this->repository->findDTO($timeOff->id);

            if (! $updatedDTO) {
                throw new Exception('Failed to retrieve updated time-off request.');
            }

            // Fire event (commented out until event class is created)
            // event(new EmployeeTimeOffUpdated($updatedDTO));

            DB::commit();

            Log::info('Time-off request updated successfully', [
                'time_off_id' => $timeOff->id,
                'employee_id' => $timeOff->employee_id,
                'updated_by' => auth()->id() ?? 'system',
            ]);

            return $updatedDTO;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update time-off request', [
                'time_off_id' => $timeOff->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function canBeModified(EmployeeTimeOff $timeOff): bool
    {
        // Can only modify pending requests
        return $timeOff->status === 'pending';
    }

    private function validateUpdateData(array $data, EmployeeTimeOff $timeOff): void
    {
        // Basic validation rules
        $rules = [
            'start_date' => 'sometimes|date|after_or_equal:today',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'reason' => 'sometimes|string|max:500',
            'description' => 'sometimes|string|max:1000',
            'is_half_day' => 'sometimes|boolean',
            'is_urgent' => 'sometimes|boolean',
        ];

        // Validate data
        $validator = validator($data, $rules);

        if ($validator->fails()) {
            throw new Exception('Validation failed: '.$validator->errors()->first());
        }
    }

    private function checkForOverlappingRequests(EmployeeTimeOff $timeOff, array $data): void
    {
        $startDate = $data['start_date'] ?? $timeOff->start_date;
        $endDate = $data['end_date'] ?? $timeOff->end_date;

        $overlapping = $this->repository->findOverlapping(
            $timeOff->employee_id,
            $startDate,
            $endDate
        )->filter(function ($item) use ($timeOff) {
            return $item->id !== $timeOff->id;
        });

        if ($overlapping->isNotEmpty()) {
            throw new Exception('Time-off request overlaps with existing approved or pending requests.');
        }
    }
}
