<?php

namespace Fereydooni\Shopping\app\Notifications\ProviderContract;

use Fereydooni\Shopping\app\Models\ProviderContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractRenewed extends Notification implements ShouldQueue
{
    use Queueable;

    public ProviderContract $contract;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderContract $contract)
    {
        $this->contract = $contract;
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
            ->subject('Provider Contract Renewed')
            ->greeting('Hello ' . $notifiable->name)
            ->line('A provider contract has been renewed.')
            ->line('Contract Details:')
            ->line('- Contract Number: ' . $this->contract->contract_number)
            ->line('- Provider: ' . $provider->name)
            ->line('- Contract Type: ' . ucfirst($this->contract->contract_type))
            ->line('- Renewal Date: ' . $this->contract->renewal_date->format('M d, Y'))
            ->line('- New End Date: ' . $this->contract->end_date->format('M d, Y'))
            ->line('- Status: Active')
            ->action('View Contract', url('/provider-contracts/' . $this->contract->id))
            ->line('The contract has been successfully renewed.');
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
            'status' => 'active',
            'renewal_date' => $this->contract->renewal_date,
            'message' => 'Provider contract renewed: ' . $this->contract->contract_number,
            'type' => 'contract_renewed'
        ];
    }
}
