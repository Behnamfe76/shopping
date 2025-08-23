<?php

namespace App\Notifications\EmployeeDepartment;

use App\Models\EmployeeDepartment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class BudgetThresholdReached extends Notification implements ShouldQueue
{
    use Queueable;

    public $department;
    public $threshold;
    public $currentUtilization;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeDepartment $department, float $threshold, float $currentUtilization)
    {
        $this->department = $department;
        $this->threshold = $threshold;
        $this->currentUtilization = $currentUtilization;
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
            ->subject('Budget Threshold Alert: ' . $this->department->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A budget threshold has been reached for the following department:')
            ->line('Department: ' . $this->department->name)
            ->line('Code: ' . $this->department->code)
            ->line('Budget: $' . number_format($this->department->budget ?? 0, 2))
            ->line('Current Utilization: ' . number_format($this->currentUtilization, 1) . '%')
            ->line('Threshold: ' . number_format($this->threshold, 1) . '%')
            ->line('This alert indicates that the department is approaching or has exceeded its budget limit.')
            ->action('View Department', $this->getDepartmentUrl())
            ->line('Recommended actions:')
            ->line('• Review current spending patterns')
            ->line('• Identify cost-saving opportunities')
            ->line('• Consider budget reallocation')
            ->line('• Contact finance team for guidance')
            ->salutation('Best regards, Finance Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'budget_threshold_reached',
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'threshold' => $this->threshold,
            'current_utilization' => $this->currentUtilization,
            'message' => 'Budget threshold of ' . $this->threshold . '% reached for "' . $this->department->name . '".',
            'action_url' => $this->getDepartmentUrl(),
            'priority' => 'urgent'
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'budget_threshold_reached',
            'department_id' => $this->department->id,
            'department_name' => $this->department->name,
            'message' => 'Budget threshold of ' . $this->threshold . '% reached for "' . $this->department->name . '".',
            'timestamp' => now()->toISOString(),
            'action_url' => $this->getDepartmentUrl()
        ]);
    }

    /**
     * Get department URL
     */
    protected function getDepartmentUrl(): string
    {
        return '/departments/' . $this->department->id;
    }
}
