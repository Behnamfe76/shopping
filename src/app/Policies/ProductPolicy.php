<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use Fereydooni\Shopping\app\Models\Product;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('product.view.any');
    }

    /**
     * Determine whether the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        if ($user->can('product.view.any')) {
            return true;
        }

        if ($user->can('product.view.own')) {
            return $this->isOwner($user, $product);
        }

        return false;
    }

    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->can('product.create.any') || $user->can('product.create.own');
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        if ($user->can('product.update.any')) {
            return true;
        }

        if ($user->can('product.update.own')) {
            return $this->isOwner($user, $product);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        if ($user->can('product.delete.any')) {
            return true;
        }

        if ($user->can('product.delete.own')) {
            return $this->isOwner($user, $product);
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the product's active status.
     */
    public function toggleActive(User $user, Product $product): bool
    {
        if ($user->can('product.toggle.active.any')) {
            return true;
        }

        if ($user->can('product.toggle.active.own')) {
            return $this->isOwner($user, $product);
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the product's featured status.
     */
    public function toggleFeatured(User $user, Product $product): bool
    {
        if ($user->can('product.toggle.featured.any')) {
            return true;
        }

        if ($user->can('product.toggle.featured.own')) {
            return $this->isOwner($user, $product);
        }

        return false;
    }

    /**
     * Determine whether the user can publish the product.
     */
    public function publish(User $user, Product $product): bool
    {
        if ($user->can('product.publish.any')) {
            return true;
        }

        if ($user->can('product.publish.own')) {
            return $this->isOwner($user, $product);
        }

        return false;
    }

    /**
     * Determine whether the user can unpublish the product.
     */
    public function unpublish(User $user, Product $product): bool
    {
        if ($user->can('product.unpublish.any')) {
            return true;
        }

        if ($user->can('product.unpublish.own')) {
            return $this->isOwner($user, $product);
        }

        return false;
    }

    /**
     * Determine whether the user can archive the product.
     */
    public function archive(User $user, Product $product): bool
    {
        if ($user->can('product.archive.any')) {
            return true;
        }

        if ($user->can('product.archive.own')) {
            return $this->isOwner($user, $product);
        }

        return false;
    }

    /**
     * Determine whether the user can search products.
     */
    public function search(User $user): bool
    {
        return $user->can('product.search.any') || $user->can('product.search.own');
    }

    /**
     * Determine whether the user can export products.
     */
    public function export(User $user): bool
    {
        return $user->can('product.export');
    }

    /**
     * Determine whether the user can import products.
     */
    public function import(User $user): bool
    {
        return $user->can('product.import');
    }

    /**
     * Determine whether the user can upload product media.
     */
    public function uploadMedia(User $user, Product $product): bool
    {
        if ($user->can('product.media.upload')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete product media.
     */
    public function deleteMedia(User $user, Product $product): bool
    {
        if ($user->can('product.media.delete')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage product inventory.
     */
    public function manageInventory(User $user, Product $product): bool
    {
        if ($user->can('product.inventory.manage')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view product inventory.
     */
    public function viewInventory(User $user, Product $product): bool
    {
        if ($user->can('product.inventory.view')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage product SEO.
     */
    public function manageSeo(User $user, Product $product): bool
    {
        if ($user->can('product.seo.manage')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view product analytics.
     */
    public function viewAnalytics(User $user, Product $product): bool
    {
        if ($user->can('product.analytics.view')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view product reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('product.reports.view');
    }

    /**
     * Determine whether the user can perform bulk operations.
     */
    public function bulkOperations(User $user): bool
    {
        return $user->can('product.bulk.operations');
    }

    /**
     * Determine whether the user can duplicate the product.
     */
    public function duplicate(User $user, Product $product): bool
    {
        if ($user->can('product.duplicate')) {
            return true;
        }

        return false;
    }

    /**
     * Check if the user is the owner of the product.
     */
    protected function isOwner(User $user, Product $product): bool
    {
        // This assumes there's a user_id field on the product
        // Adjust this logic based on your actual ownership model
        return $product->user_id === $user->id;
    }
}
