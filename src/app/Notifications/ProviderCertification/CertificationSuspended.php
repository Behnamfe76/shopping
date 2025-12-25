<?php

namespace App\Notifications\ProviderCertification;

use App\Models\ProviderCertification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CertificationSuspended extends Notification implements ShouldQueue
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

        $url = URL::route('provider.certifications.show', [
            'provider' => $provider->id,
            'certification' => $certification->id,
        ]);

        $mailMessage = (new MailMessage)
            ->subject('WARNING: Certification Suspended - '.$certification->certification_name)
            ->greeting('Hello '.$provider->name.',')
            ->line('**WARNING:** Your certification has been suspended.')
            ->line('**Certification Details:**')
            ->line('• **Name:** '.$certification->certification_name)
            ->line('• **Number:** '.$certification->certification_number)
            ->line('• **Issuing Organization:** '.$certification->issuing_organization)
            ->line('• **Category:** '.ucfirst(str_replace('_', ' ', $certification->category)))
            ->line('• **Status:** '.ucfirst($certification->status))
            ->line('• **Verification Status:** '.ucfirst(str_replace('_', ' ', $certification->verification_status)));

        if ($this->reason) {
            $mailMessage->line('**Suspension Reason:**')
                ->line($this->reason);
        }

        $mailMessage->line('**Immediate Action Required:**')
            ->line('1. **STOP** providing services that require this certification')
            ->line('2. Review the suspension reason carefully')
            ->line('3. Address any issues or concerns identified')
            ->line('4. Contact our compliance team for guidance')
            ->line('5. Submit required documentation for reinstatement')
            ->action('View Certification', $url)
            ->line('**What This Means:**')
            ->line('• Your certification is temporarily inactive')
            ->line('• You cannot provide services requiring this certification')
            ->line('• This may affect your current contracts and clients')
            ->line('• Your professional reputation may be impacted')
            ->line('**Next Steps:**')
            ->line('• Contact our support team immediately')
            ->line('• Understand the reinstatement process')
            ->line('• Work to resolve the suspension issues')
            ->line('• Keep documentation of all communications')
            ->line('We\'re here to help you resolve this situation and restore your certification.')
            ->salutation('Best regards,<br>'.config('app.name').' Compliance Team');

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
            'type' => 'certification_suspended',
            'certification_id' => $this->certification->id,
            'certification_name' => $this->certification->certification_name,
            'certification_number' => $this->certification->certification_number,
            'issuing_organization' => $this->certification->issuing_organization,
            'category' => $this->certification->category,
            'status' => $this->certification->status,
            'verification_status' => $this->certification->verification_status,
            'suspension_reason' => $this->reason,
            'message' => 'Your certification "'.$this->certification->certification_name.'" has been suspended.',
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
        return 'certification_suspended';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Only send if the certification is suspended and not deleted
        return $this->certification->status === 'suspended' &&
               $this->certification->status !== 'deleted';
    }

    /**
     * Get the tags for the notification.
     */
    public function tags(): array
    {
        return [
            'provider_certification',
            'certification_suspended',
            'provider_'.$this->certification->provider_id,
            'certification_'.$this->certification->id,
            'urgency_high',
        ];
    }
}
