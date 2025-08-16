<?php

namespace Fereydooni\Shopping\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;
use Fereydooni\Shopping\app\Models\ProductDiscount;

class ProductDiscountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any product discounts.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('product-discount.view.any') || $user->can('product-discount.view');
    }

    /**
     * Determine whether the user can view the product discount.
     */
    public function view(User $user, ProductDiscount $discount): bool
    {
        if ($user->can('product-discount.view.any')) {
            return true;
        }

        if ($user->can('product-discount.view.own')) {
            return $discount->created_by === $user->id;
        }

        return $user->can('product-discount.view');
    }

    /**
     * Determine whether the user can create product discounts.
     */
    public function create(User $user): bool
    {
        return $user->can('product-discount.create.any') || $user->can('product-discount.create');
    }

    /**
     * Determine whether the user can update the product discount.
     */
    public function update(User $user, ProductDiscount $discount): bool
    {
        if ($user->can('product-discount.update.any')) {
            return true;
        }

        if ($user->can('product-discount.update.own')) {
            return $discount->created_by === $user->id;
        }

        return $user->can('product-discount.update');
    }

    /**
     * Determine whether the user can delete the product discount.
     */
    public function delete(User $user, ProductDiscount $discount): bool
    {
        if ($user->can('product-discount.delete.any')) {
            return true;
        }

        if ($user->can('product-discount.delete.own')) {
            return $discount->created_by === $user->id;
        }

        return $user->can('product-discount.delete');
    }

    /**
     * Determine whether the user can toggle the product discount active status.
     */
    public function toggleActive(User $user, ProductDiscount $discount): bool
    {
        if ($user->can('product-discount.toggle.active.any')) {
            return true;
        }

        if ($user->can('product-discount.toggle.active.own')) {
            return $discount->created_by === $user->id;
        }

        return $user->can('product-discount.toggle.active');
    }

    /**
     * Determine whether the user can extend the product discount.
     */
    public function extend(User $user, ProductDiscount $discount): bool
    {
        if ($user->can('product-discount.extend.any')) {
            return true;
        }

        if ($user->can('product-discount.extend.own')) {
            return $discount->created_by === $user->id;
        }

        return $user->can('product-discount.extend');
    }

    /**
     * Determine whether the user can shorten the product discount.
     */
    public function shorten(User $user, ProductDiscount $discount): bool
    {
        if ($user->can('product-discount.shorten.any')) {
            return true;
        }

        if ($user->can('product-discount.shorten.own')) {
            return $discount->created_by === $user->id;
        }

        return $user->can('product-discount.shorten');
    }

    /**
     * Determine whether the user can calculate the product discount.
     */
    public function calculate(User $user, ProductDiscount $discount): bool
    {
        if ($user->can('product-discount.calculate.any')) {
            return true;
        }

        if ($user->can('product-discount.calculate.own')) {
            return $discount->created_by === $user->id;
        }

        return $user->can('product-discount.calculate');
    }

    /**
     * Determine whether the user can apply the product discount.
     */
    public function apply(User $user, ProductDiscount $discount): bool
    {
        if ($user->can('product-discount.apply.any')) {
            return true;
        }

        if ($user->can('product-discount.apply.own')) {
            return $discount->created_by === $user->id;
        }

        return $user->can('product-discount.apply');
    }

    /**
     * Determine whether the user can validate the product discount.
     */
    public function validate(User $user, ProductDiscount $discount): bool
    {
        if ($user->can('product-discount.validate.any')) {
            return true;
        }

        if ($user->can('product-discount.validate.own')) {
            return $discount->created_by === $user->id;
        }

        return $user->can('product-discount.validate');
    }

    /**
     * Determine whether the user can search product discounts.
     */
    public function search(User $user): bool
    {
        if ($user->can('product-discount.search.any')) {
            return true;
        }

        if ($user->can('product-discount.search.own')) {
            return true;
        }

        return $user->can('product-discount.search');
    }

    /**
     * Determine whether the user can export product discounts.
     */
    public function export(User $user): bool
    {
        return $user->can('product-discount.export');
    }

    /**
     * Determine whether the user can import product discounts.
     */
    public function import(User $user): bool
    {
        return $user->can('product-discount.import');
    }

    /**
     * Determine whether the user can view product discount analytics.
     */
    public function viewAnalytics(User $user, ProductDiscount $discount): bool
    {
        if ($user->can('product-discount.analytics.view.any')) {
            return true;
        }

        if ($user->can('product-discount.analytics.view.own')) {
            return $discount->created_by === $user->id;
        }

        return $user->can('product-discount.analytics.view');
    }

    /**
     * Determine whether the user can view product discount reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('product-discount.reports.view');
    }

    /**
     * Determine whether the user can view product discount forecast.
     */
    public function viewForecast(User $user): bool
    {
        return $user->can('product-discount.forecast.view');
    }

    /**
     * Determine whether the user can view product discount recommendations.
     */
    public function viewRecommendations(User $user): bool
    {
        return $user->can('product-discount.recommendations.view');
    }
}
