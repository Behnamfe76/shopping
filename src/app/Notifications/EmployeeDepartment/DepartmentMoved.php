<?php

namespace App\Notifications\EmployeeDepartment;

use App\Models\EmployeeDepartment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepartmentMoved extends Notification implements ShouldQueue
{
    use Queueable;

    public $department;

    public $previousParentId;

    public $newParentId;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeDepartment $department, int $previousParentId, int $newParentId)
    {
        $this->department = $department;
        $this->previousParentId = $previousParentId;
        $this->newParentId = $newParentId;
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
        return (new MailMessage)
            ->subject('Department Moved: '.$this->department->name)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A department has been moved in the organizational hierarchy.')
            ->line('Department: '.$this->department->name)
            ->line('Code: '.$this->department->code)
            ->line('Previous Parent: '.$this->getParentDepartmentName($this->previousParentId))
            ->line('New Parent: '.$this->getParentDepartmentName($this->newParentId))
            ->line('Location: '.($this->department->location ?? 'Not specified'))
            ->action('View Department', $this->getDepartmentUrl())
            ->line('This change may affect:')
            ->line('• Reporting relationships')
            ->line('• Budget allocations')
            ->line('• Access permissions')
            ->line('• Organizational structure')
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'department_moved',
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'previous_parent_id' => $this->previousParentId,
            'new_parent_id' => $this->newParentId,
            'message' => 'Department "'.$this->department->name.'" has been moved in the hierarchy.',
            'action_url' => $this->getDepartmentUrl(),
            'priority' => 'normal',
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'department_moved',
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'message' => 'Department "'.$this->department->name.'" has been moved in the hierarchy.',
            'timestamp' => now()->toISOString(),
            'action_url' => $this->getDepartmentUrl(),
        ]);
    }

    /**
     * Get parent department name
     */
    protected function getParentDepartmentName(int $parentId): string
    {
        try {
            if ($parentId) {
                $parent = EmployeeDepartment::find($parentId);

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
        return '/departments/'.$this->department->id;
    }
}
