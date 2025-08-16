<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use Fereydooni\Shopping\app\Models\Brand;

class BrandPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any brands.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('brand.view.any') || $user->can('brand.view');
    }

    /**
     * Determine whether the user can view the brand.
     */
    public function view(User $user, Brand $brand): bool
    {
        // Check if user can view any brands
        if ($user->can('brand.view.any')) {
            return true;
        }

        // Check if user can view own brands
        if ($user->can('brand.view.own')) {
            // For now, assume all brands are viewable by authenticated users
            // You can modify this logic based on your ownership requirements
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create brands.
     */
    public function create(User $user): bool
    {
        return $user->can('brand.create.any') || $user->can('brand.create');
    }

    /**
     * Determine whether the user can update the brand.
     */
    public function update(User $user, Brand $brand): bool
    {
        // Check if user can update any brands
        if ($user->can('brand.update.any')) {
            return true;
        }

        // Check if user can update own brands
        if ($user->can('brand.update.own')) {
            // For now, assume all brands are updatable by authenticated users
            // You can modify this logic based on your ownership requirements
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the brand.
     */
    public function delete(User $user, Brand $brand): bool
    {
        // Check if user can delete any brands
        if ($user->can('brand.delete.any')) {
            return true;
        }

        // Check if user can delete own brands
        if ($user->can('brand.delete.own')) {
            // For now, assume all brands are deletable by authenticated users
            // You can modify this logic based on your ownership requirements
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the brand's active status.
     */
    public function toggleActive(User $user, Brand $brand): bool
    {
        // Check if user can toggle active status for any brands
        if ($user->can('brand.toggle.active.any')) {
            return true;
        }

        // Check if user can toggle active status for own brands
        if ($user->can('brand.toggle.active.own')) {
            // For now, assume all brands are toggleable by authenticated users
            // You can modify this logic based on your ownership requirements
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can toggle the brand's featured status.
     */
    public function toggleFeatured(User $user, Brand $brand): bool
    {
        // Check if user can toggle featured status for any brands
        if ($user->can('brand.toggle.featured.any')) {
            return true;
        }

        // Check if user can toggle featured status for own brands
        if ($user->can('brand.toggle.featured.own')) {
            // For now, assume all brands are toggleable by authenticated users
            // You can modify this logic based on your ownership requirements
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can search brands.
     */
    public function search(User $user): bool
    {
        return $user->can('brand.search.any') || $user->can('brand.search');
    }

    /**
     * Determine whether the user can export brands.
     */
    public function export(User $user): bool
    {
        return $user->can('brand.export');
    }

    /**
     * Determine whether the user can import brands.
     */
    public function import(User $user): bool
    {
        return $user->can('brand.import');
    }

    /**
     * Determine whether the user can upload brand media.
     */
    public function uploadMedia(User $user, Brand $brand): bool
    {
        // Check if user can upload media for any brands
        if ($user->can('brand.media.upload')) {
            return true;
        }

        // Check if user can update the brand (implies media upload permission)
        return $this->update($user, $brand);
    }

    /**
     * Determine whether the user can delete brand media.
     */
    public function deleteMedia(User $user, Brand $brand): bool
    {
        // Check if user can delete media for any brands
        if ($user->can('brand.media.delete')) {
            return true;
        }

        // Check if user can update the brand (implies media delete permission)
        return $this->update($user, $brand);
    }

    /**
     * Determine whether the user can manage brand SEO.
     */
    public function manageSeo(User $user, Brand $brand): bool
    {
        // Check if user can manage SEO for any brands
        if ($user->can('brand.seo.manage')) {
            return true;
        }

        // Check if user can update the brand (implies SEO management permission)
        return $this->update($user, $brand);
    }

    /**
     * Determine whether the user can validate brands.
     */
    public function validate(User $user): bool
    {
        return $user->can('brand.validate');
    }
}
