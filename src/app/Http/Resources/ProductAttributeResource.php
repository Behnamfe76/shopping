<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Fereydooni\Shopping\app\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var ProductAttribute $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => $this->type?->value,
            'type_label' => $this->type?->label(),
            'input_type' => $this->input_type?->value,
            'input_type_label' => $this->input_type?->label(),
            'is_required' => $this->is_required,
            'is_searchable' => $this->is_searchable,
            'is_filterable' => $this->is_filterable,
            'is_comparable' => $this->is_comparable,
            'is_visible' => $this->is_visible,
            'sort_order' => $this->sort_order,
            'validation_rules' => $this->validation_rules,
            'default_value' => $this->default_value,
            'unit' => $this->unit,
            'group' => $this->group,
            'is_system' => $this->is_system,
            'is_active' => $this->is_active,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships
            'values' => $this->whenLoaded('values', function () {
                return $this->values->map(function ($value) {
                    return $value->value;
                });
            }),
            'values_count' => $this->whenCounted('values'),
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                    'email' => $this->creator->email,
                ];
            }),
            'updater' => $this->whenLoaded('updater', function () {
                return [
                    'id' => $this->updater->id,
                    'name' => $this->updater->name,
                    'email' => $this->updater->email,
                ];
            }),

            // Computed attributes
            'display_name' => $this->name.($this->unit ? ' ('.$this->unit.')' : ''),
            'has_values' => $this->whenLoaded('has_values', fn () => $this->values()->exists()),
            'active_values_count' => $this->whenLoaded('active_values_count', fn () => $this->values()->where('is_active', true)->count()),
        ];
    }
}
