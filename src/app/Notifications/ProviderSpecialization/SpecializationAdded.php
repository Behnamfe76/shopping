<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderSpecialization;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;

class SpecializationAdded extends Notification implements ShouldQueue
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
        $category = $this->specialization->category->value;
        $proficiencyLevel = $this->specialization->proficiency_level->value;

        return (new MailMessage)
            ->subject("New Specialization Added: {$specializationName}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your new specialization '{$specializationName}' has been successfully added.")
            ->line("Category: {$category}")
            ->line("Proficiency Level: {$proficiencyLevel}")
            ->line("Status: {$this->specialization->verification_status->value}")
            ->action('View Specialization', url("/provider/specializations/{$this->specialization->id}"))
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'specialization_added',
            'specialization_id' => $this->specialization->id,
            'specialization_name' => $this->specialization->specialization_name,
            'category' => $this->specialization->category->value,
            'proficiency_level' => $this->specialization->proficiency_level->value,
            'verification_status' => $this->specialization->verification_status->value,
            'is_primary' => $this->specialization->is_primary,
            'is_active' => $this->specialization->is_active,
            'created_at' => $this->specialization->created_at,
            'message' => "New specialization '{$this->specialization->specialization_name}' has been added successfully.",
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType($notifiable): string
    {
        return 'specialization_added';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend($notifiable): bool
    {
        // Only send if the specialization is active
        return $this->specialization->is_active;
    }

    /**
     * Get the notification's unique identifier.
     */
    public function id(): string
    {
        return "specialization_added_{$this->specialization->id}";
    }
}
