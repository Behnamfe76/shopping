<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderInvoice;

use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceOverdue extends Notification implements ShouldQueue
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
        $daysOverdue = now()->diffInDays($this->invoice->due_date);

        return (new MailMessage)
            ->subject('Provider Invoice Overdue - ' . $this->invoice->invoice_number)
            ->greeting('Hello ' . ($notifiable->name ?? 'Team Member'))
            ->line('A provider invoice has become overdue.')
            ->line('Invoice Number: ' . $this->invoice->invoice_number)
            ->line('Provider: ' . ($provider->name ?? 'Unknown Provider'))
            ->line('Amount: $' . number_format($this->invoice->total_amount, 2))
            ->line('Due Date: ' . $this->invoice->due_date->format('M d, Y'))
            ->line('Days Overdue: ' . $daysOverdue . ' day(s)')
            ->action('View Invoice', url('/provider-invoices/' . $this->invoice->id))
            ->line('Please follow up with the provider regarding this overdue payment.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $daysOverdue = now()->diffInDays($this->invoice->due_date);

        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'provider_id' => $this->invoice->provider_id,
            'provider_name' => $this->invoice->provider->name ?? 'Unknown Provider',
            'amount' => $this->invoice->total_amount,
            'due_date' => $this->invoice->due_date->toISOString(),
            'days_overdue' => $daysOverdue,
            'status' => $this->invoice->status,
            'type' => 'invoice_overdue',
            'message' => 'Provider invoice overdue: ' . $this->invoice->invoice_number . ' (' . $daysOverdue . ' days)'
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
