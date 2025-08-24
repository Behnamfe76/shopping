<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderSpecialization;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Fereydooni\Shopping\App\Models\ProviderSpecialization;

class SpecializationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $specialization;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderSpecialization $specialization, ?string $reason = null)
    {
        $this->specialization = $specialization;
        $this->reason = $reason;
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
        $specializationName = $this->specialization->specialization_name;
        $message = (new MailMessage)
            ->subject("Specialization Rejected: {$specializationName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your specialization '{$specializationName}' has been rejected during the verification process.");

        if ($this->reason) {
            $message->line("Reason: {$this->reason}");
        }

        $message->line("Please review the feedback and make necessary adjustments before resubmitting.")
            ->action('Update Specialization', url("/provider/specializations/{$this->specialization->id}/edit"))
            ->line('If you have any questions, please contact our support team.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'specialization_rejected',
            'specialization_id' => $this->specialization->id,
            'specialization_name' => $this->specialization->specialization_name,
            'reason' => $this->reason,
            'rejected_at' => $this->specialization->updated_at,
            'message' => "Specialization '{$this->specialization->specialization_name}' has been rejected.",
        ];
    }
}
