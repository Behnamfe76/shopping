<?php

namespace Fereydooni\Shopping\Notifications\EmployeeTraining;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Fereydooni\Shopping\Models\EmployeeTraining;

class TrainingStarted extends Notification implements ShouldQueue
{
    use Queueable;

    public EmployeeTraining $training;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeTraining $training)
    {
        $this->training = $training;
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

        return (new MailMessage)
            ->subject('Training Started: ' . $this->training->training_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($employeeName . ' has started their assigned training.')
            ->line('Training: ' . $this->training->training_name)
            ->line('Employee: ' . $employeeName)
            ->line('Start Date: ' . now()->format('M d, Y H:i'))
            ->line('Expected Completion: ' . $this->training->end_date->format('M d, Y'))
            ->line('Total Hours: ' . $this->training->total_hours . ' hours')
            ->when($this->training->is_mandatory, function ($message) {
                return $message->line('This is a mandatory training that must be completed on time.');
            })
            ->action('View Training Progress', url('/training/' . $this->training->id . '/progress'))
            ->line('You will be notified when the training is completed.')
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
            'start_date' => now()->format('Y-m-d H:i:s'),
            'expected_completion' => $this->training->end_date->format('Y-m-d'),
            'total_hours' => $this->training->total_hours,
            'is_mandatory' => $this->training->is_mandatory,
            'message' => $employeeName . ' has started training: ' . $this->training->training_name,
            'action_url' => '/training/' . $this->training->id . '/progress',
            'type' => 'training_started'
        ];
    }
}
