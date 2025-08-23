<?php

namespace Fereydooni\Shopping\Notifications\EmployeeTraining;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Fereydooni\Shopping\Models\EmployeeTraining;

class TrainingAssigned extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('New Training Assigned: ' . $this->training->training_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned a new training program.')
            ->line('Training: ' . $this->training->training_name)
            ->line('Type: ' . $this->training->training_type)
            ->line('Provider: ' . $this->training->provider)
            ->line('Start Date: ' . $this->training->start_date->format('M d, Y'))
            ->line('End Date: ' . $this->training->end_date->format('M d, Y'))
            ->line('Total Hours: ' . $this->training->total_hours . ' hours')
            ->line('Method: ' . $this->training->training_method)
            ->when($this->training->is_mandatory, function ($message) {
                return $message->line('This is a mandatory training that must be completed.');
            })
            ->when($this->training->is_certification, function ($message) {
                return $message->line('This training provides certification upon completion.');
            })
            ->when($this->training->description, function ($message) {
                return $message->line('Description: ' . $this->training->description);
            })
            ->action('View Training Details', url('/training/' . $this->training->id))
            ->line('Please review the training details and start the training when ready.')
            ->line('Thank you for your commitment to professional development!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'training_id' => $this->training->id,
            'training_name' => $this->training->training_name,
            'training_type' => $this->training->training_type,
            'provider' => $this->training->provider,
            'start_date' => $this->training->start_date->format('Y-m-d'),
            'end_date' => $this->training->end_date->format('Y-m-d'),
            'total_hours' => $this->training->total_hours,
            'training_method' => $this->training->training_method,
            'is_mandatory' => $this->training->is_mandatory,
            'is_certification' => $this->training->is_certification,
            'message' => 'New training assigned: ' . $this->training->training_name,
            'action_url' => '/training/' . $this->training->id,
            'type' => 'training_assigned'
        ];
    }
}
