<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use Fereydooni\Shopping\app\Models\ProductVariant;

class ProductVariantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any product variants.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('product-variant.view.any');
    }

    /**
     * Determine whether the user can view the product variant.
     */
    public function view(User $user, ProductVariant $variant): bool
    {
        if ($user->can('product-variant.view.any')) {
            return true;
        }

        if ($user->can('product-variant.view.own')) {
            return $this->isOwner($user, $variant);
        }

        return false;
    }

    /**
     * Determine whether the user can create product variants.
     */
    public function create(User $user): bool
    {
        return $user->can('product-variant.create.any') || $user->can('product-variant.create.own');
    }

    /**
     * Determine whether the user can update the product variant.
     */
    public function update(User $user, ProductVariant $variant): bool
    {
        if ($user->can('product-variant.update.any')) {
            return true;
        }

        if ($user->can('product-variant.update.own')) {
            return $this->isOwner($user, $variant);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the product variant.
     */
    public function delete(User $user, ProductVariant $variant): bool
    {
        if ($user->can('product-variant.delete.any')) {
            return true;
        }

        if ($user->can('product-variant.delete.own')) {
            return $this->isOwner($user, $variant);
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the variant's active status.
     */
    public function toggleActive(User $user, ProductVariant $variant): bool
    {
        if ($user->can('product-variant.toggle.active.any')) {
            return true;
        }

        if ($user->can('product-variant.toggle.active.own')) {
            return $this->isOwner($user, $variant);
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the variant's featured status.
     */
    public function toggleFeatured(User $user, ProductVariant $variant): bool
    {
        if ($user->can('product-variant.toggle.featured.any')) {
            return true;
        }

        if ($user->can('product-variant.toggle.featured.own')) {
            return $this->isOwner($user, $variant);
        }

        return false;
    }

    /**
     * Determine whether the user can manage the variant's inventory.
     */
    public function manageInventory(User $user, ProductVariant $variant): bool
    {
        if ($user->can('product-variant.manage.inventory.any')) {
            return true;
        }

        if ($user->can('product-variant.manage.inventory.own')) {
            return $this->isOwner($user, $variant);
        }

        return false;
    }

    /**
     * Determine whether the user can manage the variant's pricing.
     */
    public function managePricing(User $user, ProductVariant $variant): bool
    {
        if ($user->can('product-variant.manage.pricing.any')) {
            return true;
        }

        if ($user->can('product-variant.manage.pricing.own')) {
            return $this->isOwner($user, $variant);
        }

        return false;
    }

    /**
     * Determine whether the user can search product variants.
     */
    public function search(User $user): bool
    {
        return $user->can('product-variant.search.any') || $user->can('product-variant.search.own');
    }

    /**
     * Determine whether the user can export product variants.
     */
    public function export(User $user): bool
    {
        return $user->can('product-variant.export');
    }

    /**
     * Determine whether the user can import product variants.
     */
    public function import(User $user): bool
    {
        return $user->can('product-variant.import');
    }

    /**
     * Determine whether the user can manage bulk operations.
     */
    public function bulkManage(User $user): bool
    {
        return $user->can('product-variant.bulk.manage.any') || $user->can('product-variant.bulk.manage.own');
    }

    /**
     * Determine whether the user can sync variants.
     */
    public function sync(User $user): bool
    {
        return $user->can('product-variant.sync.any') || $user->can('product-variant.sync.own');
    }

    /**
     * Determine whether the user can view variant analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('product-variant.analytics.view.any') || $user->can('product-variant.analytics.view.own');
    }

    /**
     * Determine whether the user can view variant reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('product-variant.reports.view');
    }

    /**
     * Check if the user is the owner of the variant.
     */
    private function isOwner(User $user, ProductVariant $variant): bool
    {
        // This is a simplified implementation - in a real scenario, you'd need to define ownership logic
        // For now, we'll assume ownership is based on the created_by field
        return $variant->created_by === $user->id;
    }
}
