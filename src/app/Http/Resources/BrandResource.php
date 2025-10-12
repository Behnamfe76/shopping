<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Fereydooni\Shopping\app\Models\Brand;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var Brand $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'website' => $this->website,
            'email' => $this->email,
            'phone' => $this->phone,
            'founded_year' => $this->founded_year,
            'headquarters' => $this->headquarters,
            'logo_url' => $this->logo_url,
            'banner_url' => $this->banner_url,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'status' => __('brands.statuses.' . $this->status->value),
            'status_label' => $this->status->label(),
            'products_count' => $this->when(isset($this->products_count), $this->products_count),
            'media' => $this->when($this->relationLoaded('media'), function () {
                return $this->getMedia('*')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'url' => $media->getUrl(),
                        'size' => $media->size,
                        'mime_type' => $media->mime_type,
                        'collection_name' => $media->collection_name,
                    ];
                });
            }),
            'seo' => [
                'title' => $this->meta_title ?: $this->name,
                'description' => $this->meta_description ?: $this->description,
                'keywords' => $this->meta_keywords,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
