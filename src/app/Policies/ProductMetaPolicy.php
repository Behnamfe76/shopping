<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\ProductMeta;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class ProductMetaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any product meta.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('product-meta.view.any');
    }

    /**
     * Determine whether the user can view the product meta.
     */
    public function view(User $user, ProductMeta $productMeta): bool
    {
        if ($user->can('product-meta.view.any')) {
            return true;
        }

        if ($user->can('product-meta.view.own')) {
            return $this->isOwner($user, $productMeta);
        }

        if ($user->can('product-meta.view.public')) {
            return $productMeta->isPublic();
        }

        return false;
    }

    /**
     * Determine whether the user can view public product meta.
     */
    public function viewPublic(User $user): bool
    {
        return $user->can('product-meta.view.public');
    }

    /**
     * Determine whether the user can view private product meta.
     */
    public function viewPrivate(User $user): bool
    {
        return $user->can('product-meta.view.any') || $user->can('product-meta.view.own');
    }

    /**
     * Determine whether the user can create product meta.
     */
    public function create(User $user): bool
    {
        return $user->can('product-meta.create.any') || $user->can('product-meta.create.own');
    }

    /**
     * Determine whether the user can update the product meta.
     */
    public function update(User $user, ProductMeta $productMeta): bool
    {
        if ($user->can('product-meta.update.any')) {
            return true;
        }

        if ($user->can('product-meta.update.own')) {
            return $this->isOwner($user, $productMeta);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the product meta.
     */
    public function delete(User $user, ProductMeta $productMeta): bool
    {
        if ($user->can('product-meta.delete.any')) {
            return true;
        }

        if ($user->can('product-meta.delete.own')) {
            return $this->isOwner($user, $productMeta);
        }

        return false;
    }

    /**
     * Determine whether the user can toggle public status.
     */
    public function togglePublic(User $user, ProductMeta $productMeta): bool
    {
        if ($user->can('product-meta.toggle.public.any')) {
            return true;
        }

        if ($user->can('product-meta.toggle.public.own')) {
            return $this->isOwner($user, $productMeta);
        }

        return false;
    }

    /**
     * Determine whether the user can toggle searchable status.
     */
    public function toggleSearchable(User $user, ProductMeta $productMeta): bool
    {
        if ($user->can('product-meta.toggle.searchable.any')) {
            return true;
        }

        if ($user->can('product-meta.toggle.searchable.own')) {
            return $this->isOwner($user, $productMeta);
        }

        return false;
    }

    /**
     * Determine whether the user can toggle filterable status.
     */
    public function toggleFilterable(User $user, ProductMeta $productMeta): bool
    {
        if ($user->can('product-meta.toggle.filterable.any')) {
            return true;
        }

        if ($user->can('product-meta.toggle.filterable.own')) {
            return $this->isOwner($user, $productMeta);
        }

        return false;
    }

    /**
     * Determine whether the user can search product meta.
     */
    public function search(User $user): bool
    {
        return $user->can('product-meta.search.any') || $user->can('product-meta.search.own');
    }

    /**
     * Determine whether the user can export product meta.
     */
    public function export(User $user): bool
    {
        return $user->can('product-meta.export');
    }

    /**
     * Determine whether the user can import product meta.
     */
    public function import(User $user): bool
    {
        return $user->can('product-meta.import');
    }

    /**
     * Determine whether the user can manage bulk operations.
     */
    public function bulkManage(User $user): bool
    {
        return $user->can('product-meta.bulk.manage.any') || $user->can('product-meta.bulk.manage.own');
    }

    /**
     * Determine whether the user can sync meta.
     */
    public function sync(User $user): bool
    {
        return $user->can('product-meta.sync.any') || $user->can('product-meta.sync.own');
    }

    /**
     * Determine whether the user can view meta analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('product-meta.analytics.view.any') || $user->can('product-meta.analytics.view.own');
    }

    /**
     * Determine whether the user can view meta reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('product-meta.reports.view');
    }

    /**
     * Check if the user is the owner of the product meta.
     */
    private function isOwner(User $user, ProductMeta $productMeta): bool
    {
        return $productMeta->created_by === $user->id;
    }
}
