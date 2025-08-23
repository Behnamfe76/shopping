<?php

namespace App\Notifications\EmployeeEmergencyContact;

use App\Models\EmployeeEmergencyContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class EmergencyContactAdded extends Notification implements ShouldQueue
{
    use Queueable;

    public $contact;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeEmergencyContact $contact)
    {
        $this->contact = $contact;
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
        $employee = $notifiable;
        $contact = $this->contact;

        return (new MailMessage)
            ->subject('Emergency Contact Added')
            ->greeting("Hello {$employee->first_name},")
            ->line("A new emergency contact has been added to your profile.")
            ->line("**Contact Details:**")
            ->line("Name: {$contact->contact_name}")
            ->line("Relationship: {$contact->relationship}")
            ->line("Primary Phone: {$contact->phone_primary}")
            ->when($contact->phone_secondary, function ($message) use ($contact) {
                return $message->line("Secondary Phone: {$contact->phone_secondary}");
            })
            ->when($contact->email, function ($message) use ($contact) {
                return $message->line("Email: {$contact->email}");
            })
            ->when($contact->is_primary, function ($message) {
                return $message->line("**This contact has been set as your primary emergency contact.**");
            })
            ->line("If you did not request this change or if any information is incorrect, please contact HR immediately.")
            ->action('View Your Profile', url('/employee/profile'))
            ->line('Thank you for keeping your emergency contact information up to date.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'emergency_contact_added',
            'contact_id' => $this->contact->id,
            'contact_name' => $this->contact->contact_name,
            'relationship' => $this->contact->relationship,
            'is_primary' => $this->contact->is_primary,
            'added_at' => now(),
            'message' => "Emergency contact {$this->contact->contact_name} ({$this->contact->relationship}) has been added to your profile.",
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
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'emails',
            'database' => 'notifications',
        ];
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'emergency_contact',
            'employee_' . $this->contact->employee_id,
            'contact_' . $this->contact->id,
        ];
    }
}
