<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\ProductAttributeValue;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class ProductAttributeValuePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any product attribute values.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('product-attribute-value.view.any') ||
               $user->can('product-attribute-value.view.own');
    }

    /**
     * Determine whether the user can view the product attribute value.
     */
    public function view(User $user, ProductAttributeValue $value): bool
    {
        if ($user->can('product-attribute-value.view.any')) {
            return true;
        }

        if ($user->can('product-attribute-value.view.own')) {
            return $this->isOwner($user, $value);
        }

        return false;
    }

    /**
     * Determine whether the user can create product attribute values.
     */
    public function create(User $user): bool
    {
        return $user->can('product-attribute-value.create.any') ||
               $user->can('product-attribute-value.create.own');
    }

    /**
     * Determine whether the user can update the product attribute value.
     */
    public function update(User $user, ProductAttributeValue $value): bool
    {
        if ($user->can('product-attribute-value.update.any')) {
            return true;
        }

        if ($user->can('product-attribute-value.update.own')) {
            return $this->isOwner($user, $value);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the product attribute value.
     */
    public function delete(User $user, ProductAttributeValue $value): bool
    {
        if ($user->can('product-attribute-value.delete.any')) {
            return true;
        }

        if ($user->can('product-attribute-value.delete.own')) {
            return $this->isOwner($user, $value);
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the active status of the product attribute value.
     */
    public function toggleActive(User $user, ProductAttributeValue $value): bool
    {
        if ($user->can('product-attribute-value.toggle.active.any')) {
            return true;
        }

        if ($user->can('product-attribute-value.toggle.active.own')) {
            return $this->isOwner($user, $value);
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the default status of the product attribute value.
     */
    public function toggleDefault(User $user, ProductAttributeValue $value): bool
    {
        if ($user->can('product-attribute-value.toggle.default.any')) {
            return true;
        }

        if ($user->can('product-attribute-value.toggle.default.own')) {
            return $this->isOwner($user, $value);
        }

        return false;
    }

    /**
     * Determine whether the user can set the product attribute value as default.
     */
    public function setDefault(User $user, ProductAttributeValue $value): bool
    {
        if ($user->can('product-attribute-value.toggle.default.any')) {
            return true;
        }

        if ($user->can('product-attribute-value.toggle.default.own')) {
            return $this->isOwner($user, $value);
        }

        return false;
    }

    /**
     * Determine whether the user can search product attribute values.
     */
    public function search(User $user): bool
    {
        return $user->can('product-attribute-value.search.any') ||
               $user->can('product-attribute-value.search.own');
    }

    /**
     * Determine whether the user can export product attribute values.
     */
    public function export(User $user): bool
    {
        return $user->can('product-attribute-value.export');
    }

    /**
     * Determine whether the user can import product attribute values.
     */
    public function import(User $user): bool
    {
        return $user->can('product-attribute-value.import');
    }

    /**
     * Determine whether the user can manage product attribute value relationships.
     */
    public function manageRelationships(User $user): bool
    {
        return $user->can('product-attribute-value.relationships.manage');
    }

    /**
     * Determine whether the user can view product attribute value relationships.
     */
    public function viewRelationships(User $user): bool
    {
        return $user->can('product-attribute-value.relationships.view');
    }

    /**
     * Determine whether the user can view product attribute value usage.
     */
    public function viewUsage(User $user): bool
    {
        return $user->can('product-attribute-value.usage.view');
    }

    /**
     * Determine whether the user can view product attribute value analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('product-attribute-value.analytics.view');
    }

    /**
     * Determine whether the user can view product attribute value reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('product-attribute-value.reports.view');
    }

    /**
     * Determine whether the user can generate product attribute value reports.
     */
    public function generateReports(User $user): bool
    {
        return $user->can('product-attribute-value.reports.generate');
    }

    /**
     * Check if the user is the owner of the product attribute value.
     */
    private function isOwner(User $user, ProductAttributeValue $value): bool
    {
        return $value->created_by === $user->id;
    }
}
