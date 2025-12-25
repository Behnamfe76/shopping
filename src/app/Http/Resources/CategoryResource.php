<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Fereydooni\Shopping\app\DTOs\CategoryDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var CategoryDTO $category */
        $category = $this->resource;

        $data = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'parent_id' => $category->parent_id,
            'description' => $category->description,
            'status' => __('categories.statuses.'.$category->status->value),
            'status_label' => $category->status->label(),
            'sort_order' => $category->sort_order,
            'is_default' => $category->is_default,
            'created_at' => $category->created_at?->toISOString(),
            'updated_at' => $category->updated_at?->toISOString(),
            'media' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($mediaItem) {
                    return [
                        'id' => $mediaItem->id,
                        'file_name' => $mediaItem->file_name,
                        'url' => $mediaItem->getUrl(),
                        // 'thumbnail_url' => $mediaItem->getUrl('thumb'),
                        // 'preview_url' => $mediaItem->getUrl('preview'),
                        // 'created_at' => $mediaItem->created_at?->toISOString(),
                    ];
                });
            }),
        ];

        // Include parent information if available
        if ($category->parent) {
            $data['parent'] = [
                'id' => $category->parent->id,
                'name' => $category->parent->name,
                'slug' => $category->parent->slug,
            ];
        } else {
            $data['parent'] = [
                'name' => __('resources/categories.messages.no_parent'),
            ];
        }

        // Include children count if available
        if ($category->children !== null) {
            $data['children_count'] = count($category->children);
        }

        // Include products count if available
        if ($category->products_count !== null) {
            $data['products_count'] = $category->products_count;
        }

        // Include depth if available
        if ($category->depth !== null) {
            $data['depth'] = $category->depth;
        }

        // Include path if available
        if ($category->path !== null) {
            $data['path'] = $category->path;
        }

        // Include hierarchical information if requested
        if ($request->boolean('include_hierarchy')) {
            $data['children'] = $category->children;
            $data['ancestors'] = $this->getAncestors($category);
        }

        return $data;
    }

    /**
     * Get ancestors for the category.
     */
    private function getAncestors(CategoryDTO $category): array
    {
        $ancestors = [];
        $current = $category->parent;

        while ($current) {
            $ancestors[] = [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ];
            $current = $current->parent;
        }

        return array_reverse($ancestors);
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'type' => 'category',
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }
}
