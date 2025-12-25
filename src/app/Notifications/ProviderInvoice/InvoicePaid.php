<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderInvoice;

use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaid extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invoice;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderInvoice $invoice)
    {
        $this->invoice = $invoice;
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
        $provider = $this->invoice->provider;

        return (new MailMessage)
            ->subject('Provider Invoice Paid - '.$this->invoice->invoice_number)
            ->greeting('Hello '.($notifiable->name ?? 'Team Member'))
            ->line('A provider invoice has been marked as paid.')
            ->line('Invoice Number: '.$this->invoice->invoice_number)
            ->line('Provider: '.($provider->name ?? 'Unknown Provider'))
            ->line('Amount: $'.number_format($this->invoice->total_amount, 2))
            ->line('Payment Date: '.$this->invoice->paid_at->format('M d, Y'))
            ->line('Payment Method: '.($this->invoice->payment_method ?? 'Not specified'))
            ->action('View Invoice', url('/provider-invoices/'.$this->invoice->id))
            ->line('The invoice has been successfully paid and closed.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'provider_id' => $this->invoice->provider_id,
            'provider_name' => $this->invoice->provider->name ?? 'Unknown Provider',
            'amount' => $this->invoice->total_amount,
            'payment_date' => $this->invoice->paid_at->toISOString(),
            'payment_method' => $this->invoice->payment_method,
            'status' => $this->invoice->status,
            'type' => 'invoice_paid',
            'message' => 'Provider invoice paid: '.$this->invoice->invoice_number,
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
