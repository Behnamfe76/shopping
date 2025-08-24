<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderPerformanceSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'provider_id' => $this->provider_id,
            'provider_name' => $this->whenLoaded('provider', function() {
                return $this->provider->company_name;
            }),
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'period_type' => $this->period_type,
            'performance_score' => $this->performance_score,
            'performance_grade' => $this->performance_grade,
            'total_orders' => $this->total_orders,
            'total_revenue' => $this->total_revenue,
            'customer_satisfaction_score' => $this->customer_satisfaction_score,
            'on_time_delivery_rate' => $this->on_time_delivery_rate,
            'is_verified' => $this->is_verified,
            'verified_at' => $this->verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'search_highlights' => $this->getSearchHighlights(),
            'relevance_score' => $this->calculateRelevanceScore(),
        ];
    }

    /**
     * Get search highlights for the query
     */
    private function getSearchHighlights(): array
    {
        $highlights = [];

        // Add highlights based on searchable fields
        if ($this->performance_grade) {
            $highlights[] = "Grade: {$this->performance_grade}";
        }

        if ($this->performance_score) {
            $highlights[] = "Score: {$this->performance_score}";
        }

        if ($this->total_revenue) {
            $highlights[] = "Revenue: $" . number_format($this->total_revenue, 2);
        }

        return $highlights;
    }

    /**
     * Calculate relevance score for search ranking
     */
    private function calculateRelevanceScore(): float
    {
        $score = 0;

        // Base score from performance
        if ($this->performance_score) {
            $score += $this->performance_score * 0.3;
        }

        // Verification bonus
        if ($this->is_verified) {
            $score += 10;
        }

        // Recency bonus
        $daysSinceUpdate = now()->diffInDays($this->updated_at);
        if ($daysSinceUpdate <= 30) {
            $score += 5;
        } elseif ($daysSinceUpdate <= 90) {
            $score += 2;
        }

        return round($score, 2);
    }
}
