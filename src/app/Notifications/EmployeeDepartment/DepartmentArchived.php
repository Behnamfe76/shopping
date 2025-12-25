<?php

namespace App\Notifications\EmployeeDepartment;

use App\Models\EmployeeDepartment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepartmentArchived extends Notification implements ShouldQueue
{
    use Queueable;

    public $department;

    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeDepartment $department, string $reason = '')
    {
        $this->department = $department;
        $this->reason = $reason;
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
            ->subject('Department Archived: '.$this->department->name)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A department has been archived in the organization.')
            ->line('Department: '.$this->department->name)
            ->line('Code: '.$this->department->code)
            ->line('Location: '.($this->department->location ?? 'Not specified'))
            ->when($this->reason, function ($message) {
                return $message->line('Reason: '.$this->reason);
            })
            ->line('This department is now archived and may affect:')
            ->line('• Employee assignments')
            ->line('• Budget allocations')
            ->line('• Reporting relationships')
            ->line('• Access permissions')
            ->action('View Department', $this->getDepartmentUrl())
            ->salutation('Best regards, HR Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'department_archived',
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'reason' => $this->reason,
            'message' => 'Department "'.$this->department->name.'" has been archived.',
            'action_url' => $this->getDepartmentUrl(),
            'priority' => 'high',
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'department_archived',
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'message' => 'Department "'.$this->department->name.'" has been archived.',
            'timestamp' => now()->toISOString(),
            'action_url' => $this->getDepartmentUrl(),
        ]);
    }

    /**
     * Get department URL
     */
    protected function getDepartmentUrl(): string
    {
        return '/departments/'.$this->department->id;
    }
}
