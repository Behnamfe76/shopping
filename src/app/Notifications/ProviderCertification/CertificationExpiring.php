<?php

namespace App\Notifications\ProviderCertification;

use App\Models\ProviderCertification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CertificationExpiring extends Notification implements ShouldQueue
{
    use Queueable;

    public $certification;
    public $daysUntilExpiry;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderCertification $certification, int $daysUntilExpiry)
    {
        $this->certification = $certification;
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
        $provider = $notifiable;
        $certification = $this->certification;

        $url = URL::route('provider.certifications.show', [
            'provider' => $provider->id,
            'certification' => $certification->id
        ]);

        $urgencyLevel = $this->getUrgencyLevel();
        $subject = $urgencyLevel . ' - Certification Expiring in ' . $this->daysUntilExpiry . ' days';

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $provider->name . ',')
            ->line('This is a reminder that your certification will expire soon.')
            ->line('**Certification Details:**')
            ->line('• **Name:** ' . $certification->certification_name)
            ->line('• **Number:** ' . $certification->certification_number)
            ->line('• **Issuing Organization:** ' . $certification->issuing_organization)
            ->line('• **Category:** ' . ucfirst(str_replace('_', ' ', $certification->category)))
            ->line('• **Expiry Date:** ' . $certification->expiry_date->format('M d, Y'))
            ->line('• **Days Until Expiry:** ' . $this->daysUntilExpiry . ' days')
            ->line('• **Status:** ' . ucfirst($certification->status))
            ->line('• **Verification Status:** ' . ucfirst(str_replace('_', ' ', $certification->verification_status)));

        if ($certification->is_recurring) {
            $mailMessage->line('**Renewal Information:**')
                       ->line('• **Renewal Period:** ' . $certification->renewal_period . ' months')
                       ->line('• **Renewal Requirements:** ' . ($certification->renewal_requirements ?: 'Standard renewal process'));
        }

        $mailMessage->line('**Action Required:**')
                   ->line('Please review your certification and take necessary action to renew it before expiration.')
                   ->action('View Certification', $url)
                   ->line('**Important:** Expired certifications may affect your ability to provide services and your professional credibility.')
                   ->line('If you have any questions about the renewal process, please contact our support team.')
                   ->salutation('Best regards,<br>' . config('app.name') . ' Team');

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
            'type' => 'certification_expiring',
            'certification_id' => $this->certification->id,
            'certification_name' => $this->certification->certification_name,
            'certification_number' => $this->certification->certification_number,
            'issuing_organization' => $this->certification->issuing_organization,
            'category' => $this->certification->category,
            'status' => $this->certification->status,
            'verification_status' => $this->certification->verification_status,
            'days_until_expiry' => $this->daysUntilExpiry,
            'expiry_date' => $this->certification->expiry_date->toISOString(),
            'urgency_level' => $this->getUrgencyLevel(),
            'message' => 'Your certification "' . $this->certification->certification_name . '" will expire in ' . $this->daysUntilExpiry . ' days.',
            'action_url' => URL::route('provider.certifications.show', [
                'provider' => $notifiable->id,
                'certification' => $this->certification->id
            ]),
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get the urgency level based on days until expiry.
     */
    private function getUrgencyLevel(): string
    {
        if ($this->daysUntilExpiry <= 7) {
            return 'URGENT';
        } elseif ($this->daysUntilExpiry <= 14) {
            return 'HIGH PRIORITY';
        } elseif ($this->daysUntilExpiry <= 30) {
            return 'PRIORITY';
        } else {
            return 'REMINDER';
        }
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'certification_expiring';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Only send if the certification is active and not deleted
        return $this->certification->status === 'active' &&
               $this->certification->status !== 'deleted' &&
               $this->daysUntilExpiry > 0;
    }

    /**
     * Get the tags for the notification.
     */
    public function tags(): array
    {
        return [
            'provider_certification',
            'certification_expiring',
            'provider_' . $this->certification->provider_id,
            'certification_' . $this->certification->id,
            'urgency_' . strtolower($this->getUrgencyLevel())
        ];
    }
}
