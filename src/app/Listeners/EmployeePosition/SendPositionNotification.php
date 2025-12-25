<?php

namespace App\Listeners\EmployeePosition;

use App\Events\EmployeePosition\EmployeePositionArchived;
use App\Events\EmployeePosition\EmployeePositionCreated;
use App\Events\EmployeePosition\EmployeePositionSalaryUpdated;
use App\Events\EmployeePosition\EmployeePositionSetHiring;
use App\Events\EmployeePosition\EmployeePositionUpdated;
use App\Notifications\EmployeePosition\PositionArchived;
use App\Notifications\EmployeePosition\PositionCreated;
use App\Notifications\EmployeePosition\PositionSalaryUpdated;
use App\Notifications\EmployeePosition\PositionSetHiring;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendPositionNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            if ($event instanceof EmployeePositionCreated) {
                $this->handlePositionCreated($event);
            } elseif ($event instanceof EmployeePositionUpdated) {
                $this->handlePositionUpdated($event);
            } elseif ($event instanceof EmployeePositionSalaryUpdated) {
                $this->handleSalaryUpdated($event);
            } elseif ($event instanceof EmployeePositionSetHiring) {
                $this->handlePositionSetHiring($event);
            } elseif ($event instanceof EmployeePositionArchived) {
                $this->handlePositionArchived($event);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send position notification', [
                'event' => get_class($event),
                'position_id' => $event->position->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle position created event
     */
    protected function handlePositionCreated(EmployeePositionCreated $event): void
    {
        $position = $event->position;

        // Notify department managers
        if ($position->department && $position->department->manager) {
            $position->department->manager->notify(new PositionCreated($position));
        }

        // Notify HR team
        $this->notifyHRTeam(new PositionCreated($position));

        // Notify relevant stakeholders based on position level
        if ($position->level->isManagement()) {
            $this->notifyManagementTeam(new PositionCreated($position));
        }

        Log::info('Position created notification sent', [
            'position_id' => $position->id,
            'title' => $position->title,
        ]);
    }

    /**
     * Handle position updated event
     */
    protected function handlePositionUpdated(EmployeePositionUpdated $event): void
    {
        $position = $event->position;
        $changes = $event->changes;

        // Only send notifications for significant changes
        $significantChanges = ['status', 'level', 'department_id', 'requirements'];
        $hasSignificantChanges = ! empty(array_intersect(array_keys($changes), $significantChanges));

        if ($hasSignificantChanges) {
            // Notify department managers
            if ($position->department && $position->department->manager) {
                $position->department->manager->notify(new PositionCreated($position));
            }

            // Notify HR team
            $this->notifyHRTeam(new PositionCreated($position));
        }

        Log::info('Position updated notification sent', [
            'position_id' => $position->id,
            'title' => $position->title,
            'changes' => $changes,
        ]);
    }

    /**
     * Handle salary updated event
     */
    protected function handleSalaryUpdated(EmployeePositionSalaryUpdated $event): void
    {
        $position = $event->position;
        $salaryChanges = $event->salaryChanges;

        // Notify current employees in this position
        if ($position->employees) {
            foreach ($position->employees as $employee) {
                $employee->notify(new PositionSalaryUpdated($position, $salaryChanges));
            }
        }

        // Notify department managers
        if ($position->department && $position->department->manager) {
            $position->department->manager->notify(new PositionSalaryUpdated($position, $salaryChanges));
        }

        // Notify HR team
        $this->notifyHRTeam(new PositionSalaryUpdated($position, $salaryChanges));

        Log::info('Salary updated notification sent', [
            'position_id' => $position->id,
            'title' => $position->title,
            'salary_changes' => $salaryChanges,
        ]);
    }

    /**
     * Handle position set to hiring event
     */
    protected function handlePositionSetHiring(EmployeePositionSetHiring $event): void
    {
        $position = $event->position;
        $hiringDetails = $event->hiringDetails;

        // Notify HR team
        $this->notifyHRTeam(new PositionSetHiring($position, $hiringDetails));

        // Notify recruitment team
        $this->notifyRecruitmentTeam(new PositionSetHiring($position, $hiringDetails));

        // Notify department managers
        if ($position->department && $position->department->manager) {
            $position->department->manager->notify(new PositionSetHiring($position, $hiringDetails));
        }

        Log::info('Position set to hiring notification sent', [
            'position_id' => $position->id,
            'title' => $position->title,
            'hiring_details' => $hiringDetails,
        ]);
    }

    /**
     * Handle position archived event
     */
    protected function handlePositionArchived(EmployeePositionArchived $event): void
    {
        $position = $event->position;
        $archiveDetails = $event->archiveDetails;

        // Notify current employees in this position
        if ($position->employees) {
            foreach ($position->employees as $employee) {
                $employee->notify(new PositionArchived($position, $archiveDetails));
            }
        }

        // Notify department managers
        if ($position->department && $position->department->manager) {
            $position->department->manager->notify(new PositionArchived($position, $archiveDetails));
        }

        // Notify HR team
        $this->notifyHRTeam(new PositionArchived($position, $archiveDetails));

        Log::info('Position archived notification sent', [
            'position_id' => $position->id,
            'title' => $position->title,
            'archive_details' => $archiveDetails,
        ]);
    }

    /**
     * Notify HR team
     */
    protected function notifyHRTeam($notification): void
    {
        // This would typically query users with HR roles
        // For now, we'll use a placeholder approach
        $hrUsers = $this->getHRUsers();

        if ($hrUsers->isNotEmpty()) {
            Notification::send($hrUsers, $notification);
        }
    }

    /**
     * Notify management team
     */
    protected function notifyManagementTeam($notification): void
    {
        // This would typically query users with management roles
        $managementUsers = $this->getManagementUsers();

        if ($managementUsers->isNotEmpty()) {
            Notification::send($managementUsers, $notification);
        }
    }

    /**
     * Notify recruitment team
     */
    protected function notifyRecruitmentTeam($notification): void
    {
        // This would typically query users with recruitment roles
        $recruitmentUsers = $this->getRecruitmentUsers();

        if ($recruitmentUsers->isNotEmpty()) {
            Notification::send($recruitmentUsers, $notification);
        }
    }

    /**
     * Get HR users (placeholder implementation)
     */
    protected function getHRUsers()
    {
        // This would typically query the database for users with HR roles
        // For now, return empty collection
        return collect();
    }

    /**
     * Get management users (placeholder implementation)
     */
    protected function getManagementUsers()
    {
        // This would typically query the database for users with management roles
        // For now, return empty collection
        return collect();
    }

    /**
     * Get recruitment users (placeholder implementation)
     */
    protected function getRecruitmentUsers()
    {
        // This would typically query the database for users with recruitment roles
        // For now, return empty collection
        return collect();
    }
}
