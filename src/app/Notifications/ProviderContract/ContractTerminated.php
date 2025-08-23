<?php

namespace Fereydooni\Shopping\app\Notifications\ProviderContract;

use Fereydooni\Shopping\app\Models\ProviderContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractTerminated extends Notification implements ShouldQueue
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
            ->subject('Provider Contract Terminated')
            ->greeting('Hello ' . $notifiable->name)
            ->line('A provider contract has been terminated.')
            ->line('Contract Details:')
            ->line('- Contract Number: ' . $this->contract->contract_number)
            ->line('- Provider: ' . $provider->name)
            ->line('- Contract Type: ' . ucfirst($this->contract->contract_type))
            ->line('- Termination Date: ' . $this->contract->termination_date->format('M d, Y'))
            ->line('- Termination Reason: ' . ($this->contract->termination_reason ?? 'Not specified'))
            ->line('- Status: Terminated')
            ->action('View Contract', url('/provider-contracts/' . $this->contract->id))
            ->line('Please review the termination details and take necessary actions.');
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
            'status' => 'terminated',
            'termination_date' => $this->contract->termination_date,
            'termination_reason' => $this->contract->termination_reason,
            'message' => 'Provider contract terminated: ' . $this->contract->contract_number,
            'type' => 'contract_terminated'
        ];
    }
}
