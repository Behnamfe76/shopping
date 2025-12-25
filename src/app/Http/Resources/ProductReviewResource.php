<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Fereydooni\Shopping\app\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var ProductReview $this */
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'rating' => $this->rating,
            'review' => $this->review,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'status_color' => $this->status?->color(),
            'title' => $this->title,
            'pros' => $this->pros,
            'cons' => $this->cons,
            'verified_purchase' => $this->verified_purchase,
            'helpful_votes' => $this->helpful_votes,
            'total_votes' => $this->total_votes,
            'helpful_percentage' => $this->getHelpfulPercentage(),
            'sentiment_score' => $this->sentiment_score,
            'sentiment_label' => $this->getSentimentLabel(),
            'moderation_status' => $this->moderation_status,
            'moderation_notes' => $this->moderation_notes,
            'is_featured' => $this->is_featured,
            'is_verified' => $this->is_verified,
            'review_date' => $this->review_date?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Computed fields
            'is_approved' => $this->isApproved(),
            'is_pending' => $this->isPending(),
            'is_rejected' => $this->isRejected(),
            'is_featured_status' => $this->isFeatured(),
            'is_verified_status' => $this->isVerified(),
            'is_verified_purchase' => $this->isVerifiedPurchase(),

            // Relationships
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'title' => $this->product->title,
                    'slug' => $this->product->slug,
                    'sku' => $this->product->sku,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'created_by' => $this->whenLoaded('createdBy', function () {
                return [
                    'id' => $this->createdBy->id,
                    'name' => $this->createdBy->name,
                    'email' => $this->createdBy->email,
                ];
            }),
            'updated_by' => $this->whenLoaded('updatedBy', function () {
                return [
                    'id' => $this->updatedBy->id,
                    'name' => $this->updatedBy->name,
                    'email' => $this->updatedBy->email,
                ];
            }),
        ];
    }
}
