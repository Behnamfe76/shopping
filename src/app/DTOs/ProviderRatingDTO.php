<?php

namespace App\DTOs;

use App\Enums\RatingCategory;
use App\Enums\RatingStatus;
use App\Models\ProviderRating;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Boolean;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Ip;
use Spatie\LaravelData\Attributes\Validation\Json;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\In;

class ProviderRatingDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id = null,

        #[Required, Exists('providers', 'id')]
        public int $provider_id,

        #[Required, Exists('users', 'id')]
        public int $user_id,

        #[Required, Numeric, Min(1), Max(5)]
        public float $rating_value,

        #[Required, Numeric, Min(1), Max(10)]
        public float $max_rating = 5.0,

        #[Required, In(RatingCategory::class)]
        #[WithCast(EnumCast::class, RatingCategory::class)]
        public RatingCategory $category = RatingCategory::OVERALL,

        #[Required, StringType, Max(255)]
        public string $title,

        #[Required, StringType, Max(1000)]
        public string $comment,

        #[Nullable, StringType, Max(500)]
        public ?string $pros = null,

        #[Nullable, StringType, Max(500)]
        public ?string $cons = null,

        #[Required, Boolean]
        public bool $would_recommend = true,

        #[Nullable, Json]
        public ?array $rating_criteria = null,

        #[Numeric, Min(0)]
        public int $helpful_votes = 0,

        #[Numeric, Min(0)]
        public int $total_votes = 0,

        #[Boolean]
        public bool $is_verified = false,

        #[Required, In(RatingStatus::class)]
        #[WithCast(EnumCast::class, RatingStatus::class)]
        public RatingStatus $status = RatingStatus::PENDING,

        #[Nullable, Ip]
        public ?string $ip_address = null,

        #[Nullable, StringType, Max(500)]
        public ?string $user_agent = null,

        #[Nullable, Date]
        public ?string $created_at = null,

        #[Nullable, Date]
        public ?string $updated_at = null,
    ) {
    }

    public static function fromModel(ProviderRating $rating): static
    {
        return new static(
            id: $rating->id,
            provider_id: $rating->provider_id,
            user_id: $rating->user_id,
            rating_value: $rating->rating_value,
            max_rating: $rating->max_rating,
            category: $rating->category,
            title: $rating->title,
            comment: $rating->comment,
            pros: $rating->pros,
            cons: $rating->cons,
            would_recommend: $rating->would_recommend,
            rating_criteria: $rating->rating_criteria,
            helpful_votes: $rating->helpful_votes,
            total_votes: $rating->total_votes,
            is_verified: $rating->is_verified,
            status: $rating->status,
            ip_address: $rating->ip_address,
            user_agent: $rating->user_agent,
            created_at: $rating->created_at?->toDateString(),
            updated_at: $rating->updated_at?->toDateString(),
        );
    }

    public static function rules(): array
    {
        return [
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'rating_value' => ['required', 'numeric', 'min:1', 'max:5'],
            'max_rating' => ['required', 'numeric', 'min:1', 'max:10'],
            'category' => ['required', 'string', 'in:' . implode(',', RatingCategory::values())],
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['required', 'string', 'max:1000'],
            'pros' => ['nullable', 'string', 'max:500'],
            'cons' => ['nullable', 'string', 'max:500'],
            'would_recommend' => ['required', 'boolean'],
            'rating_criteria' => ['nullable', 'array'],
            'helpful_votes' => ['numeric', 'min:0'],
            'total_votes' => ['numeric', 'min:0'],
            'is_verified' => ['boolean'],
            'status' => ['required', 'string', 'in:' . implode(',', RatingStatus::values())],
            'ip_address' => ['nullable', 'ip'],
            'user_agent' => ['nullable', 'string', 'max:500'],
        ];
    }

    public static function messages(): array
    {
        return [
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'The selected provider does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'rating_value.required' => 'Rating value is required.',
            'rating_value.min' => 'Rating value must be at least 1.',
            'rating_value.max' => 'Rating value cannot exceed 5.',
            'title.required' => 'Rating title is required.',
            'title.max' => 'Rating title cannot exceed 255 characters.',
            'comment.required' => 'Rating comment is required.',
            'comment.max' => 'Rating comment cannot exceed 1000 characters.',
            'pros.max' => 'Pros cannot exceed 500 characters.',
            'cons.max' => 'Cons cannot exceed 500 characters.',
            'category.in' => 'Invalid rating category.',
            'status.in' => 'Invalid rating status.',
            'ip_address.ip' => 'Invalid IP address format.',
            'user_agent.max' => 'User agent cannot exceed 500 characters.',
        ];
    }
}
