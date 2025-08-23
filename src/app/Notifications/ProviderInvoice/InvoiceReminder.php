<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderInvoice;

use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invoice;
    protected $reminderType;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderInvoice $invoice, string $reminderType = 'general')
    {
        $this->invoice = $invoice;
        $this->reminderType = $reminderType;
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
        $daysUntilDue = now()->diffInDays($this->invoice->due_date, false);
        $isOverdue = $daysUntilDue < 0;

        $subject = $this->getReminderSubject();
        $message = $this->getReminderMessage($daysUntilDue, $isOverdue);

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . ($notifiable->name ?? 'Team Member'))
            ->line($message)
            ->line('Invoice Number: ' . $this->invoice->invoice_number)
            ->line('Provider: ' . ($provider->name ?? 'Unknown Provider'))
            ->line('Amount: $' . number_format($this->invoice->total_amount, 2))
            ->line('Due Date: ' . $this->invoice->due_date->format('M d, Y'))
            ->line('Days ' . ($isOverdue ? 'Overdue: ' . abs($daysUntilDue) : 'Until Due: ' . $daysUntilDue))
            ->action('View Invoice', url('/provider-invoices/' . $this->invoice->id))
            ->line('Please follow up with the provider regarding this invoice.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $daysUntilDue = now()->diffInDays($this->invoice->due_date, false);
        $isOverdue = $daysUntilDue < 0;

        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'provider_id' => $this->invoice->provider_id,
            'provider_name' => $this->invoice->provider->name ?? 'Unknown Provider',
            'amount' => $this->invoice->total_amount,
            'due_date' => $this->invoice->due_date->toISOString(),
            'days_until_due' => $daysUntilDue,
            'is_overdue' => $isOverdue,
            'reminder_type' => $this->reminderType,
            'status' => $this->invoice->status,
            'type' => 'invoice_reminder',
            'message' => $this->getReminderMessage($daysUntilDue, $isOverdue)
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    /**
     * Get the reminder subject based on type.
     */
    protected function getReminderSubject(): string
    {
        $baseSubject = 'Provider Invoice Reminder - ' . $this->invoice->invoice_number;

        switch ($this->reminderType) {
            case 'due_soon':
                return $baseSubject . ' (Due Soon)';
            case 'overdue':
                return $baseSubject . ' (Overdue)';
            case 'final':
                return $baseSubject . ' (Final Notice)';
            default:
                return $baseSubject;
        }
    }

    /**
     * Get the reminder message based on due date.
     */
    protected function getReminderMessage(int $daysUntilDue, bool $isOverdue): string
    {
        if ($isOverdue) {
            return 'This provider invoice is overdue and requires immediate attention.';
        }

        if ($daysUntilDue <= 3) {
            return 'This provider invoice is due soon and may require follow-up.';
        }

        if ($daysUntilDue <= 7) {
            return 'This provider invoice is approaching its due date.';
        }

        return 'This is a friendly reminder about an upcoming provider invoice.';
    }
}
