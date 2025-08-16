<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewStatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'total_reviews' => $this->resource['total_reviews'] ?? 0,
            'average_rating' => $this->resource['average_rating'] ?? 0.0,
            'rating_distribution' => $this->resource['rating_distribution'] ?? [],
            'featured_reviews' => $this->resource['featured_reviews'] ?? 0,
            'verified_reviews' => $this->resource['verified_reviews'] ?? 0,
            'verified_purchases' => $this->resource['verified_purchases'] ?? 0,

            // Computed fields
            'total_ratings' => collect($this->resource['rating_distribution'] ?? [])->sum(),
            'rating_percentages' => $this->calculateRatingPercentages(),
            'overall_sentiment' => $this->calculateOverallSentiment(),
        ];
    }

    /**
     * Calculate rating percentages
     */
    private function calculateRatingPercentages(): array
    {
        $distribution = $this->resource['rating_distribution'] ?? [];
        $total = collect($distribution)->sum();

        if ($total === 0) {
            return array_fill(1, 5, 0);
        }

        $percentages = [];
        for ($i = 1; $i <= 5; $i++) {
            $percentages[$i] = round(($distribution[$i] ?? 0) / $total * 100, 2);
        }

        return $percentages;
    }

    /**
     * Calculate overall sentiment
     */
    private function calculateOverallSentiment(): string
    {
        $averageRating = $this->resource['average_rating'] ?? 0;

        if ($averageRating >= 4.0) {
            return 'positive';
        } elseif ($averageRating >= 3.0) {
            return 'neutral';
        } else {
            return 'negative';
        }
    }
}
