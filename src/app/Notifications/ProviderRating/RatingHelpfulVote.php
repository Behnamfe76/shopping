<?php

namespace Fereydooni\Shopping\App\Notifications\ProviderRating;

use App\Models\ProviderRating;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RatingHelpfulVote extends Notification implements ShouldQueue
{
    use Queueable;

    public ProviderRating $rating;
    public bool $isHelpful;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProviderRating $rating, bool $isHelpful = true)
    {
        $this->rating = $rating;
        $this->isHelpful = $isHelpful;
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

        $subject = $this->isHelpful
            ? 'Your rating received a helpful vote!'
            : 'Your rating received a vote';

        $message = $this->isHelpful
            ? "Great news! Another user found your rating for {$provider->name} helpful."
            : "Your rating for {$provider->name} received a vote from another user.";

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line($message)
            ->line("Rating: {$this->rating->rating_value}/{$this->rating->max_rating} stars")
            ->line("Category: {$this->rating->category}")
            ->when($this->rating->title, function ($mail) {
                return $mail->line("Title: {$this->rating->title}");
            })
            ->when($this->rating->comment, function ($mail) {
                return $mail->line("Comment: {$this->rating->comment}");
            })
            ->line("Helpful votes: {$this->rating->helpful_votes}")
            ->line("Total votes: {$this->rating->total_votes}")
            ->action('View Your Rating', url("/ratings/{$this->rating->id}"))
            ->line('Thank you for contributing to our community!')
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
            'type' => 'rating_helpful_vote',
            'rating_id' => $this->rating->id,
            'provider_id' => $this->rating->provider_id,
            'provider_name' => $this->rating->provider->name ?? 'Unknown Provider',
            'rating_value' => $this->rating->rating_value,
            'max_rating' => $this->rating->max_rating,
            'category' => $this->rating->category,
            'title' => $this->rating->title,
            'comment' => $this->rating->comment,
            'is_helpful' => $this->isHelpful,
            'helpful_votes' => $this->rating->helpful_votes,
            'total_votes' => $this->rating->total_votes,
            'voted_at' => now(),
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function getDatabaseType(): string
    {
        return 'rating_helpful_vote';
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

        // Don't send if rating is not approved
        if ($this->rating->status !== 'approved') {
            return false;
        }

        return true;
    }
}
