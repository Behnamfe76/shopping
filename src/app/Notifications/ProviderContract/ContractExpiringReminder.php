<?php

namespace Fereydooni\Shopping\app\Notifications\ProviderContract;

use Fereydooni\Shopping\app\Models\ProviderContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractExpiringReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public ProviderContract $contract;
    public int $daysUntilExpiry;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderContract $contract, int $daysUntilExpiry)
    {
        $this->contract = $contract;
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
        $provider = $this->contract->provider;

        return (new MailMessage)
            ->subject('Provider Contract Expiring Soon')
            ->greeting('Hello ' . $notifiable->name)
            ->line('A provider contract is expiring soon.')
            ->line('Contract Details:')
            ->line('- Contract Number: ' . $this->contract->contract_number)
            ->line('- Provider: ' . $provider->name)
            ->line('- Contract Type: ' . ucfirst($this->contract->contract_type))
            ->line('- End Date: ' . $this->contract->end_date->format('M d, Y'))
            ->line('- Days Until Expiry: ' . $this->daysUntilExpiry)
            ->line('- Auto Renewal: ' . ($this->contract->auto_renewal ? 'Enabled' : 'Disabled'))
            ->action('View Contract', url('/provider-contracts/' . $this->contract->id))
            ->line('Please review and take necessary actions before expiration.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contract_id' => $this->contract->id,
            'provider_id' => $this->contract->provider_id,
            'contract_number' => $this->contract->contract_number,
            'days_until_expiry' => $this->daysUntilExpiry,
            'end_date' => $this->contract->end_date,
            'auto_renewal' => $this->contract->auto_renewal,
            'message' => 'Provider contract expiring in ' . $this->daysUntilExpiry . ' days: ' . $this->contract->contract_number,
            'type' => 'contract_expiring_reminder'
        ];
    }
}
