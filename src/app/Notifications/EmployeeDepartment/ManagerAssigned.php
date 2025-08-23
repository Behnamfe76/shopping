<?php

namespace App\Notifications\EmployeeDepartment;

use App\Models\EmployeeDepartment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ManagerAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public $department;
    public $assignmentType;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeDepartment $department, string $assignmentType = 'assigned')
    {
        $this->department = $department;
        $this->assignmentType = $assignmentType;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->assignmentType === 'assigned') {
            return $this->getAssignmentMail($notifiable);
        } else {
            return $this->getRemovalMail($notifiable);
        }
    }

    /**
     * Get assignment mail message
     */
    protected function getAssignmentMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have been assigned as Manager of ' . $this->department->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned as the manager of the following department:')
            ->line('Department: ' . $this->department->name)
            ->line('Code: ' . $this->department->code)
            ->line('Location: ' . ($this->department->location ?? 'Not specified'))
            ->line('Budget: $' . number_format($this->department->budget ?? 0, 2))
            ->line('Headcount Limit: ' . ($this->department->headcount_limit ?? 'Not specified'))
            ->when($this->department->parent_id, function ($message) {
                return $message->line('Parent Department: ' . $this->getParentDepartmentName());
            })
            ->action('View Department', $this->getDepartmentUrl())
            ->line('As a department manager, you will have access to:')
            ->line('• Employee management within your department')
            ->line('• Budget tracking and reporting')
            ->line('• Performance metrics and analytics')
            ->line('• Department hierarchy management')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get removal mail message
     */
    protected function getRemovalMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Manager Assignment Removed: ' . $this->department->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your assignment as manager of the following department has been removed:')
            ->line('Department: ' . $this->department->name)
            ->line('Code: ' . $this->department->code)
            ->line('Effective immediately, you will no longer have manager access to this department.')
            ->line('If you have any questions about this change, please contact HR.')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'manager_assigned',
            'assignment_type' => $this->assignmentType,
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'department_code' => $this->department->code,
            'message' => $this->assignmentType === 'assigned'
                ? 'You have been assigned as manager of "' . $this->department->name . '".'
                : 'Your manager assignment for "' . $this->department->name . '" has been removed.',
            'action_url' => $this->getDepartmentUrl(),
            'priority' => 'high'
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'manager_assigned',
            'assignment_type' => $this->assignmentType,
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'message' => $this->assignmentType === 'assigned'
                ? 'You have been assigned as manager of "' . $this->department->name . '".'
                : 'Your manager assignment for "' . $this->department->name . '" has been removed.',
            'timestamp' => now()->toISOString(),
            'action_url' => $this->getDepartmentUrl()
        ]);
    }

    /**
     * Get parent department name
     */
    protected function getParentDepartmentName(): string
    {
        try {
            if ($this->department->parent_id) {
                $parent = EmployeeDepartment::find($this->department->parent_id);
                return $parent ? $parent->name : 'Unknown';
            }
            return 'None (Root Department)';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get department URL
     */
    protected function getDepartmentUrl(): string
    {
        return '/departments/' . $this->department->id;
    }
}
