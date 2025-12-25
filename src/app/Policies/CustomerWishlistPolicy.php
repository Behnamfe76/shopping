<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class CustomerWishlistPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any customer wishlists.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customer-wishlists.viewAny');
    }

    /**
     * Determine whether the user can view the customer wishlist.
     */
    public function view(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can view their own wishlist items
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Users can view public wishlist items
        if ($wishlist->is_public) {
            return true;
        }

        // Admin users can view any wishlist item
        return $user->can('customer-wishlists.view');
    }

    /**
     * Determine whether the user can create customer wishlists.
     */
    public function create(User $user): bool
    {
        return $user->can('customer-wishlists.create');
    }

    /**
     * Determine whether the user can update the customer wishlist.
     */
    public function update(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can update their own wishlist items
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Admin users can update any wishlist item
        return $user->can('customer-wishlists.update');
    }

    /**
     * Determine whether the user can delete the customer wishlist.
     */
    public function delete(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can delete their own wishlist items
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Admin users can delete any wishlist item
        return $user->can('customer-wishlists.delete');
    }

    /**
     * Determine whether the user can restore the customer wishlist.
     */
    public function restore(User $user, CustomerWishlist $wishlist): bool
    {
        return $user->can('customer-wishlists.restore');
    }

    /**
     * Determine whether the user can permanently delete the customer wishlist.
     */
    public function forceDelete(User $user, CustomerWishlist $wishlist): bool
    {
        return $user->can('customer-wishlists.forceDelete');
    }

    /**
     * Determine whether the user can add products to wishlist.
     */
    public function addToWishlist(User $user, Customer $customer): bool
    {
        // Users can add to their own wishlist
        if ($user->id === $customer->user_id) {
            return true;
        }

        // Admin users can add to any customer's wishlist
        return $user->can('customer-wishlists.addToWishlist');
    }

    /**
     * Determine whether the user can remove products from wishlist.
     */
    public function removeFromWishlist(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can remove from their own wishlist
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Admin users can remove from any wishlist
        return $user->can('customer-wishlists.removeFromWishlist');
    }

    /**
     * Determine whether the user can make wishlist public.
     */
    public function makePublic(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can make their own wishlist items public
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Admin users can make any wishlist item public
        return $user->can('customer-wishlists.makePublic');
    }

    /**
     * Determine whether the user can make wishlist private.
     */
    public function makePrivate(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can make their own wishlist items private
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Admin users can make any wishlist item private
        return $user->can('customer-wishlists.makePrivate');
    }

    /**
     * Determine whether the user can set wishlist priority.
     */
    public function setPriority(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can set priority for their own wishlist items
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Admin users can set priority for any wishlist item
        return $user->can('customer-wishlists.setPriority');
    }

    /**
     * Determine whether the user can mark wishlist as notified.
     */
    public function markNotified(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can mark their own wishlist items as notified
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Admin users can mark any wishlist item as notified
        return $user->can('customer-wishlists.markNotified');
    }

    /**
     * Determine whether the user can update wishlist price.
     */
    public function updatePrice(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can update price for their own wishlist items
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Admin users can update price for any wishlist item
        return $user->can('customer-wishlists.updatePrice');
    }

    /**
     * Determine whether the user can check price drops.
     */
    public function checkPriceDrop(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can check price drops for their own wishlist items
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Admin users can check price drops for any wishlist item
        return $user->can('customer-wishlists.checkPriceDrop');
    }

    /**
     * Determine whether the user can clear wishlist.
     */
    public function clearWishlist(User $user, Customer $customer): bool
    {
        // Users can clear their own wishlist
        if ($user->id === $customer->user_id) {
            return true;
        }

        // Admin users can clear any customer's wishlist
        return $user->can('customer-wishlists.clearWishlist');
    }

    /**
     * Determine whether the user can export wishlist.
     */
    public function exportWishlist(User $user, Customer $customer): bool
    {
        // Users can export their own wishlist
        if ($user->id === $customer->user_id) {
            return true;
        }

        // Admin users can export any customer's wishlist
        return $user->can('customer-wishlists.exportWishlist');
    }

    /**
     * Determine whether the user can import wishlist.
     */
    public function importWishlist(User $user, Customer $customer): bool
    {
        // Users can import to their own wishlist
        if ($user->id === $customer->user_id) {
            return true;
        }

        // Admin users can import to any customer's wishlist
        return $user->can('customer-wishlists.importWishlist');
    }

    /**
     * Determine whether the user can duplicate wishlist.
     */
    public function duplicateWishlist(User $user, Customer $sourceCustomer, Customer $targetCustomer): bool
    {
        // Users can duplicate their own wishlist
        if ($user->id === $sourceCustomer->user_id && $user->id === $targetCustomer->user_id) {
            return true;
        }

        // Admin users can duplicate any wishlist
        return $user->can('customer-wishlists.duplicateWishlist');
    }

    /**
     * Determine whether the user can view wishlist analytics.
     */
    public function viewAnalytics(User $user, Customer $customer): bool
    {
        // Users can view their own wishlist analytics
        if ($user->id === $customer->user_id) {
            return true;
        }

        // Admin users can view any customer's wishlist analytics
        return $user->can('customer-wishlists.viewAnalytics');
    }

    /**
     * Determine whether the user can view wishlist statistics.
     */
    public function viewStats(User $user): bool
    {
        return $user->can('customer-wishlists.viewStats');
    }

    /**
     * Determine whether the user can manage wishlist notifications.
     */
    public function manageNotifications(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can manage notifications for their own wishlist items
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Admin users can manage notifications for any wishlist item
        return $user->can('customer-wishlists.manageNotifications');
    }

    /**
     * Determine whether the user can share wishlist.
     */
    public function shareWishlist(User $user, Customer $sourceCustomer, Customer $targetCustomer): bool
    {
        // Users can share their own wishlist
        if ($user->id === $sourceCustomer->user_id) {
            return true;
        }

        // Admin users can share any wishlist
        return $user->can('customer-wishlists.shareWishlist');
    }

    /**
     * Determine whether the user can compare wishlists.
     */
    public function compareWishlists(User $user, Customer $customer1, Customer $customer2): bool
    {
        // Users can compare their own wishlist with others
        if ($user->id === $customer1->user_id || $user->id === $customer2->user_id) {
            return true;
        }

        // Admin users can compare any wishlists
        return $user->can('customer-wishlists.compareWishlists');
    }

    /**
     * Determine whether the user can view wishlist recommendations.
     */
    public function viewRecommendations(User $user, Customer $customer): bool
    {
        // Users can view their own wishlist recommendations
        if ($user->id === $customer->user_id) {
            return true;
        }

        // Admin users can view any customer's wishlist recommendations
        return $user->can('customer-wishlists.viewRecommendations');
    }

    /**
     * Determine whether the user can manage wishlist privacy settings.
     */
    public function managePrivacy(User $user, CustomerWishlist $wishlist): bool
    {
        // Users can manage privacy for their own wishlist items
        if ($user->id === $wishlist->customer->user_id) {
            return true;
        }

        // Admin users can manage privacy for any wishlist item
        return $user->can('customer-wishlists.managePrivacy');
    }

    /**
     * Determine whether the user can bulk manage wishlists.
     */
    public function bulkManage(User $user, Customer $customer): bool
    {
        // Users can bulk manage their own wishlist
        if ($user->id === $customer->user_id) {
            return true;
        }

        // Admin users can bulk manage any customer's wishlist
        return $user->can('customer-wishlists.bulkManage');
    }
}
