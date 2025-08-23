<?php

namespace Fereydooni\Shopping\Notifications\EmployeeTraining;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Fereydooni\Shopping\Models\EmployeeTraining;

class TrainingFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public EmployeeTraining $training;
    public ?string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeTraining $training, ?string $reason = null)
    {
        $this->training = $training;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $employeeName = $this->training->employee->name ?? 'Employee';

        $message = (new MailMessage)
            ->subject('Training Failed: ' . $this->training->training_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($employeeName . ' has failed to complete their training.')
            ->line('Training: ' . $this->training->training_name)
            ->line('Employee: ' . $employeeName)
            ->line('Failure Date: ' . now()->format('M d, Y H:i'))
            ->line('Total Hours: ' . $this->training->total_hours . ' hours');

        if ($this->reason) {
            $message->line('Failure Reason: ' . $this->reason);
        }

        if ($this->training->is_mandatory) {
            $message->line('This is a mandatory training. Immediate action is required.');
        }

        return $message
            ->action('View Training Details', url('/training/' . $this->training->id))
            ->line('Please review the training failure and take appropriate action.')
            ->line('Thank you for your attention to this matter.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $employeeName = $this->training->employee->name ?? 'Employee';

        return [
            'training_id' => $this->training->id,
            'training_name' => $this->training->training_name,
            'employee_name' => $employeeName,
            'employee_id' => $this->training->employee_id,
            'failure_date' => now()->format('Y-m-d H:i:s'),
            'total_hours' => $this->training->total_hours,
            'failure_reason' => $this->reason,
            'is_mandatory' => $this->training->is_mandatory,
            'message' => $employeeName . ' has failed training: ' . $this->training->training_name,
            'action_url' => '/training/' . $this->training->id,
            'type' => 'training_failed'
        ];
    }
}
