<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderRating;

use App\Models\ProviderRating;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class RatingReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public ProviderRating $rating;
    public string $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderRating $rating, string $type = 'created')
    {
        $this->rating = $rating;
        $this->type = $type;
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
        $provider = $this->rating->provider;
        $user = $this->rating->user;

        $subject = $this->type === 'updated'
            ? 'Your provider rating has been updated'
            : 'You have received a new provider rating';

        $greeting = $this->type === 'updated'
            ? "Hello {$notifiable->name},"
            : "Hello {$notifiable->name},";

        $message = $this->type === 'updated'
            ? "A user has updated their rating for your service."
            : "Congratulations! You have received a new rating from a user.";

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message)
            ->line("Rating: {$this->rating->rating_value}/{$this->rating->max_rating} stars")
            ->line("Category: {$this->rating->category}")
            ->when($this->rating->title, function ($mail) {
                return $mail->line("Title: {$this->rating->title}");
            })
            ->when($this->rating->comment, function ($mail) {
                return $mail->line("Comment: {$this->rating->comment}");
            })
            ->when($this->rating->pros, function ($mail) {
                return $mail->line("Pros: {$this->rating->pros}");
            })
            ->when($this->rating->cons, function ($mail) {
                return $mail->line("Cons: {$this->rating->cons}");
            })
            ->line("Would recommend: " . ($this->rating->would_recommend ? 'Yes' : 'No'))
            ->action('View Rating Details', url("/provider/ratings/{$this->rating->id}"))
            ->line('Thank you for providing excellent service!')
            ->salutation('Best regards, The Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rating_received',
            'rating_id' => $this->rating->id,
            'provider_id' => $this->rating->provider_id,
            'user_id' => $this->rating->user_id,
            'rating_value' => $this->rating->rating_value,
            'max_rating' => $this->rating->max_rating,
            'category' => $this->rating->category,
            'title' => $this->rating->title,
            'comment' => $this->rating->comment,
            'pros' => $this->rating->pros,
            'cons' => $this->rating->cons,
            'would_recommend' => $this->rating->would_recommend,
            'notification_type' => $this->type,
            'created_at' => $this->rating->created_at,
            'updated_at' => $this->rating->updated_at,
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'rating_received';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Don't send if rating is flagged or rejected
        if (in_array($this->rating->status, ['flagged', 'rejected'])) {
            return false;
        }

        // Don't send if provider has disabled rating notifications
        if (method_exists($notifiable, 'hasRatingNotificationsDisabled') &&
            $notifiable->hasRatingNotificationsDisabled()) {
            return false;
        }

        return true;
    }

    /**
     * Get the notification's unique identifier.
     */
    public function getNotificationId(): string
    {
        return "rating_{$this->rating->id}_{$this->type}";
    }
}
