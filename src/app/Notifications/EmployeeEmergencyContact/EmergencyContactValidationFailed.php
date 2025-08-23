<?php

namespace App\Notifications\EmployeeEmergencyContact;

use App\Models\EmployeeEmergencyContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmergencyContactValidationFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public $contact;
    public $errors;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeEmergencyContact $contact, array $errors = [])
    {
        $this->contact = $contact;
        $this->errors = $errors;
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
        $errors = $this->errors;

        $message = (new MailMessage)
            ->subject('Emergency Contact Validation Issue')
            ->greeting("Hello {$employee->first_name},")
            ->line("We've identified some issues with your emergency contact information that need to be addressed.");

        $message->line("**Contact Details:**")
            ->line("Name: {$contact->contact_name}")
            ->line("Relationship: {$contact->relationship}");

        if (!empty($errors)) {
            $message->line("**Validation Issues Found:**");
            foreach ($errors as $field => $fieldErrors) {
                $fieldName = ucwords(str_replace('_', ' ', $field));
                foreach ($fieldErrors as $error) {
                    $message->line("• {$fieldName}: {$error}");
                }
            }
        }

        $message->line("**Please take the following actions:**")
            ->line("1. Review the validation issues listed above")
            ->line("2. Update your emergency contact information with correct details")
            ->line("3. Ensure all required fields are properly filled")
            ->line("4. Verify phone numbers and email addresses are valid")
            ->action('Update Your Profile', url('/employee/profile'))
            ->line("**Important:** Having accurate emergency contact information is crucial for your safety and the company's emergency response procedures.")
            ->line("If you need assistance updating this information, please contact HR.");

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'emergency_contact_validation_failed',
            'contact_id' => $this->contact->id,
            'contact_name' => $this->contact->contact_name,
            'relationship' => $this->contact->relationship,
            'validation_errors' => $this->errors,
            'failed_at' => now(),
            'message' => "Emergency contact {$this->contact->contact_name} ({$this->contact->relationship}) has validation issues that need to be resolved.",
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
            'validation_failed',
            'employee_' . $this->contact->employee_id,
            'contact_' . $this->contact->id,
        ];
    }
}
