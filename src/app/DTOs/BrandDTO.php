<?php

namespace Fereydooni\Shopping\app\DTOs;

use Fereydooni\Shopping\app\Enums\BrandStatus;
use Fereydooni\Shopping\app\Enums\BrandType;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class BrandDTO extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $slug,
        public ?string $description,
        public ?string $website,
        public ?string $email,
        public ?string $phone,
        public ?int $founded_year,
        public ?string $headquarters,
        public ?string $logo_url,
        public ?string $banner_url,
        public ?string $meta_title,
        public ?string $meta_description,
        public ?string $meta_keywords,
        public bool $is_active,
        public bool $is_featured,
        public int $sort_order,
        public BrandStatus $status,
        public ?BrandType $type = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?int $products_count = null,
        public ?array $media = null,
    ) {}

    public static function fromModel($brand): static
    {
        return new static(
            id: $brand->id,
            name: $brand->name,
            slug: $brand->slug,
            description: $brand->description,
            website: $brand->website,
            email: $brand->email,
            phone: $brand->phone,
            founded_year: $brand->founded_year,
            headquarters: $brand->headquarters,
            logo_url: $brand->logo_url,
            banner_url: $brand->banner_url,
            meta_title: $brand->meta_title,
            meta_description: $brand->meta_description,
            meta_keywords: $brand->meta_keywords,
            is_active: $brand->is_active,
            is_featured: $brand->is_featured,
            sort_order: $brand->sort_order,
            status: $brand->status,
            type: $brand->type ?? null,
            created_at: $brand->created_at,
            updated_at: $brand->updated_at,
            products_count: $brand->products_count ?? null,
            media: $brand->getMedia() ? $brand->getMedia()->toArray() : null,
        );
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'founded_year' => 'nullable|integer|min:1800|max:'.(date('Y') + 1),
            'headquarters' => 'nullable|string|max:255',
            'logo_url' => 'nullable|url|max:500',
            'banner_url' => 'nullable|url|max:500',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
            'status' => 'nullable|in:'.implode(',', array_column(BrandStatus::cases(), 'value')),
            'type' => 'nullable|in:'.implode(',', array_column(BrandType::cases(), 'value')),
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Brand name is required',
            'name.max' => 'Brand name cannot exceed 255 characters',
            'slug.unique' => 'This slug is already taken',
            'website.url' => 'Please enter a valid website URL',
            'email.email' => 'Please enter a valid email address',
            'founded_year.integer' => 'Founded year must be a number',
            'founded_year.min' => 'Founded year cannot be earlier than 1800',
            'founded_year.max' => 'Founded year cannot be in the future',
            'logo_url.url' => 'Please enter a valid logo URL',
            'banner_url.url' => 'Please enter a valid banner URL',
            'meta_title.max' => 'Meta title cannot exceed 60 characters',
            'meta_description.max' => 'Meta description cannot exceed 160 characters',
            'meta_keywords.max' => 'Meta keywords cannot exceed 500 characters',
            'sort_order.integer' => 'Sort order must be a number',
            'sort_order.min' => 'Sort order cannot be negative',
            'status.in' => 'Invalid brand status selected',
            'type.in' => 'Invalid brand type selected',
        ];
    }

    /**
     * Get the brand's display name.
     */
    public function getDisplayName(): string
    {
        return $this->name;
    }

    /**
     * Check if the brand is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the brand is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Get the brand's status label.
     */
    public function getStatusLabel(): string
    {
        return $this->status->label();
    }

    /**
     * Get the brand's status color.
     */
    public function getStatusColor(): string
    {
        return $this->status->color();
    }

    /**
     * Get the brand's type label.
     */
    public function getTypeLabel(): ?string
    {
        return $this->type?->label();
    }

    /**
     * Get SEO data for the brand.
     */
    public function getSeoData(): array
    {
        return [
            'title' => $this->meta_title ?: $this->name,
            'description' => $this->meta_description ?: $this->description,
            'keywords' => $this->meta_keywords,
        ];
    }
}
