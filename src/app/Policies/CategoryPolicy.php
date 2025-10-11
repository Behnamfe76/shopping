<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Fereydooni\Shopping\app\Models\Category;
use Fereydooni\Shopping\app\Enums\CategoryStatus;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any categories.
     */
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission('category.view.any', 'category.view.own');
    }

    /**
     * Determine whether the user can view the category.
     */
    public function view($user, Category $category): bool
    {
        // Check if user can view any categories
        if ($user->can('category.view.any')) {
            return true;
        }

        // Check if user can view own categories
        if ($user->can('category.view.own')) {
            // For categories, ownership might be based on store/tenant
            // This is a simplified check - adjust based on your business logic
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create($user): bool
    {
        return $user->can('category.create.any') || $user->can('category.create.own');
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update($user, Category $category): bool
    {
        // Check if user can update any categories
        if ($user->can('category.update.any')) {
            return true;
        }

        // Check if user can update own categories
        if ($user->can('category.update.own')) {
            // For categories, ownership might be based on store/tenant
            // This is a simplified check - adjust based on your business logic
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete($user, Category $category): bool
    {
        // Check if user can delete any categories
        if ($user->can('category.delete.any')) {
            return true;
        }

        // Check if user can delete own categories
        if ($user->can('category.delete.own')) {
            // For categories, ownership might be based on store/tenant
            // This is a simplified check - adjust based on your business logic
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete some product tag.
     */
    public function deleteSome($user): bool
    {
        return $user->hasPermissionTo('category.delete.some');
    }

    /**
     * Determine whether the user can delete all product tag.
     */
    public function deleteAll($user): bool
    {
        return $user->hasPermissionTo('category.delete.all');
    }

    /**
     * Determine whether the user can set the category as default.
     */
    public function setDefault($user, Category $category): bool
    {
        // Check if user can set any category as default
        if ($user->can('category.set.default.any')) {
            return true;
        }

        // Check if user can set own category as default
        if ($user->can('category.set.default.own')) {
            // For categories, ownership might be based on store/tenant
            // This is a simplified check - adjust based on your business logic
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can search categories.
     */
    public function search($user): bool
    {
        return $user->can('category.search.any') || $user->can('category.search.own');
    }

    /**
     * Determine whether the user can export categories.
     */
    public function export($user): bool
    {
        return $user->can('category.export');
    }

    /**
     * Determine whether the user can import categories.
     */
    public function import($user): bool
    {
        return $user->can('category.import');
    }

    /**
     * Determine whether the user can manage category hierarchy.
     */
    public function manageHierarchy($user): bool
    {
        return $user->can('category.manage.hierarchy.any') || $user->can('category.manage.hierarchy.own');
    }

    /**
     * Determine whether the user can reorder categories.
     */
    public function reorder($user): bool
    {
        return $user->can('category.reorder.any') || $user->can('category.reorder.own');
    }

    /**
     * Determine whether the user can move categories.
     */
    public function move($user, Category $category): bool
    {
        // Check if user can move any categories
        if ($user->can('category.move.any')) {
            return true;
        }

        // Check if user can move own categories
        if ($user->can('category.move.own')) {
            // For categories, ownership might be based on store/tenant
            // This is a simplified check - adjust based on your business logic
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage category media.
     */
    public function manageMedia($user, Category $category): bool
    {
        // Check if user can manage any category media
        if ($user->can('category.manage.media.any')) {
            return true;
        }

        // Check if user can manage own category media
        if ($user->can('category.manage.media.own')) {
            // For categories, ownership might be based on store/tenant
            // This is a simplified check - adjust based on your business logic
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view category statistics.
     */
    public function viewStats($user): bool
    {
        return $user->can('category.view.stats.any') || $user->can('category.view.stats.own');
    }

    /**
     * Determine whether the user can bulk delete categories.
     */
    public function bulkDelete($user): bool
    {
        return $user->can('category.bulk.delete');
    }

    /**
     * Determine whether the user can bulk update categories.
     */
    public function bulkUpdate($user): bool
    {
        return $user->can('category.bulk.update');
    }

    /**
     * Determine whether the user can bulk set default categories.
     */
    public function bulkSetDefault($user): bool
    {
        return $user->can('category.bulk.set.default');
    }

    /**
     * Determine whether the user can validate categories.
     */
    public function validate($user): bool
    {
        return $user->can('category.validate');
    }

    /**
     * Determine whether the user can restore categories.
     */
    public function restore($user, Category $category): bool
    {
        return $user->can('category.restore');
    }

    /**
     * Determine whether the user can force delete categories.
     */
    public function forceDelete($user, Category $category): bool
    {
        return $user->can('category.force.delete');
    }
}
