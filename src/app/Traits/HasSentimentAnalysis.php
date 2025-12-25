<?php

namespace Fereydooni\Shopping\app\Traits;

trait HasSentimentAnalysis
{
    /**
     * Analyze sentiment of text
     */
    public function analyzeSentiment(string $text): float
    {
        // Simple sentiment analysis implementation
        // In a real application, you would use a proper sentiment analysis service
        $positiveWords = [
            'good', 'great', 'excellent', 'amazing', 'wonderful', 'perfect', 'love', 'like', 'best', 'awesome',
            'fantastic', 'outstanding', 'superb', 'brilliant', 'fabulous', 'terrific', 'incredible', 'phenomenal',
            'satisfied', 'happy', 'pleased', 'delighted', 'thrilled', 'impressed', 'recommend', 'quality', 'value',
        ];

        $negativeWords = [
            'bad', 'terrible', 'awful', 'horrible', 'worst', 'hate', 'dislike', 'poor', 'disappointing',
            'terrible', 'awful', 'horrible', 'worst', 'hate', 'dislike', 'poor', 'disappointing',
            'useless', 'waste', 'broken', 'defective', 'faulty', 'cheap', 'expensive', 'overpriced',
            'difficult', 'complicated', 'confusing', 'frustrated', 'angry', 'upset', 'annoyed',
        ];

        $text = strtolower($text);
        $words = str_word_count($text, 1);

        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($words as $word) {
            if (in_array($word, $positiveWords)) {
                $positiveCount++;
            } elseif (in_array($word, $negativeWords)) {
                $negativeCount++;
            }
        }

        $total = count($words);

        if ($total === 0) {
            return 0.0;
        }

        return ($positiveCount - $negativeCount) / $total;
    }

    /**
     * Get sentiment label
     */
    public function getSentimentLabel(float $score): string
    {
        if ($score > 0.3) {
            return 'Positive';
        } elseif ($score < -0.3) {
            return 'Negative';
        } else {
            return 'Neutral';
        }
    }

    /**
     * Check if sentiment is positive
     */
    public function isPositiveSentiment(float $score): bool
    {
        return $score > 0.3;
    }

    /**
     * Check if sentiment is negative
     */
    public function isNegativeSentiment(float $score): bool
    {
        return $score < -0.3;
    }

    /**
     * Check if sentiment is neutral
     */
    public function isNeutralSentiment(float $score): bool
    {
        return $score >= -0.3 && $score <= 0.3;
    }

    /**
     * Get sentiment color for UI
     */
    public function getSentimentColor(float $score): string
    {
        if ($this->isPositiveSentiment($score)) {
            return 'success';
        } elseif ($this->isNegativeSentiment($score)) {
            return 'danger';
        } else {
            return 'warning';
        }
    }

    /**
     * Analyze sentiment trends
     */
    public function analyzeSentimentTrends(array $sentiments): array
    {
        if (empty($sentiments)) {
            return [
                'average' => 0.0,
                'positive_count' => 0,
                'negative_count' => 0,
                'neutral_count' => 0,
                'trend' => 'neutral',
            ];
        }

        $average = array_sum($sentiments) / count($sentiments);
        $positiveCount = count(array_filter($sentiments, fn ($s) => $this->isPositiveSentiment($s)));
        $negativeCount = count(array_filter($sentiments, fn ($s) => $this->isNegativeSentiment($s)));
        $neutralCount = count(array_filter($sentiments, fn ($s) => $this->isNeutralSentiment($s)));

        $trend = 'neutral';
        if ($positiveCount > $negativeCount) {
            $trend = 'positive';
        } elseif ($negativeCount > $positiveCount) {
            $trend = 'negative';
        }

        return [
            'average' => round($average, 3),
            'positive_count' => $positiveCount,
            'negative_count' => $negativeCount,
            'neutral_count' => $neutralCount,
            'trend' => $trend,
        ];
    }
}
