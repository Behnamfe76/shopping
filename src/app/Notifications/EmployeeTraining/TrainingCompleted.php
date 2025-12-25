<?php

namespace Fereydooni\Shopping\Notifications\EmployeeTraining;

use Fereydooni\Shopping\Models\EmployeeTraining;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public EmployeeTraining $training;

    public ?float $score;

    public ?string $grade;

    /**
     * Create a new notification instance.
     */
    public function __construct(EmployeeTraining $training, ?float $score = null, ?string $grade = null)
    {
        $this->training = $training;
        $this->score = $score;
        $this->grade = $grade;
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
        $employeeName = $this->training->employee->name ?? 'Employee';
        $completionDate = $this->training->completion_date ? $this->training->completion_date->format('M d, Y H:i') : now()->format('M d, Y H:i');

        $message = (new MailMessage)
            ->subject('Training Completed: '.$this->training->training_name)
            ->greeting('Hello '.$notifiable->name.',')
            ->line($employeeName.' has successfully completed their training.')
            ->line('Training: '.$this->training->training_name)
            ->line('Employee: '.$employeeName)
            ->line('Completion Date: '.$completionDate)
            ->line('Total Hours: '.$this->training->total_hours.' hours');

        if ($this->score !== null) {
            $message->line('Score: '.$this->score.'%');
        }

        if ($this->grade !== null) {
            $message->line('Grade: '.$this->grade);
        }

        if ($this->training->is_certification) {
            $message->line('Certificate Number: '.($this->training->certificate_number ?? 'Pending'));
        }

        return $message
            ->action('View Training Details', url('/training/'.$this->training->id))
            ->line('Congratulations on the successful completion!')
            ->line('Thank you for your attention to this matter.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $employeeName = $this->training->employee->name ?? 'Employee';
        $completionDate = $this->training->completion_date ? $this->training->completion_date->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s');

        return [
            'training_id' => $this->training->id,
            'training_name' => $this->training->training_name,
            'employee_name' => $employeeName,
            'employee_id' => $this->training->employee_id,
            'completion_date' => $completionDate,
            'total_hours' => $this->training->total_hours,
            'score' => $this->score,
            'grade' => $this->grade,
            'is_certification' => $this->training->is_certification,
            'certificate_number' => $this->training->certificate_number,
            'message' => $employeeName.' has completed training: '.$this->training->training_name,
            'action_url' => '/training/'.$this->training->id,
            'type' => 'training_completed',
        ];
    }
}
