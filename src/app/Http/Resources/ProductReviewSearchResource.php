<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'rating' => $this->rating,
            'review' => $this->review,
            'title' => $this->title,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'is_featured' => $this->is_featured,
            'is_verified' => $this->is_verified,
            'helpful_votes' => $this->helpful_votes,
            'total_votes' => $this->total_votes,
            'helpful_percentage' => $this->getHelpfulPercentage(),
            'sentiment_score' => $this->sentiment_score,
            'sentiment_label' => $this->getSentimentLabel(),
            'created_at' => $this->created_at?->toISOString(),

            // Search metadata
            'search_score' => $this->search_score ?? null,
            'highlighted_review' => $this->highlighted_review ?? null,
            'highlighted_title' => $this->highlighted_title ?? null,

            // Relationships
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'title' => $this->product->title,
                    'slug' => $this->product->slug,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
        ];
    }
}
