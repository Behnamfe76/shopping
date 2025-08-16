<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeTransformer;
use Illuminate\Support\Carbon;

class ProductAttributeValueDTO extends Data
{
    public function __construct(
        public ?int $id,
        public int $attribute_id,
        public string $value,
        public ?array $metadata = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?ProductAttributeDTO $attribute = null,
        public ?int $usage_count = null,
    ) {
    }

    public static function fromModel($attributeValue): static
    {
        return new static(
            id: $attributeValue->id,
            attribute_id: $attributeValue->attribute_id,
            value: $attributeValue->value,
            metadata: $attributeValue->metadata ? json_decode($attributeValue->metadata, true) : null,
            created_at: $attributeValue->created_at,
            updated_at: $attributeValue->updated_at,
            attribute: $attributeValue->attribute ? ProductAttributeDTO::fromModel($attributeValue->attribute) : null,
            usage_count: $attributeValue->usage_count ?? null,
        );
    }

    public static function rules(): array
    {
        return [
            'attribute_id' => 'required|integer|exists:product_attributes,id',
            'value' => 'required|string|max:1000',
            'metadata' => 'nullable|array',
        ];
    }

    public static function messages(): array
    {
        return [
            'attribute_id.required' => 'Attribute ID is required',
            'attribute_id.integer' => 'Attribute ID must be a number',
            'attribute_id.exists' => 'Selected attribute does not exist',
            'value.required' => 'Attribute value is required',
            'value.string' => 'Attribute value must be a string',
            'value.max' => 'Attribute value cannot exceed 1000 characters',
            'metadata.array' => 'Metadata must be an array',
        ];
    }
}

