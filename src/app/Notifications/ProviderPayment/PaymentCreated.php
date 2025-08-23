<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderPayment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Fereydooni\Shopping\App\Models\ProviderPayment;

class PaymentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The provider payment instance.
     */
    public ProviderPayment $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderPayment $payment)
    {
        $this->payment = $payment;
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
        return (new MailMessage)
            ->subject('New Provider Payment Created')
            ->greeting('Hello!')
            ->line('A new provider payment has been created.')
            ->line('Payment Details:')
            ->line('Payment Number: ' . $this->payment->payment_number)
            ->line('Amount: ' . $this->payment->currency . ' ' . number_format($this->payment->amount, 2))
            ->line('Payment Method: ' . $this->payment->payment_method)
            ->line('Status: ' . ucfirst($this->payment->status))
            ->line('Payment Date: ' . $this->payment->payment_date->format('M d, Y'))
            ->action('View Payment', url('/provider-payments/' . $this->payment->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'provider_id' => $this->payment->provider_id,
            'payment_number' => $this->payment->payment_number,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'payment_method' => $this->payment->payment_method,
            'status' => $this->payment->status,
            'payment_date' => $this->payment->payment_date,
            'type' => 'payment_created',
            'message' => 'New provider payment created: ' . $this->payment->payment_number
        ];
    }
}
