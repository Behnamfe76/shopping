<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\FloatType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Fereydooni\Shopping\app\Models\ProductReview;
use Fereydooni\Shopping\app\Enums\ReviewStatus;
use Illuminate\Support\Carbon;

class ProductReviewDTO extends Data
{
    public function __construct(
        public int $product_id,
        public int $user_id,
        #[Min(1), Max(5)]
        public int $rating,
        #[Required, StringType, Min(10), Max(2000)]
        public string $review,
        #[In(['pending', 'approved', 'rejected', 'flagged', 'spam'])]
        public string $status = ReviewStatus::PENDING->value,
        #[Nullable, StringType, Max(255)]
        public ?string $title = null,
        #[Nullable, StringType, Max(1000)]
        public ?string $pros = null,
        #[Nullable, StringType, Max(1000)]
        public ?string $cons = null,
        #[BooleanType]
        public bool $verified_purchase = false,
        #[IntegerType, Min(0)]
        public int $helpful_votes = 0,
        #[IntegerType, Min(0)]
        public int $total_votes = 0,
        #[FloatType, Min(-1), Max(1)]
        public float $sentiment_score = 0.0,
        #[Nullable, StringType, Max(50)]
        public ?string $moderation_status = null,
        #[Nullable, StringType, Max(1000)]
        public ?string $moderation_notes = null,
        #[BooleanType]
        public bool $is_featured = false,
        #[BooleanType]
        public bool $is_verified = false,
        #[Nullable, Date]
        public ?Carbon $review_date = null,
        #[Nullable, IntegerType]
        public ?int $created_by = null,
        #[Nullable, IntegerType]
        public ?int $updated_by = null,
        #[Nullable, Date]
        public ?Carbon $created_at = null,
        #[Nullable, Date]
        public ?Carbon $updated_at = null,
        #[Nullable, IntegerType]
        public ?int $id = null,
    ) {
    }

    public static function fromModel(ProductReview $review): self
    {
        return new self(
            product_id: $review->product_id,
            user_id: $review->user_id,
            rating: $review->rating,
            review: $review->review,
            status: $review->status->value,
            title: $review->title,
            pros: $review->pros,
            cons: $review->cons,
            verified_purchase: $review->verified_purchase,
            helpful_votes: $review->helpful_votes,
            total_votes: $review->total_votes,
            sentiment_score: $review->sentiment_score,
            moderation_status: $review->moderation_status,
            moderation_notes: $review->moderation_notes,
            is_featured: $review->is_featured,
            is_verified: $review->is_verified,
            review_date: $review->review_date,
            created_by: $review->created_by,
            updated_by: $review->updated_by,
            created_at: $review->created_at,
            updated_at: $review->updated_at,
            id: $review->id,
        );
    }

    public static function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['required', 'string', 'min:10', 'max:2000'],
            'status' => ['sometimes', 'string', 'in:' . implode(',', ReviewStatus::values())],
            'title' => ['nullable', 'string', 'max:255'],
            'pros' => ['nullable', 'string', 'max:1000'],
            'cons' => ['nullable', 'string', 'max:1000'],
            'verified_purchase' => ['sometimes', 'boolean'],
            'helpful_votes' => ['sometimes', 'integer', 'min:0'],
            'total_votes' => ['sometimes', 'integer', 'min:0'],
            'sentiment_score' => ['sometimes', 'numeric', 'min:-1', 'max:1'],
            'moderation_status' => ['nullable', 'string', 'max:50'],
            'moderation_notes' => ['nullable', 'string', 'max:1000'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_verified' => ['sometimes', 'boolean'],
            'review_date' => ['nullable', 'date'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'updated_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public static function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'rating.required' => 'Rating is required.',
            'rating.min' => 'Rating must be at least 1.',
            'rating.max' => 'Rating must not exceed 5.',
            'review.required' => 'Review text is required.',
            'review.min' => 'Review must be at least 10 characters.',
            'review.max' => 'Review must not exceed 2000 characters.',
            'status.in' => 'Invalid review status.',
            'title.max' => 'Title must not exceed 255 characters.',
            'pros.max' => 'Pros must not exceed 1000 characters.',
            'cons.max' => 'Cons must not exceed 1000 characters.',
            'helpful_votes.min' => 'Helpful votes cannot be negative.',
            'total_votes.min' => 'Total votes cannot be negative.',
            'sentiment_score.min' => 'Sentiment score must be at least -1.',
            'sentiment_score.max' => 'Sentiment score must not exceed 1.',
            'moderation_status.max' => 'Moderation status must not exceed 50 characters.',
            'moderation_notes.max' => 'Moderation notes must not exceed 1000 characters.',
            'review_date.date' => 'Review date must be a valid date.',
            'created_by.exists' => 'The selected creator does not exist.',
            'updated_by.exists' => 'The selected updater does not exist.',
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'rating' => $this->rating,
            'review' => $this->review,
            'status' => $this->status,
            'title' => $this->title,
            'pros' => $this->pros,
            'cons' => $this->cons,
            'verified_purchase' => $this->verified_purchase,
            'helpful_votes' => $this->helpful_votes,
            'total_votes' => $this->total_votes,
            'sentiment_score' => $this->sentiment_score,
            'moderation_status' => $this->moderation_status,
            'moderation_notes' => $this->moderation_notes,
            'is_featured' => $this->is_featured,
            'is_verified' => $this->is_verified,
            'review_date' => $this->review_date?->toISOString(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'helpful_percentage' => $this->getHelpfulPercentage(),
            'sentiment_label' => $this->getSentimentLabel(),
        ];
    }

    public function getHelpfulPercentage(): float
    {
        if ($this->total_votes === 0) {
            return 0.0;
        }

        return round(($this->helpful_votes / $this->total_votes) * 100, 2);
    }

    public function getSentimentLabel(): string
    {
        if ($this->sentiment_score > 0.3) {
            return 'Positive';
        } elseif ($this->sentiment_score < -0.3) {
            return 'Negative';
        } else {
            return 'Neutral';
        }
    }

    public function isApproved(): bool
    {
        return $this->status === ReviewStatus::APPROVED->value;
    }

    public function isPending(): bool
    {
        return $this->status === ReviewStatus::PENDING->value;
    }

    public function isRejected(): bool
    {
        return $this->status === ReviewStatus::REJECTED->value;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isVerifiedPurchase(): bool
    {
        return $this->verified_purchase;
    }
}
