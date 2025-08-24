<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderRating;

use App\Models\ProviderRating;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RatingApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public ProviderRating $rating;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderRating $rating)
    {
        $this->rating = $rating;
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

        return (new MailMessage)
            ->subject('Your provider rating has been approved!')
            ->greeting("Hello {$notifiable->name},")
            ->line('Great news! Your rating for ' . $provider->name . ' has been approved and is now visible to other users.')
            ->line("Rating: {$this->rating->rating_value}/{$this->rating->max_rating} stars")
            ->line("Category: {$this->rating->category}")
            ->when($this->rating->title, function ($mail) {
                return $mail->line("Title: {$this->rating->title}");
            })
            ->when($this->rating->comment, function ($mail) {
                return $mail->line("Comment: {$this->rating->comment}");
            })
            ->action('View Your Rating', url("/ratings/{$this->rating->id}"))
            ->line('Thank you for taking the time to share your experience!')
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
            'type' => 'rating_approved',
            'rating_id' => $this->rating->id,
            'provider_id' => $this->rating->provider_id,
            'provider_name' => $this->rating->provider->name ?? 'Unknown Provider',
            'rating_value' => $this->rating->rating_value,
            'max_rating' => $this->rating->max_rating,
            'category' => $this->rating->category,
            'title' => $this->rating->title,
            'comment' => $this->rating->comment,
            'approved_at' => now(),
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'rating_approved';
    }

    /**
     * Determine if the notification should be sent.
     */
    public function shouldSend(object $notifiable): bool
    {
        // Don't send if user has disabled rating notifications
        if (method_exists($notifiable, 'hasRatingNotificationsDisabled') &&
            $notifiable->hasRatingNotificationsDisabled()) {
            return false;
        }

        return true;
    }
}
