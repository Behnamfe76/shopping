<?php

namespace Fereydooni\Shopping\Notifications\EmployeeTraining;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Fereydooni\Shopping\Models\EmployeeTraining;

class MandatoryTrainingDue extends Notification implements ShouldQueue
{
    use Queueable;

    public EmployeeTraining $training;
    public int $daysOverdue;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeTraining $training, int $daysOverdue = 0)
    {
        $this->training = $training;
        $this->daysOverdue = $daysOverdue;
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
        $urgency = $this->daysOverdue > 0 ? 'OVERDUE' : 'DUE';
        $subject = $urgency . ': Mandatory Training - ' . $this->training->training_name;

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have mandatory training that requires your immediate attention.')
            ->line('Training: ' . $this->training->training_name)
            ->line('Type: ' . $this->training->training_type)
            ->line('Due Date: ' . $this->training->end_date->format('M d, Y'))
            ->line('Total Hours: ' . $this->training->total_hours . ' hours');

        if ($this->daysOverdue > 0) {
            $message->line('This training is ' . $this->daysOverdue . ' days overdue.');
            $message->line('Immediate action is required to avoid disciplinary consequences.');
        } else {
            $message->line('This training is due soon. Please complete it before the deadline.');
        }

        return $message
            ->action('Start Training Now', url('/training/' . $this->training->id . '/start'))
            ->line('Mandatory training is required for continued employment.')
            ->line('Please complete this training as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $urgency = $this->daysOverdue > 0 ? 'overdue' : 'due';

        return [
            'training_id' => $this->training->id,
            'training_name' => $this->training->training_name,
            'training_type' => $this->training->training_type,
            'due_date' => $this->training->end_date->format('Y-m-d'),
            'days_overdue' => $this->daysOverdue,
            'total_hours' => $this->training->total_hours,
            'urgency' => $urgency,
            'message' => 'Mandatory training ' . $urgency . ': ' . $this->training->training_name,
            'action_url' => '/training/' . $this->training->id . '/start',
            'type' => 'mandatory_training_due'
        ];
    }
}
