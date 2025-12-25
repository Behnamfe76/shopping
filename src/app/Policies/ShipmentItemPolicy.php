<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\ShipmentItem;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class ShipmentItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any shipment items.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('shipment-item.view.any') || $user->can('shipment-item.view');
    }

    /**
     * Determine whether the user can view the shipment item.
     */
    public function view(User $user, ShipmentItem $shipmentItem): bool
    {
        // Check if user can view any shipment items
        if ($user->can('shipment-item.view.any')) {
            return true;
        }

        // Check if user can view own shipment items
        if ($user->can('shipment-item.view.own')) {
            return $this->isShipmentOwnedByUser($user, $shipmentItem);
        }

        return $user->can('shipment-item.view');
    }

    /**
     * Determine whether the user can create shipment items.
     */
    public function create(User $user): bool
    {
        return $user->can('shipment-item.create.any') || $user->can('shipment-item.create');
    }

    /**
     * Determine whether the user can update the shipment item.
     */
    public function update(User $user, ShipmentItem $shipmentItem): bool
    {
        // Check if user can update any shipment items
        if ($user->can('shipment-item.update.any')) {
            return true;
        }

        // Check if user can update own shipment items
        if ($user->can('shipment-item.update.own')) {
            return $this->isShipmentOwnedByUser($user, $shipmentItem);
        }

        return $user->can('shipment-item.update');
    }

    /**
     * Determine whether the user can delete the shipment item.
     */
    public function delete(User $user, ShipmentItem $shipmentItem): bool
    {
        // Check if user can delete any shipment items
        if ($user->can('shipment-item.delete.any')) {
            return true;
        }

        // Check if user can delete own shipment items
        if ($user->can('shipment-item.delete.own')) {
            return $this->isShipmentOwnedByUser($user, $shipmentItem);
        }

        return $user->can('shipment-item.delete');
    }

    /**
     * Determine whether the user can manage shipment item quantity.
     */
    public function manageQuantity(User $user, ShipmentItem $shipmentItem): bool
    {
        // Check if user can manage quantity for any shipment items
        if ($user->can('shipment-item.manage.quantity.any')) {
            return true;
        }

        // Check if user can manage quantity for own shipment items
        if ($user->can('shipment-item.manage.quantity.own')) {
            return $this->isShipmentOwnedByUser($user, $shipmentItem);
        }

        return $user->can('shipment-item.manage.quantity');
    }

    /**
     * Determine whether the user can search shipment items.
     */
    public function search(User $user): bool
    {
        return $user->can('shipment-item.search.any') || $user->can('shipment-item.search');
    }

    /**
     * Determine whether the user can export shipment items.
     */
    public function export(User $user): bool
    {
        return $user->can('shipment-item.export');
    }

    /**
     * Determine whether the user can import shipment items.
     */
    public function import(User $user): bool
    {
        return $user->can('shipment-item.import');
    }

    /**
     * Determine whether the user can validate shipment items.
     */
    public function validate(User $user): bool
    {
        return $user->can('shipment-item.validate');
    }

    /**
     * Determine whether the user can calculate shipment weight.
     */
    public function calculateWeight(User $user): bool
    {
        return $user->can('shipment-item.calculate.weight');
    }

    /**
     * Determine whether the user can calculate shipment volume.
     */
    public function calculateVolume(User $user): bool
    {
        return $user->can('shipment-item.calculate.volume');
    }

    /**
     * Determine whether the user can calculate shipment metrics.
     */
    public function calculate(User $user): bool
    {
        return $user->can('shipment-item.calculate.weight') || $user->can('shipment-item.calculate.volume');
    }

    /**
     * Determine whether the user can view shipment item analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('shipment-item.view.any') || $user->can('shipment-item.view');
    }

    /**
     * Determine whether the user can perform bulk operations on shipment items.
     */
    public function bulkOperations(User $user): bool
    {
        return $user->can('shipment-item.update.any') || $user->can('shipment-item.delete.any');
    }

    /**
     * Determine whether the user can view shipment item history.
     */
    public function viewHistory(User $user, ShipmentItem $shipmentItem): bool
    {
        return $this->view($user, $shipmentItem);
    }

    /**
     * Determine whether the user can restore the shipment item.
     */
    public function restore(User $user, ShipmentItem $shipmentItem): bool
    {
        return $this->update($user, $shipmentItem);
    }

    /**
     * Determine whether the user can permanently delete the shipment item.
     */
    public function forceDelete(User $user, ShipmentItem $shipmentItem): bool
    {
        return $this->delete($user, $shipmentItem);
    }

    /**
     * Check if the shipment is owned by the user.
     */
    protected function isShipmentOwnedByUser(User $user, ShipmentItem $shipmentItem): bool
    {
        // Load the shipment relationship if not already loaded
        if (! $shipmentItem->relationLoaded('shipment')) {
            $shipmentItem->load('shipment');
        }

        // Check if the shipment belongs to the user
        if ($shipmentItem->shipment && method_exists($shipmentItem->shipment, 'user_id')) {
            return $shipmentItem->shipment->user_id === $user->id;
        }

        // Check if the shipment belongs to an order owned by the user
        if ($shipmentItem->shipment && $shipmentItem->shipment->order) {
            return $shipmentItem->shipment->order->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view shipment items for a specific shipment.
     */
    public function viewForShipment(User $user, $shipmentId): bool
    {
        if ($user->can('shipment-item.view.any')) {
            return true;
        }

        if ($user->can('shipment-item.view.own')) {
            // This would need to be implemented based on your shipment ownership logic
            // For now, we'll assume the user can view if they have the permission
            return true;
        }

        return $user->can('shipment-item.view');
    }

    /**
     * Determine whether the user can create shipment items for a specific shipment.
     */
    public function createForShipment(User $user, $shipmentId): bool
    {
        if ($user->can('shipment-item.create.any')) {
            return true;
        }

        if ($user->can('shipment-item.create.own')) {
            // This would need to be implemented based on your shipment ownership logic
            // For now, we'll assume the user can create if they have the permission
            return true;
        }

        return $user->can('shipment-item.create');
    }
}
