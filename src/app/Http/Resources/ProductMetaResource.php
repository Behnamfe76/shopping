<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Fereydooni\Shopping\app\Models\ProductMeta;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductMetaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var ProductMeta $this */
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'meta_key' => $this->meta_key,
            'meta_value' => $this->meta_value,
            'meta_type' => $this->meta_type,
            'meta_type_label' => $this->getMetaTypeEnum()->getLabel(),
            'is_public' => $this->is_public,
            'is_searchable' => $this->is_searchable,
            'is_filterable' => $this->is_filterable,
            'sort_order' => $this->sort_order,
            'description' => $this->description,
            'validation_rules' => $this->validation_rules,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Conditional relationships
            'product' => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            }),

            // Additional computed fields
            'is_owner' => $this->when(auth()->check(), function () {
                return $this->created_by === auth()->id();
            }),

            // Permissions
            'permissions' => $this->when(auth()->check(), function () {
                return [
                    'can_update' => auth()->user()->can('update', $this->resource),
                    'can_delete' => auth()->user()->can('delete', $this->resource),
                    'can_toggle_public' => auth()->user()->can('togglePublic', $this->resource),
                    'can_toggle_searchable' => auth()->user()->can('toggleSearchable', $this->resource),
                    'can_toggle_filterable' => auth()->user()->can('toggleFilterable', $this->resource),
                ];
            }),
        ];
    }
}
