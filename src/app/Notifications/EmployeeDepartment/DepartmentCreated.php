<?php

namespace App\Notifications\EmployeeDepartment;

use App\Models\EmployeeDepartment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class DepartmentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $department;
    public $createdBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeDepartment $department, $createdBy = null)
    {
        $this->department = $department;
        $this->createdBy = $createdBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        return (new MailMessage)
            ->subject('New Department Created: ' . $this->department->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new department has been created in the organization.')
            ->line('Department: ' . $this->department->name)
            ->line('Code: ' . $this->department->code)
            ->line('Location: ' . ($this->department->location ?? 'Not specified'))
            ->line('Budget: $' . number_format($this->department->budget ?? 0, 2))
            ->line('Headcount Limit: ' . ($this->department->headcount_limit ?? 'Not specified'))
            ->when($this->department->parent_id, function ($message) {
                return $message->line('Parent Department: ' . $this->getParentDepartmentName());
            })
            ->when($this->department->manager_id, function ($message) {
                return $message->line('Manager: ' . $this->getManagerName());
            })
            ->line('Created by: ' . $this->getCreatedByName())
            ->action('View Department', $this->getDepartmentUrl())
            ->line('Please review the department details and ensure all necessary resources are allocated.')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'department_created',
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'department_code' => $this->department->code,
            'parent_id' => $this->department->parent_id,
            'manager_id' => $this->department->manager_id,
            'location' => $this->department->location,
            'budget' => $this->department->budget,
            'headcount_limit' => $this->department->headcount_limit,
            'created_by' => $this->createdBy,
            'created_at' => $this->department->created_at,
            'message' => 'New department "' . $this->department->name . '" has been created.',
            'action_url' => $this->getDepartmentUrl(),
            'priority' => 'normal'
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'department_created',
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'message' => 'New department "' . $this->department->name . '" has been created.',
            'timestamp' => now()->toISOString(),
            'action_url' => $this->getDepartmentUrl()
        ]);
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'department_created';
    }

    /**
     * Get the notification's mail type.
     */
    public function getMailType(): string
    {
        return 'department_created';
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
     * Get manager name
     */
    protected function getManagerName(): string
    {
        try {
            if ($this->department->manager_id) {
                // This would typically query the Employee model
                return 'Manager ID: ' . $this->department->manager_id;
            }
            return 'Not assigned';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get created by name
     */
    protected function getCreatedByName(): string
    {
        try {
            if ($this->createdBy) {
                // This would typically query the User model
                return 'User ID: ' . $this->createdBy;
            }
            return 'System';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get department URL
     */
    protected function getDepartmentUrl(): string
    {
        // This would return the URL to view the department
        return '/departments/' . $this->department->id;
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Only send if the notifiable has permission to view departments
        return $notifiable->can('employee-department.view') ||
               $notifiable->can('employee-department.view-all');
    }

    /**
     * Get the tags for the notification.
     */
    public function tags(): array
    {
        return [
            'department',
            'department_created',
            'hr',
            'organization'
        ];
    }
}
