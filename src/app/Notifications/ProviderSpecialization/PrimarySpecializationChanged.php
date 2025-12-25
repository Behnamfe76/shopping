<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderSpecialization;

use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrimarySpecializationChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $specialization;

    public $previousPrimary;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderSpecialization $specialization, ?ProviderSpecialization $previousPrimary = null)
    {
        $this->specialization = $specialization;
        $this->previousPrimary = $previousPrimary;
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
        $newPrimaryName = $this->specialization->specialization_name;
        $previousPrimaryName = $this->previousPrimary?->specialization_name ?? 'None';

        return (new MailMessage)
            ->subject("Primary Specialization Changed to: {$newPrimaryName}")
            ->greeting("Hello {$notifiable->name}!")
            ->line('Your primary specialization has been updated.')
            ->line("New Primary: {$newPrimaryName}")
            ->line("Previous Primary: {$previousPrimaryName}")
            ->line('This specialization will now be highlighted as your main area of expertise.')
            ->action('View Specialization', url("/provider/specializations/{$this->specialization->id}"))
            ->line('Thank you for keeping your profile updated!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'primary_specialization_changed',
            'specialization_id' => $this->specialization->id,
            'specialization_name' => $this->specialization->specialization_name,
            'previous_primary_id' => $this->previousPrimary?->id,
            'previous_primary_name' => $this->previousPrimary?->specialization_name,
            'changed_at' => $this->specialization->updated_at,
            'message' => "Primary specialization changed to '{$this->specialization->specialization_name}'.",
        ];
    }
}
