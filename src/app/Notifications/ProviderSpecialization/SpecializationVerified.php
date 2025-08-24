<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderSpecialization;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;

class SpecializationVerified extends Notification implements ShouldQueue
{
    use Queueable;

    public $specialization;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderSpecialization $specialization)
    {
        $this->specialization = $specialization;
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
        $verifiedAt = $this->specialization->verified_at->format('M d, Y');

        return (new MailMessage)
            ->subject("Specialization Verified: {$specializationName}")
            ->greeting("Congratulations {$notifiable->name}!")
            ->line("Your specialization '{$specializationName}' has been verified successfully.")
            ->line("Verification Date: {$verifiedAt}")
            ->line("This specialization is now visible to potential clients and customers.")
            ->action('View Specialization', url("/provider/specializations/{$this->specialization->id}"))
            ->line('Thank you for maintaining high standards!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'specialization_verified',
            'specialization_id' => $this->specialization->id,
            'specialization_name' => $this->specialization->specialization_name,
            'verified_at' => $this->specialization->verified_at,
            'verified_by' => $this->specialization->verified_by,
            'message' => "Specialization '{$this->specialization->specialization_name}' has been verified successfully.",
        ];
    }
}
