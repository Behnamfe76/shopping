<?php

namespace Fereydooni\Shopping\Notifications\EmployeeTraining;

use Fereydooni\Shopping\Models\EmployeeTraining;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingCertificateGenerated extends Notification implements ShouldQueue
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
            ->subject('Training Certificate Generated: '.$this->training->training_name)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Congratulations! Your training certificate has been generated.')
            ->line('Training: '.$this->training->training_name)
            ->line('Certificate Number: '.$this->training->certificate_number)
            ->line('Completion Date: '.$this->training->completion_date->format('M d, Y'))
            ->line('Score: '.($this->training->score ?? 'N/A').'%')
            ->line('Grade: '.($this->training->grade ?? 'N/A'))
            ->when($this->training->expiry_date, function ($message) {
                return $message->line('Expiry Date: '.$this->training->expiry_date->format('M d, Y'));
            })
            ->when($this->training->certificate_url, function ($message) {
                return $message->action('Download Certificate', $this->training->certificate_url);
            })
            ->action('View Training Details', url('/training/'.$this->training->id))
            ->line('Your certificate is now available for download.')
            ->line('Keep this certificate for your records and future reference.')
            ->line('Thank you for completing your training!');
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
            'certificate_number' => $this->training->certificate_number,
            'completion_date' => $this->training->completion_date->format('Y-m-d'),
            'score' => $this->training->score,
            'grade' => $this->training->grade,
            'expiry_date' => $this->training->expiry_date ? $this->training->expiry_date->format('Y-m-d') : null,
            'certificate_url' => $this->training->certificate_url,
            'message' => 'Training certificate generated: '.$this->training->training_name,
            'action_url' => '/training/'.$this->training->id,
            'type' => 'training_certificate_generated',
        ];
    }
}
