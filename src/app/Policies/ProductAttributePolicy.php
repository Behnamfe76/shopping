<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use Fereydooni\Shopping\app\Models\ProductAttribute;

class ProductAttributePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any product attributes.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('product-attribute.view.any');
    }

    /**
     * Determine whether the user can view the product attribute.
     */
    public function view(User $user, ProductAttribute $attribute): bool
    {
        // Check if user can view any attributes
        if ($user->can('product-attribute.view.any')) {
            return true;
        }

        // Check if user can view own attributes
        if ($user->can('product-attribute.view.own')) {
            return $attribute->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create product attributes.
     */
    public function create(User $user): bool
    {
        return $user->can('product-attribute.create.any') || $user->can('product-attribute.create.own');
    }

    /**
     * Determine whether the user can update the product attribute.
     */
    public function update(User $user, ProductAttribute $attribute): bool
    {
        // Check if user can update any attributes
        if ($user->can('product-attribute.update.any')) {
            return true;
        }

        // Check if user can update own attributes
        if ($user->can('product-attribute.update.own')) {
            return $attribute->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the product attribute.
     */
    public function delete(User $user, ProductAttribute $attribute): bool
    {
        // Prevent deletion of system attributes
        if ($attribute->is_system) {
            return false;
        }

        // Check if user can delete any attributes
        if ($user->can('product-attribute.delete.any')) {
            return true;
        }

        // Check if user can delete own attributes
        if ($user->can('product-attribute.delete.own')) {
            return $attribute->created_by === $user->id;
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
     * Determine whether the user can toggle the active status of the product attribute.
     */
    public function toggleActive(User $user, ProductAttribute $attribute): bool
    {
        // Check if user can toggle active status for any attributes
        if ($user->can('product-attribute.toggle.active.any')) {
            return true;
        }

        // Check if user can toggle active status for own attributes
        if ($user->can('product-attribute.toggle.active.own')) {
            return $attribute->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the required status of the product attribute.
     */
    public function toggleRequired(User $user, ProductAttribute $attribute): bool
    {
        // Check if user can toggle required status for any attributes
        if ($user->can('product-attribute.toggle.required.any')) {
            return true;
        }

        // Check if user can toggle required status for own attributes
        if ($user->can('product-attribute.toggle.required.own')) {
            return $attribute->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the searchable status of the product attribute.
     */
    public function toggleSearchable(User $user, ProductAttribute $attribute): bool
    {
        // Check if user can toggle searchable status for any attributes
        if ($user->can('product-attribute.toggle.searchable.any')) {
            return true;
        }

        // Check if user can toggle searchable status for own attributes
        if ($user->can('product-attribute.toggle.searchable.own')) {
            return $attribute->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the filterable status of the product attribute.
     */
    public function toggleFilterable(User $user, ProductAttribute $attribute): bool
    {
        // Check if user can toggle filterable status for any attributes
        if ($user->can('product-attribute.toggle.filterable.any')) {
            return true;
        }

        // Check if user can toggle filterable status for own attributes
        if ($user->can('product-attribute.toggle.filterable.own')) {
            return $attribute->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the comparable status of the product attribute.
     */
    public function toggleComparable(User $user, ProductAttribute $attribute): bool
    {
        // Check if user can toggle comparable status for any attributes
        if ($user->can('product-attribute.toggle.comparable.any')) {
            return true;
        }

        // Check if user can toggle comparable status for own attributes
        if ($user->can('product-attribute.toggle.comparable.own')) {
            return $attribute->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the visible status of the product attribute.
     */
    public function toggleVisible(User $user, ProductAttribute $attribute): bool
    {
        // Check if user can toggle visible status for any attributes
        if ($user->can('product-attribute.toggle.visible.any')) {
            return true;
        }

        // Check if user can toggle visible status for own attributes
        if ($user->can('product-attribute.toggle.visible.own')) {
            return $attribute->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can search product attributes.
     */
    public function search(User $user): bool
    {
        return $user->can('product-attribute.search.any') || $user->can('product-attribute.search.own');
    }

    /**
     * Determine whether the user can export product attributes.
     */
    public function export(User $user): bool
    {
        return $user->can('product-attribute.export');
    }

    /**
     * Determine whether the user can import product attributes.
     */
    public function import(User $user): bool
    {
        return $user->can('product-attribute.import');
    }

    /**
     * Determine whether the user can manage attribute values.
     */
    public function manageValues(User $user, ProductAttribute $attribute): bool
    {
        // Check if user can manage values for any attributes
        if ($user->can('product-attribute.values.manage')) {
            return true;
        }

        // Check if user can manage values for own attributes
        if ($user->can('product-attribute.values.manage')) {
            return $attribute->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view attribute values.
     */
    public function viewValues(User $user, ProductAttribute $attribute): bool
    {
        // Check if user can view values for any attributes
        if ($user->can('product-attribute.values.view')) {
            return true;
        }

        // Check if user can view values for own attributes
        if ($user->can('product-attribute.values.view')) {
            return $attribute->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view attribute analytics.
     */
    public function viewAnalytics(User $user, ProductAttribute $attribute): bool
    {
        return $user->can('product-attribute.analytics.view');
    }

    /**
     * Determine whether the user can view attribute reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('product-attribute.reports.view');
    }

    /**
     * Determine whether the user can generate attribute reports.
     */
    public function generateReports(User $user): bool
    {
        return $user->can('product-attribute.reports.generate');
    }
}
