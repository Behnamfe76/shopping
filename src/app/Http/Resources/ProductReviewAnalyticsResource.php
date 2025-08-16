<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewAnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'total_reviews' => $this->resource['total_reviews'] ?? 0,
            'average_sentiment' => $this->resource['average_sentiment'] ?? 0.0,
            'positive_reviews' => $this->resource['positive_reviews'] ?? 0,
            'negative_reviews' => $this->resource['negative_reviews'] ?? 0,
            'neutral_reviews' => $this->resource['neutral_reviews'] ?? 0,
            'total_votes' => $this->resource['total_votes'] ?? 0,
            'total_helpful_votes' => $this->resource['total_helpful_votes'] ?? 0,

            // Computed fields
            'sentiment_distribution' => $this->calculateSentimentDistribution(),
            'vote_engagement_rate' => $this->calculateVoteEngagementRate(),
            'overall_sentiment_label' => $this->calculateOverallSentimentLabel(),
        ];
    }

    /**
     * Calculate sentiment distribution
     */
    private function calculateSentimentDistribution(): array
    {
        $total = ($this->resource['positive_reviews'] ?? 0) +
                 ($this->resource['negative_reviews'] ?? 0) +
                 ($this->resource['neutral_reviews'] ?? 0);

        if ($total === 0) {
            return [
                'positive' => 0,
                'negative' => 0,
                'neutral' => 0,
            ];
        }

        return [
            'positive' => round(($this->resource['positive_reviews'] ?? 0) / $total * 100, 2),
            'negative' => round(($this->resource['negative_reviews'] ?? 0) / $total * 100, 2),
            'neutral' => round(($this->resource['neutral_reviews'] ?? 0) / $total * 100, 2),
        ];
    }

    /**
     * Calculate vote engagement rate
     */
    private function calculateVoteEngagementRate(): float
    {
        $totalReviews = $this->resource['total_reviews'] ?? 0;
        $totalVotes = $this->resource['total_votes'] ?? 0;

        if ($totalReviews === 0) {
            return 0.0;
        }

        return round($totalVotes / $totalReviews, 2);
    }

    /**
     * Calculate overall sentiment label
     */
    private function calculateOverallSentimentLabel(): string
    {
        $averageSentiment = $this->resource['average_sentiment'] ?? 0;

        if ($averageSentiment > 0.3) {
            return 'positive';
        } elseif ($averageSentiment < -0.3) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }
}
