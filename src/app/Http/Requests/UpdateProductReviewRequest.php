<?php

namespace Fereydooni\Shopping\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fereydooni\Shopping\app\DTOs\ProductReviewDTO;

class UpdateProductReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('review'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = ProductReviewDTO::rules();

        // Make some fields optional for updates
        $rules['product_id'] = ['sometimes', 'integer', 'exists:products,id'];
        $rules['user_id'] = ['sometimes', 'integer', 'exists:users,id'];
        $rules['rating'] = ['sometimes', 'integer', 'min:1', 'max:5'];
        $rules['review'] = ['sometimes', 'string', 'min:10', 'max:2000'];

        return $rules;
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
            'updated_by' => $this->updated_by ?? $this->user()->id,
        ]);
    }
}
