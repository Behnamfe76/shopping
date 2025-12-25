<?php

namespace App\Notifications\ProviderCertification;

use App\Models\ProviderCertification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CertificationAdded extends Notification implements ShouldQueue
{
    use Queueable;

    public $certification;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderCertification $certification)
    {
        $this->certification = $certification;
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
        $provider = $notifiable;
        $certification = $this->certification;

        $url = URL::route('provider.certifications.show', [
            'provider' => $provider->id,
            'certification' => $certification->id,
        ]);

        return (new MailMessage)
            ->subject('New Certification Added - '.$certification->certification_name)
            ->greeting('Hello '.$provider->name.',')
            ->line('A new certification has been added to your profile.')
            ->line('**Certification Details:**')
            ->line('• **Name:** '.$certification->certification_name)
            ->line('• **Number:** '.$certification->certification_number)
            ->line('• **Issuing Organization:** '.$certification->issuing_organization)
            ->line('• **Category:** '.ucfirst(str_replace('_', ' ', $certification->category)))
            ->line('• **Issue Date:** '.$certification->issue_date->format('M d, Y'))
            ->line('• **Expiry Date:** '.$certification->expiry_date->format('M d, Y'))
            ->line('• **Status:** '.ucfirst($certification->status))
            ->line('• **Verification Status:** '.ucfirst(str_replace('_', ' ', $certification->verification_status)))
            ->action('View Certification', $url)
            ->line('Please review the certification details and ensure all information is correct.')
            ->line('If you notice any discrepancies, please contact our support team.')
            ->line('Thank you for maintaining your professional credentials with us!')
            ->salutation('Best regards,<br>'.config('app.name').' Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'certification_added',
            'certification_id' => $this->certification->id,
            'certification_name' => $this->certification->certification_name,
            'certification_number' => $this->certification->certification_number,
            'issuing_organization' => $this->certification->issuing_organization,
            'category' => $this->certification->category,
            'status' => $this->certification->status,
            'verification_status' => $this->certification->verification_status,
            'issue_date' => $this->certification->issue_date->toISOString(),
            'expiry_date' => $this->certification->expiry_date->toISOString(),
            'message' => 'New certification "'.$this->certification->certification_name.'" has been added to your profile.',
            'action_url' => URL::route('provider.certifications.show', [
                'provider' => $notifiable->id,
                'certification' => $this->certification->id,
            ]),
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'certification_added';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Only send if the certification is still active
        return $this->certification->status !== 'deleted';
    }

    /**
     * Get the tags for the notification.
     */
    public function tags(): array
    {
        return [
            'provider_certification',
            'certification_added',
            'provider_'.$this->certification->provider_id,
            'certification_'.$this->certification->id,
        ];
    }
}
