<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Fereydooni\Shopping\app\DTOs\DepartmentDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        /** @var DepartmentDTO $department */
        $department = $this->resource;

        $data = [
            'id' => $department->id,
            'name' => $department->name,
            'code' => $department->code,
            'description' => $department->description,
            'parent_id' => $department->parent_id,
            'manager_id' => $department->manager_id,
            'location' => $department->location,
            'budget' => $department->budget,
            'headcount_limit' => $department->headcount_limit,
            'is_active' => $department->is_active,
            'status' => __('departments.statuses.'.$department->status->value),
            'status_value' => $department->status->value,
            'status_label' => $department->status->label(),
            'status_color' => $department->status->color(),
            'metadata' => $department->metadata,
            'created_at' => $department->created_at?->toISOString(),
            'updated_at' => $department->updated_at?->toISOString(),
        ];

        // Include parent information if available
        if ($department->parent) {
            $data['parent'] = [
                'id' => $department->parent->id,
                'name' => $department->parent->name,
                'code' => $department->parent->code,
            ];
        } else {
            $data['parent'] = null;
        }

        // Include manager information if available
        if ($department->manager) {
            $data['manager'] = [
                'id' => $department->manager->id,
                'name' => $department->manager->name,
            ];
        } else {
            $data['manager'] = null;
        }

        // // Include children count if available
        // if ($department->children !== null) {
        //     $data['children_count'] = count($department->children);
        // }

        // // Include employee count if available
        // if ($department->employee_count !== null) {
        //     $data['employee_count'] = $department->employee_count;
        // }

        // // Include depth if available
        // if ($department->depth !== null) {
        //     $data['depth'] = $department->depth;
        // }

        // // Include path if available
        // if ($department->path !== null) {
        //     $data['path'] = $department->path;
        // }

        // // Include hierarchical information if requested
        // if ($request->boolean('include_hierarchy')) {
        //     $data['children'] = $department->children;
        //     $data['ancestors'] = $this->getAncestors($department);
        // }

        return $data;
    }

    /**
     * Get ancestors for the department.
     */
    private function getAncestors(DepartmentDTO $department): array
    {
        $ancestors = [];
        $current = $department->parent;

        while ($current) {
            $ancestors[] = [
                'id' => $current->id,
                'name' => $current->name,
                'code' => $current->code,
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
                'type' => 'department',
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }
}
