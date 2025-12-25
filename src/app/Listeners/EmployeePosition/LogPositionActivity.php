<?php

namespace App\Listeners\EmployeePosition;

use App\Events\EmployeePosition\EmployeePositionArchived;
use App\Events\EmployeePosition\EmployeePositionCreated;
use App\Events\EmployeePosition\EmployeePositionSalaryUpdated;
use App\Events\EmployeePosition\EmployeePositionSetHiring;
use App\Events\EmployeePosition\EmployeePositionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogPositionActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            if ($event instanceof EmployeePositionCreated) {
                $this->logPositionCreated($event);
            } elseif ($event instanceof EmployeePositionUpdated) {
                $this->logPositionUpdated($event);
            } elseif ($event instanceof EmployeePositionSalaryUpdated) {
                $this->logSalaryUpdated($event);
            } elseif ($event instanceof EmployeePositionSetHiring) {
                $this->logPositionSetHiring($event);
            } elseif ($event instanceof EmployeePositionArchived) {
                $this->logPositionArchived($event);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log position activity', [
                'event' => get_class($event),
                'position_id' => $event->position->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Log position created activity
     */
    protected function logPositionCreated(EmployeePositionCreated $event): void
    {
        $position = $event->position;
        $metadata = $event->metadata;

        $this->createActivityLog([
            'position_id' => $position->id,
            'action' => 'created',
            'description' => "Position '{$position->title}' was created",
            'user_id' => $metadata['created_by'] ?? null,
            'department_id' => $position->department_id,
            'level' => $position->level->value,
            'status' => $position->status->value,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        Log::info('Position activity logged: created', [
            'position_id' => $position->id,
            'title' => $position->title,
            'user_id' => $metadata['created_by'] ?? null,
        ]);
    }

    /**
     * Log position updated activity
     */
    protected function logPositionUpdated(EmployeePositionUpdated $event): void
    {
        $position = $event->position;
        $changes = $event->changes;
        $metadata = $event->metadata;

        $this->createActivityLog([
            'position_id' => $position->id,
            'action' => 'updated',
            'description' => "Position '{$position->title}' was updated",
            'user_id' => $metadata['updated_by'] ?? null,
            'department_id' => $position->department_id,
            'level' => $position->level->value,
            'status' => $position->status->value,
            'changes' => $changes,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        Log::info('Position activity logged: updated', [
            'position_id' => $position->id,
            'title' => $position->title,
            'changes' => $changes,
            'user_id' => $metadata['updated_by'] ?? null,
        ]);
    }

    /**
     * Log salary updated activity
     */
    protected function logSalaryUpdated(EmployeePositionSalaryUpdated $event): void
    {
        $position = $event->position;
        $salaryChanges = $event->salaryChanges;
        $metadata = $event->metadata;

        $this->createActivityLog([
            'position_id' => $position->id,
            'action' => 'salary_updated',
            'description' => "Salary range updated for position '{$position->title}'",
            'user_id' => $metadata['updated_by'] ?? null,
            'department_id' => $position->department_id,
            'level' => $position->level->value,
            'status' => $position->status->value,
            'changes' => $salaryChanges,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        Log::info('Position activity logged: salary updated', [
            'position_id' => $position->id,
            'title' => $position->title,
            'salary_changes' => $salaryChanges,
            'user_id' => $metadata['updated_by'] ?? null,
        ]);
    }

    /**
     * Log position set to hiring activity
     */
    protected function logPositionSetHiring(EmployeePositionSetHiring $event): void
    {
        $position = $event->position;
        $hiringDetails = $event->hiringDetails;
        $metadata = $event->metadata;

        $this->createActivityLog([
            'position_id' => $position->id,
            'action' => 'set_hiring',
            'description' => "Position '{$position->title}' was set to hiring status",
            'user_id' => $metadata['set_by'] ?? null,
            'department_id' => $position->department_id,
            'level' => $position->level->value,
            'status' => $position->status->value,
            'changes' => $hiringDetails,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        Log::info('Position activity logged: set to hiring', [
            'position_id' => $position->id,
            'title' => $position->title,
            'hiring_details' => $hiringDetails,
            'user_id' => $metadata['set_by'] ?? null,
        ]);
    }

    /**
     * Log position archived activity
     */
    protected function logPositionArchived(EmployeePositionArchived $event): void
    {
        $position = $event->position;
        $archiveDetails = $event->archiveDetails;
        $metadata = $event->metadata;

        $this->createActivityLog([
            'position_id' => $position->id,
            'action' => 'archived',
            'description' => "Position '{$position->title}' was archived",
            'user_id' => $metadata['archived_by'] ?? null,
            'department_id' => $position->department_id,
            'level' => $position->level->value,
            'status' => $position->status->value,
            'changes' => $archiveDetails,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        Log::info('Position activity logged: archived', [
            'position_id' => $position->id,
            'title' => $position->title,
            'archive_details' => $archiveDetails,
            'user_id' => $metadata['archived_by'] ?? null,
        ]);
    }

    /**
     * Create activity log entry
     */
    protected function createActivityLog(array $data): void
    {
        try {
            // Check if position_activity_logs table exists
            if (DB::getSchemaBuilder()->hasTable('position_activity_logs')) {
                DB::table('position_activity_logs')->insert([
                    'position_id' => $data['position_id'],
                    'action' => $data['action'],
                    'description' => $data['description'],
                    'user_id' => $data['user_id'],
                    'department_id' => $data['department_id'],
                    'level' => $data['level'],
                    'status' => $data['status'],
                    'changes' => json_encode($data['changes'] ?? []),
                    'metadata' => json_encode($data['metadata'] ?? []),
                    'ip_address' => $data['ip_address'],
                    'user_agent' => $data['user_agent'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Fallback to general activity logs table if it exists
                if (DB::getSchemaBuilder()->hasTable('activity_logs')) {
                    DB::table('activity_logs')->insert([
                        'log_name' => 'employee_positions',
                        'description' => $data['description'],
                        'subject_type' => 'App\\Models\\EmployeePosition',
                        'subject_id' => $data['position_id'],
                        'causer_type' => 'App\\Models\\User',
                        'causer_id' => $data['user_id'],
                        'properties' => json_encode([
                            'action' => $data['action'],
                            'department_id' => $data['department_id'],
                            'level' => $data['level'],
                            'status' => $data['status'],
                            'changes' => $data['changes'] ?? [],
                            'metadata' => $data['metadata'] ?? [],
                            'ip_address' => $data['ip_address'],
                            'user_agent' => $data['user_agent'],
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // If no activity log tables exist, just log to Laravel log
                    Log::info('Position activity (no activity log table): '.$data['description'], $data);
                }
            }
        } catch (\Exception $e) {
            // If database logging fails, fallback to Laravel log
            Log::warning('Failed to log position activity to database, using fallback logging', [
                'error' => $e->getMessage(),
                'activity_data' => $data,
            ]);

            // Log the activity to Laravel log as fallback
            Log::info('Position activity (fallback): '.$data['description'], $data);
        }
    }

    /**
     * Get request IP address safely
     */
    protected function getRequestIP(): ?string
    {
        try {
            return request()->ip();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get request user agent safely
     */
    protected function getRequestUserAgent(): ?string
    {
        try {
            return request()->userAgent();
        } catch (\Exception $e) {
            return null;
        }
    }
}
