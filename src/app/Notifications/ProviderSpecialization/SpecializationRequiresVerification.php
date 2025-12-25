<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderSpecialization;

use Fereydooni\Shopping\App\Models\ProviderSpecialization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SpecializationRequiresVerification extends Notification implements ShouldQueue
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
        $provider = $this->specialization->provider;
        $specializationName = $this->specialization->specialization_name;

        return (new MailMessage)
            ->subject('Provider Specialization Requires Verification')
            ->greeting('Hello '.$notifiable->name)
            ->line('A new provider specialization requires your verification.')
            ->line('Provider: '.($provider->name ?? 'Unknown'))
            ->line('Specialization: '.$specializationName)
            ->line('Category: '.$this->specialization->category)
            ->line('Proficiency Level: '.$this->specialization->proficiency_level)
            ->line('Years of Experience: '.$this->specialization->years_experience)
            ->action('Review Specialization', url('/admin/provider-specializations/'.$this->specialization->id.'/review'))
            ->line('Please review this specialization and take appropriate action.')
            ->salutation('Best regards, '.config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'specialization_requires_verification',
            'specialization_id' => $this->specialization->id,
            'provider_id' => $this->specialization->provider_id,
            'provider_name' => $this->specialization->provider->name ?? 'Unknown',
            'specialization_name' => $this->specialization->specialization_name,
            'category' => $this->specialization->category,
            'proficiency_level' => $this->specialization->proficiency_level,
            'years_experience' => $this->specialization->years_experience,
            'message' => 'Provider specialization requires verification',
            'action_url' => '/admin/provider-specializations/'.$this->specialization->id.'/review',
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'specialization_requires_verification';
    }
}
