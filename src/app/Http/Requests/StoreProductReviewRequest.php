<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fereydooni\Shopping\app\DTOs\ProductReviewDTO;

class StoreProductReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \Fereydooni\Shopping\app\Models\ProductReview::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return ProductReviewDTO::rules();
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return ProductReviewDTO::messages();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'product',
            'user_id' => 'user',
            'rating' => 'rating',
            'review' => 'review text',
            'status' => 'status',
            'title' => 'title',
            'pros' => 'pros',
            'cons' => 'cons',
            'verified_purchase' => 'verified purchase',
            'helpful_votes' => 'helpful votes',
            'total_votes' => 'total votes',
            'sentiment_score' => 'sentiment score',
            'moderation_status' => 'moderation status',
            'moderation_notes' => 'moderation notes',
            'is_featured' => 'featured status',
            'is_verified' => 'verified status',
            'review_date' => 'review date',
            'created_by' => 'created by',
            'updated_by' => 'updated by',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user_id ?? $this->user()->id,
            'created_by' => $this->created_by ?? $this->user()->id,
            'updated_by' => $this->updated_by ?? $this->user()->id,
        ]);
    }
}
