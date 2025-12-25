<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderSpecialization;

use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SpecializationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $specialization;

    public $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderSpecialization $specialization, string $status)
    {
        $this->specialization = $specialization;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $specializationName = $this->specialization->specialization_name;
        $status = ucfirst($this->status);

        return (new MailMessage)
            ->subject("Specialization {$status}: {$specializationName}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your specialization '{$specializationName}' has been {$this->status}.")
            ->line("Status: {$status}")
            ->line('This change affects the visibility and availability of your specialization.')
            ->action('View Specialization', url("/provider/specializations/{$this->specialization->id}"))
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'specialization_status_changed',
            'specialization_id' => $this->specialization->id,
            'specialization_name' => $this->specialization->specialization_name,
            'status' => $this->status,
            'changed_at' => $this->specialization->updated_at,
            'message' => "Specialization '{$this->specialization->specialization_name}' has been {$this->status}.",
        ];
    }
}
