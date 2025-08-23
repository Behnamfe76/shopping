<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderInvoice;

use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceCancelled extends Notification implements ShouldQueue
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
            ->subject('Provider Invoice Cancelled - ' . $this->invoice->invoice_number)
            ->greeting('Hello ' . ($notifiable->name ?? 'Team Member'))
            ->line('A provider invoice has been cancelled.')
            ->line('Invoice Number: ' . $this->invoice->invoice_number)
            ->line('Provider: ' . ($provider->name ?? 'Unknown Provider'))
            ->line('Amount: $' . number_format($this->invoice->total_amount, 2))
            ->line('Cancellation Date: ' . now()->format('M d, Y H:i'))
            ->action('View Invoice', url('/provider-invoices/' . $this->invoice->id))
            ->line('The invoice has been cancelled and is no longer active.');
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
            'cancellation_date' => now()->toISOString(),
            'status' => $this->invoice->status,
            'type' => 'invoice_cancelled',
            'message' => 'Provider invoice cancelled: ' . $this->invoice->invoice_number
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
