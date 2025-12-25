<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class AnalyzeReviewSentiment implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $review = $event->review;

        // Analyze sentiment of review text
        $sentimentScore = $this->analyzeSentiment($review->review);

        // Update review with sentiment score
        $review->update([
            'sentiment_score' => $sentimentScore,
        ]);

        Log::info('Review sentiment analyzed', [
            'review_id' => $review->id,
            'sentiment_score' => $sentimentScore,
            'review_text_length' => strlen($review->review),
        ]);
    }

    /**
     * Analyze sentiment of text
     */
    private function analyzeSentiment(string $text): float
    {
        // Simple sentiment analysis implementation
        // In a real application, you would use a proper sentiment analysis service

        $positiveWords = ['good', 'great', 'excellent', 'amazing', 'wonderful', 'perfect', 'love', 'like', 'best', 'awesome'];
        $negativeWords = ['bad', 'terrible', 'awful', 'horrible', 'worst', 'hate', 'dislike', 'poor', 'disappointing', 'useless'];

        $text = strtolower($text);
        $words = str_word_count($text, 1);

        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $positiveCount++;
            }
            if (in_array($word, $negativeWords)) {
                $negativeCount++;
            }
        }

        $totalWords = count($words);
        if ($totalWords === 0) {
            return 0.0;
        }

        // Calculate sentiment score (-1 to 1)
        $sentimentScore = ($positiveCount - $negativeCount) / $totalWords;

        // Normalize to 0-1 range
        return ($sentimentScore + 1) / 2;
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Failed to analyze review sentiment', [
            'review_id' => $event->review->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
