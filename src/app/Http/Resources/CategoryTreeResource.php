<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryTreeResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->buildTree($this->collection),
            'tree_meta' => [
                'total_nodes' => $this->collection->count(),
                'max_depth' => $this->getMaxDepth($this->collection),
                'root_count' => $this->getRootCount($this->collection),
                'leaf_count' => $this->getLeafCount($this->collection),
                'generated_at' => now()->toISOString(),
            ],
            'meta' => [
                'type' => 'category_tree',
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }

    /**
     * Build hierarchical tree structure.
     */
    private function buildTree($categories): array
    {
        $tree = [];
        $lookup = [];

        // Create lookup table
        foreach ($categories as $category) {
            $lookup[$category->id] = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'status' => $category->status->value,
                'status_label' => $category->status->label(),
                'sort_order' => $category->sort_order,
                'is_default' => $category->is_default,
                'depth' => $category->depth ?? 0,
                'children' => [],
            ];
        }

        // Build tree structure
        foreach ($categories as $category) {
            $node = $lookup[$category->id];

            if ($category->parent_id === null) {
                // Root node
                $tree[] = $node;
            } else {
                // Child node
                if (isset($lookup[$category->parent_id])) {
                    $lookup[$category->parent_id]['children'][] = $node;
                }
            }
        }

        return $tree;
    }

    /**
     * Get maximum depth of the tree.
     */
    private function getMaxDepth($categories): int
    {
        $maxDepth = 0;
        foreach ($categories as $category) {
            $depth = $category->depth ?? 0;
            if ($depth > $maxDepth) {
                $maxDepth = $depth;
            }
        }
        return $maxDepth;
    }

    /**
     * Get count of root categories.
     */
    private function getRootCount($categories): int
    {
        return $categories->where('parent_id', null)->count();
    }

    /**
     * Get count of leaf categories (categories with no children).
     */
    private function getLeafCount($categories): int
    {
        $parentIds = $categories->pluck('parent_id')->filter()->unique();
        $allIds = $categories->pluck('id');

        return $allIds->diff($parentIds)->count();
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'type' => 'category_tree',
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }
}
