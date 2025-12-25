<?php

namespace App\Notifications\ProviderCertification;

use App\Models\ProviderCertification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CertificationExpired extends Notification implements ShouldQueue
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

        $mailMessage = (new MailMessage)
            ->subject('URGENT: Certification Expired - '.$certification->certification_name)
            ->greeting('Hello '.$provider->name.',')
            ->line('**URGENT NOTICE:** Your certification has expired.')
            ->line('**Certification Details:**')
            ->line('• **Name:** '.$certification->certification_name)
            ->line('• **Number:** '.$certification->certification_number)
            ->line('• **Issuing Organization:** '.$certification->issuing_organization)
            ->line('• **Category:** '.ucfirst(str_replace('_', ' ', $certification->category)))
            ->line('• **Expiry Date:** '.$certification->expiry_date->format('M d, Y'))
            ->line('• **Status:** '.ucfirst($certification->status))
            ->line('• **Verification Status:** '.ucfirst(str_replace('_', ' ', $certification->verification_status)));

        if ($certification->is_recurring) {
            $mailMessage->line('**Renewal Information:**')
                ->line('• **Renewal Period:** '.$certification->renewal_period.' months')
                ->line('• **Renewal Requirements:** '.($certification->renewal_requirements ?: 'Standard renewal process'));
        }

        $mailMessage->line('**Immediate Action Required:**')
            ->line('1. **STOP** providing services that require this certification')
            ->line('2. Contact the issuing organization for renewal requirements')
            ->line('3. Complete any required continuing education or training')
            ->line('4. Submit renewal application and documentation')
            ->line('5. Update your profile once renewed')
            ->action('View Certification', $url)
            ->line('**Important:** Expired certifications may:')
            ->line('• Affect your professional liability insurance')
            ->line('• Impact your ability to win contracts')
            ->line('• Damage your professional reputation')
            ->line('• Result in legal or regulatory issues')
            ->line('Please prioritize renewing this certification immediately.')
            ->line('If you need assistance with the renewal process, contact our support team.')
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
            'type' => 'certification_expired',
            'certification_id' => $this->certification->id,
            'certification_name' => $this->certification->certification_name,
            'certification_number' => $this->certification->certification_number,
            'issuing_organization' => $this->certification->issuing_organization,
            'category' => $this->certification->category,
            'status' => $this->certification->status,
            'verification_status' => $this->certification->verification_status,
            'expiry_date' => $this->certification->expiry_date->toISOString(),
            'days_since_expiry' => now()->diffInDays($this->certification->expiry_date),
            'message' => 'Your certification "'.$this->certification->certification_name.'" has expired.',
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
        return 'certification_expired';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Only send if the certification is expired and not deleted
        return $this->certification->status === 'expired' &&
               $this->certification->status !== 'deleted';
    }

    /**
     * Get the tags for the notification.
     */
    public function tags(): array
    {
        return [
            'provider_certification',
            'certification_expired',
            'provider_'.$this->certification->provider_id,
            'certification_'.$this->certification->id,
            'urgency_urgent',
        ];
    }
}
