<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Enums\AddressType;
use Fereydooni\Shopping\app\Models\Address;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any addresses.
     */
    public function viewAny($user): bool
    {
        return $user->can('address.view.any') || $user->can('address.view.own');
    }

    /**
     * Determine whether the user can view the address.
     */
    public function view($user, Address $address): bool
    {
        // Check if user can view any addresses
        if ($user->can('address.view.any')) {
            return true;
        }

        // Check if user can view own addresses and owns this address
        if ($user->can('address.view.own') && $address->user_id === $user->id) {
            return true;
        }

        // Check type-specific permissions
        if ($address->type === AddressType::BILLING && $user->can('address.view.billing')) {
            return $user->can('address.view.any') || $address->user_id === $user->id;
        }

        if ($address->type === AddressType::SHIPPING && $user->can('address.view.shipping')) {
            return $user->can('address.view.any') || $address->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create addresses.
     */
    public function create($user): bool
    {
        return $user->can('address.create.any') || $user->can('address.create.own');
    }

    /**
     * Determine whether the user can update the address.
     */
    public function update($user, Address $address): bool
    {
        // Check if user can update any addresses
        if ($user->can('address.update.any')) {
            return true;
        }

        // Check if user can update own addresses and owns this address
        if ($user->can('address.update.own') && $address->user_id === $user->id) {
            return true;
        }

        // Check type-specific permissions
        if ($address->type === AddressType::BILLING && $user->can('address.update.billing')) {
            return $user->can('address.update.any') || $address->user_id === $user->id;
        }

        if ($address->type === AddressType::SHIPPING && $user->can('address.update.shipping')) {
            return $user->can('address.update.any') || $address->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the address.
     */
    public function delete($user, Address $address): bool
    {
        // Check if user can delete any addresses
        if ($user->can('address.delete.any')) {
            return true;
        }

        // Check if user can delete own addresses and owns this address
        if ($user->can('address.delete.own') && $address->user_id === $user->id) {
            return true;
        }

        // Check type-specific permissions
        if ($address->type === AddressType::BILLING && $user->can('address.delete.billing')) {
            return $user->can('address.delete.any') || $address->user_id === $user->id;
        }

        if ($address->type === AddressType::SHIPPING && $user->can('address.delete.shipping')) {
            return $user->can('address.delete.any') || $address->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can set the address as default.
     */
    public function setDefault($user, Address $address): bool
    {
        // Check if user can set any address as default
        if ($user->can('address.set.default.any')) {
            return true;
        }

        // Check if user can set own address as default and owns this address
        if ($user->can('address.set.default.own') && $address->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can unset the address as default.
     */
    public function unsetDefault($user, Address $address): bool
    {
        // Check if user can unset any address as default
        if ($user->can('address.unset.default.any')) {
            return true;
        }

        // Check if user can unset own address as default and owns this address
        if ($user->can('address.unset.default.own') && $address->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can search addresses.
     */
    public function search($user): bool
    {
        return $user->can('address.search.any') || $user->can('address.search.own');
    }

    /**
     * Determine whether the user can export addresses.
     */
    public function export($user): bool
    {
        return $user->can('address.export');
    }

    /**
     * Determine whether the user can import addresses.
     */
    public function import($user): bool
    {
        return $user->can('address.import');
    }

    /**
     * Determine whether the user can validate addresses.
     */
    public function validate($user, ?Address $address = null): bool
    {
        // Check if user can validate any addresses
        if ($user->can('address.validate.any')) {
            return true;
        }

        // Check if user can validate own addresses
        if ($user->can('address.validate.own')) {
            // If no specific address provided, allow validation
            if (! $address) {
                return true;
            }

            // Check if user owns the address
            return $address->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view address statistics.
     */
    public function viewStats($user): bool
    {
        return $user->can('address.view.stats.any') || $user->can('address.view.stats.own');
    }

    /**
     * Determine whether the user can perform bulk operations.
     */
    public function bulkDelete($user): bool
    {
        return $user->can('address.bulk.delete');
    }

    public function bulkUpdate($user): bool
    {
        return $user->can('address.bulk.update');
    }

    public function bulkSetDefault($user): bool
    {
        return $user->can('address.bulk.set.default');
    }

    /**
     * Determine whether the user can manage addresses of a specific type.
     */
    public function manageBillingAddresses($user): bool
    {
        return $user->can('address.view.billing') ||
               $user->can('address.create.billing') ||
               $user->can('address.update.billing') ||
               $user->can('address.delete.billing');
    }

    public function manageShippingAddresses($user): bool
    {
        return $user->can('address.view.shipping') ||
               $user->can('address.create.shipping') ||
               $user->can('address.update.shipping') ||
               $user->can('address.delete.shipping');
    }

    /**
     * Determine whether the user can access address by type.
     */
    public function accessByType($user, AddressType $type): bool
    {
        switch ($type) {
            case AddressType::BILLING:
                return $user->can('address.view.billing') ||
                       $user->can('address.create.billing') ||
                       $user->can('address.update.billing') ||
                       $user->can('address.delete.billing');

            case AddressType::SHIPPING:
                return $user->can('address.view.shipping') ||
                       $user->can('address.create.shipping') ||
                       $user->can('address.update.shipping') ||
                       $user->can('address.delete.shipping');

            default:
                return false;
        }
    }
}
