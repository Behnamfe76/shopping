<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Fereydooni\Shopping\app\DTOs\CategoryDTO;

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
            'description' => $category->description,
            'status' => $category->status->value,
            'status_label' => $category->status->label(),
            'sort_order' => $category->sort_order,
            'is_default' => $category->is_default,
            'created_at' => $category->created_at?->toISOString(),
            'updated_at' => $category->updated_at?->toISOString(),
        ];

        // Include parent information if available
        if ($category->parent) {
            $data['parent'] = [
                'id' => $category->parent->id,
                'name' => $category->parent->name,
                'slug' => $category->parent->slug,
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

        // Include media if available
        if ($category->media !== null) {
            $data['media'] = $category->media;
        }

        // Include hierarchical information if requested
        if ($request->boolean('include_hierarchy')) {
            $data['children'] = $category->children;
            $data['ancestors'] = $this->getAncestors($category);
        }

        // Include actions if user is authenticated
        if ($request->user()) {
            $data['actions'] = $this->getAvailableActions($request, $category);
        }

        return $data;
    }

    /**
     * Get available actions for the category.
     */
    private function getAvailableActions(Request $request, CategoryDTO $category): array
    {
        $actions = [];

        if ($request->user()) {
            $user = $request->user();

            // Update action
            if ($user->can('update', $this->resource)) {
                $actions['update'] = [
                    'method' => 'PUT',
                    'url' => route('api.v1.categories.update', $category->slug),
                    'label' => 'Update Category',
                ];
            }

            // Delete action
            if ($user->can('delete', $this->resource)) {
                $actions['delete'] = [
                    'method' => 'DELETE',
                    'url' => route('api.v1.categories.destroy', $category->slug),
                    'label' => 'Delete Category',
                ];
            }

            // Set as default action
            if (!$category->is_default && $user->can('setDefault', $this->resource)) {
                $actions['set_default'] = [
                    'method' => 'POST',
                    'url' => route('api.v1.categories.set-default', $category->slug),
                    'label' => 'Set as Default',
                ];
            }

            // Move action
            if ($user->can('move', $this->resource)) {
                $actions['move'] = [
                    'method' => 'POST',
                    'url' => route('api.v1.categories.move', $category->slug),
                    'label' => 'Move Category',
                ];
            }

            // Manage media action
            if ($user->can('manageMedia', $this->resource)) {
                $actions['manage_media'] = [
                    'method' => 'POST',
                    'url' => route('api.v1.categories.media', $category->slug),
                    'label' => 'Manage Media',
                ];
            }

            // View children action
            if ($user->can('view', $this->resource)) {
                $actions['view_children'] = [
                    'method' => 'GET',
                    'url' => route('api.v1.categories.children', $category->slug),
                    'label' => 'View Children',
                ];
            }

            // View ancestors action
            if ($user->can('view', $this->resource)) {
                $actions['view_ancestors'] = [
                    'method' => 'GET',
                    'url' => route('api.v1.categories.ancestors', $category->slug),
                    'label' => 'View Ancestors',
                ];
            }

            // View descendants action
            if ($user->can('view', $this->resource)) {
                $actions['view_descendants'] = [
                    'method' => 'GET',
                    'url' => route('api.v1.categories.descendants', $category->slug),
                    'label' => 'View Descendants',
                ];
            }
        }

        return $actions;
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
