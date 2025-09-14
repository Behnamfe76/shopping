<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Fereydooni\Shopping\app\Models\ProductTag;

class ProductTagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any product tags.
     */
    public function viewAny($user): bool
    {
        return $user->hasAnyPermission(['product-tag.view.any', 'product-tag.view.own']);
    }

    /**
     * Determine whether the user can view product tag lenses.
     */
    public function viewLenses($user): bool
    {
        return $user->hasPermissionTo('product-tag.view.lenses');
    }

    /**
     * Determine whether the user can view the product tag.
     */
    public function view($user, ProductTag $tag): bool
    {
        // Check if user can view any product tags
        if ($user->can('product-tag.view.any')) {
            return true;
        }

        // Check if user can view own product tags
        if ($user->can('product-tag.view.own')) {
            // For product tags, ownership might be based on store/tenant
            // This is a simplified check - adjust based on your business logic
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create product tags.
     */
    public function create($user): bool
    {
        return $user->can('product-tag.create.any') || $user->can('product-tag.create.own');
    }

    /**
     * Determine whether the user can update the product tag.
     */
    public function update($user, ProductTag $tag): bool
    {
        // Check if user can update any product tags
        if ($user->can('product-tag.update.any')) {
            return true;
        }

        // Check if user can update own product tags
        if ($user->can('product-tag.update.own')) {
            // For product tags, ownership might be based on store/tenant
            // This is a simplified check - adjust based on your business logic
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the product tag.
     */
    public function delete($user, ProductTag $tag): bool
    {
        return $user->hasAnyPermission(['product-tag.delete.own', 'product-tag.delete.any', 'product-tag.delete.own']);
    }

    /**
     * Determine whether the user can delete some product tag.
     */
    public function deleteSome($user): bool
    {
        return $user->hasPermissionTo('product-tag.delete.some');
    }

    /**
     * Determine whether the user can delete all product tag.
     */
    public function deleteAll($user): bool
    {
        return $user->hasPermissionTo('product-tag.delete.all');
    }

    /**
     * Determine whether the user can toggle the product tag active status.
     */
    public function toggleActive($user): bool
    {
        // Check if user can toggle active status for any product tags
        if ($user->can('product-tag.toggle.active.any')) {
            return true;
        }

        // Check if user can toggle active status for own product tags
        if ($user->can('product-tag.toggle.active.own')) {
            // For product tags, ownership might be based on store/tenant
            // This is a simplified check - adjust based on your business logic
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the product tag featured status.
     */
    public function toggleFeatured($user, ProductTag $tag): bool
    {
        // Check if user can toggle featured status for any product tags
        if ($user->can('product-tag.toggle.featured.any')) {
            return true;
        }

        // Check if user can toggle featured status for own product tags
        if ($user->can('product-tag.toggle.featured.own')) {
            // For product tags, ownership might be based on store/tenant
            // This is a simplified check - adjust based on your business logic
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can search product tags.
     */
    public function search($user): bool
    {
        return $user->can('product-tag.search.any') || $user->can('product-tag.search.own');
    }

    /**
     * Determine whether the user can export product tags.
     */
    public function export($user): bool
    {
        return $user->can('product-tag.export');
    }

    /**
     * Determine whether the user can import product tags.
     */
    public function import($user): bool
    {
        return $user->can('product-tag.import');
    }

    /**
     * Determine whether the user can manage bulk operations on product tags.
     */
    public function bulkManage($user): bool
    {
        return $user->can('product-tag.bulk.manage.any') || $user->can('product-tag.bulk.manage.own');
    }

    /**
     * Determine whether the user can sync product tags.
     */
    public function sync($user): bool
    {
        return $user->can('product-tag.sync.any') || $user->can('product-tag.sync.own');
    }

    /**
     * Determine whether the user can merge product tags.
     */
    public function merge($user): bool
    {
        return $user->can('product-tag.merge.any') || $user->can('product-tag.merge.own');
    }

    /**
     * Determine whether the user can split product tags.
     */
    public function split($user): bool
    {
        return $user->can('product-tag.split.any') || $user->can('product-tag.split.own');
    }

    /**
     * Determine whether the user can view product tag analytics.
     */
    public function viewAnalytics($user): bool
    {
        return $user->can('product-tag.analytics.view.any') || $user->can('product-tag.analytics.view.own');
    }

    /**
     * Determine whether the user can view product tag reports.
     */
    public function viewReports($user): bool
    {
        return $user->can('product-tag.reports.view') || $user->can('product-tag.reports.generate');
    }

    /**
     * Determine whether the user can generate product tag reports.
     */
    public function generateReports($user): bool
    {
        return $user->can('product-tag.reports.generate');
    }
}
