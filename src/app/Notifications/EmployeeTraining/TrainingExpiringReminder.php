<?php

namespace Fereydooni\Shopping\Notifications\EmployeeTraining;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Fereydooni\Shopping\Models\EmployeeTraining;

class TrainingExpiringReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public EmployeeTraining $training;
    public int $daysUntilExpiry;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeTraining $training, int $daysUntilExpiry)
    {
        $this->training = $training;
        $this->daysUntilExpiry = $daysUntilExpiry;
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
        $urgency = $this->daysUntilExpiry <= 7 ? 'URGENT' : 'Reminder';
        $subject = $urgency . ': Training Expiring Soon - ' . $this->training->training_name;

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your training certification is expiring soon.')
            ->line('Training: ' . $this->training->training_name)
            ->line('Expiry Date: ' . $this->training->expiry_date->format('M d, Y'))
            ->line('Days Remaining: ' . $this->daysUntilExpiry . ' days');

        if ($this->training->is_mandatory) {
            $message->line('This is a mandatory training. Failure to renew may affect your employment status.');
        }

        if ($this->training->is_renewable) {
            $message->line('This training can be renewed. Please contact your manager or HR department.');
        }

        return $message
            ->action('View Training Details', url('/training/' . $this->training->id))
            ->line('Please take action to renew your certification before it expires.')
            ->line('Thank you for your attention to this matter.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $urgency = $this->daysUntilExpiry <= 7 ? 'urgent' : 'normal';

        return [
            'training_id' => $this->training->id,
            'training_name' => $this->training->training_name,
            'expiry_date' => $this->training->expiry_date->format('Y-m-d'),
            'days_until_expiry' => $this->daysUntilExpiry,
            'is_mandatory' => $this->training->is_mandatory,
            'is_renewable' => $this->training->is_renewable,
            'urgency' => $urgency,
            'message' => 'Training expiring in ' . $this->daysUntilExpiry . ' days: ' . $this->training->training_name,
            'action_url' => '/training/' . $this->training->id,
            'type' => 'training_expiring_reminder'
        ];
    }
}
