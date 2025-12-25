<?php

namespace App\Notifications\ProviderCertification;

use App\Models\ProviderCertification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CertificationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $certification;

    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderCertification $certification, ?string $reason = null)
    {
        $this->certification = $certification;
        $this->reason = $reason;
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

        $url = URL::route('provider.certifications.edit', [
            'provider' => $provider->id,
            'certification' => $certification->id,
        ]);

        $mailMessage = (new MailMessage)
            ->subject('Certification Rejected - '.$certification->certification_name)
            ->greeting('Hello '.$provider->name.',')
            ->line('We regret to inform you that your certification has been rejected during the verification process.')
            ->line('**Certification Details:**')
            ->line('• **Name:** '.$certification->certification_name)
            ->line('• **Number:** '.$certification->certification_number)
            ->line('• **Issuing Organization:** '.$certification->issuing_organization)
            ->line('• **Category:** '.ucfirst(str_replace('_', ' ', $certification->category)))
            ->line('• **Status:** '.ucfirst($certification->status))
            ->line('• **Verification Status:** '.ucfirst(str_replace('_', ' ', $certification->verification_status)));

        if ($this->reason) {
            $mailMessage->line('**Rejection Reason:**')
                ->line($this->reason);
        }

        $mailMessage->line('**Next Steps:**')
            ->line('1. Review the rejection reason carefully')
            ->line('2. Gather any additional documentation if needed')
            ->line('3. Update your certification information')
            ->line('4. Resubmit for verification')
            ->action('Update Certification', $url)
            ->line('If you have any questions about the rejection or need assistance, please contact our support team.')
            ->line('We\'re here to help you get your certification verified successfully.')
            ->salutation('Best regards,<br>'.config('app.name').' Team');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'certification_rejected',
            'certification_id' => $this->certification->id,
            'certification_name' => $this->certification->certification_name,
            'certification_number' => $this->certification->certification_number,
            'issuing_organization' => $this->certification->issuing_organization,
            'category' => $this->certification->category,
            'status' => $this->certification->status,
            'verification_status' => $this->certification->verification_status,
            'rejection_reason' => $this->reason,
            'message' => 'Your certification "'.$this->certification->certification_name.'" has been rejected.',
            'action_url' => URL::route('provider.certifications.edit', [
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
        return 'certification_rejected';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Only send if the certification is rejected and not deleted
        return $this->certification->verification_status === 'rejected' &&
               $this->certification->status !== 'deleted';
    }

    /**
     * Get the tags for the notification.
     */
    public function tags(): array
    {
        return [
            'provider_certification',
            'certification_rejected',
            'provider_'.$this->certification->provider_id,
            'certification_'.$this->certification->id,
        ];
    }
}
