<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\OrderItem;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class OrderItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any order items.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('order-item.view.any');
    }

    /**
     * Determine whether the user can view the order item.
     */
    public function view(User $user, OrderItem $orderItem): bool
    {
        if ($user->can('order-item.view.any')) {
            return true;
        }

        if ($user->can('order-item.view.own')) {
            // Check if user owns the order that contains this item
            return $orderItem->order->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create order items.
     */
    public function create(User $user): bool
    {
        return $user->can('order-item.create.any');
    }

    /**
     * Determine whether the user can update the order item.
     */
    public function update(User $user, OrderItem $orderItem): bool
    {
        if ($user->can('order-item.update.any')) {
            return true;
        }

        if ($user->can('order-item.update.own')) {
            // Check if user owns the order that contains this item
            return $orderItem->order->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the order item.
     */
    public function delete(User $user, OrderItem $orderItem): bool
    {
        if ($user->can('order-item.delete.any')) {
            return true;
        }

        if ($user->can('order-item.delete.own')) {
            // Check if user owns the order that contains this item
            return $orderItem->order->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can mark the order item as shipped.
     */
    public function markShipped(User $user, OrderItem $orderItem): bool
    {
        if ($user->can('order-item.mark.shipped.any')) {
            return true;
        }

        if ($user->can('order-item.mark.shipped.own')) {
            // Check if user owns the order that contains this item
            return $orderItem->order->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can mark the order item as returned.
     */
    public function markReturned(User $user, OrderItem $orderItem): bool
    {
        if ($user->can('order-item.mark.returned.any')) {
            return true;
        }

        if ($user->can('order-item.mark.returned.own')) {
            // Check if user owns the order that contains this item
            return $orderItem->order->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can process refund for the order item.
     */
    public function processRefund(User $user, OrderItem $orderItem): bool
    {
        if ($user->can('order-item.process.refund.any')) {
            return true;
        }

        if ($user->can('order-item.process.refund.own')) {
            // Check if user owns the order that contains this item
            return $orderItem->order->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can search order items.
     */
    public function search(User $user): bool
    {
        if ($user->can('order-item.search.any')) {
            return true;
        }

        if ($user->can('order-item.search.own')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can export order items.
     */
    public function export(User $user): bool
    {
        return $user->can('order-item.export');
    }

    /**
     * Determine whether the user can import order items.
     */
    public function import(User $user): bool
    {
        return $user->can('order-item.import');
    }

    /**
     * Determine whether the user can validate order items.
     */
    public function validate(User $user): bool
    {
        return $user->can('order-item.validate');
    }

    /**
     * Determine whether the user can manage inventory.
     */
    public function manageInventory(User $user): bool
    {
        return $user->can('order-item.inventory.manage');
    }

    /**
     * Determine whether the user can view inventory.
     */
    public function viewInventory(User $user): bool
    {
        return $user->can('order-item.inventory.view');
    }

    /**
     * Determine whether the user can view reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('order-item.reports.view');
    }

    /**
     * Determine whether the user can generate reports.
     */
    public function generateReports(User $user): bool
    {
        return $user->can('order-item.reports.generate');
    }

    /**
     * Determine whether the user can view shipped items.
     */
    public function viewShipped(User $user): bool
    {
        return $user->can('order-item.view.any') || $user->can('order-item.view.own');
    }

    /**
     * Determine whether the user can view unshipped items.
     */
    public function viewUnshipped(User $user): bool
    {
        return $user->can('order-item.view.any') || $user->can('order-item.view.own');
    }

    /**
     * Determine whether the user can view top selling items.
     */
    public function viewTopSelling(User $user): bool
    {
        return $user->can('order-item.reports.view');
    }

    /**
     * Determine whether the user can view low stock items.
     */
    public function viewLowStock(User $user): bool
    {
        return $user->can('order-item.inventory.view');
    }

    /**
     * Determine whether the user can view items by order.
     */
    public function viewByOrder(User $user): bool
    {
        return $user->can('order-item.view.any') || $user->can('order-item.view.own');
    }

    /**
     * Determine whether the user can view items by product.
     */
    public function viewByProduct(User $user): bool
    {
        return $user->can('order-item.view.any') || $user->can('order-item.view.own');
    }

    /**
     * Determine whether the user can view order item count.
     */
    public function viewCount(User $user): bool
    {
        return $user->can('order-item.view.any') || $user->can('order-item.view.own');
    }

    /**
     * Determine whether the user can view revenue.
     */
    public function viewRevenue(User $user): bool
    {
        return $user->can('order-item.reports.view');
    }

    /**
     * Determine whether the user can view inventory level.
     */
    public function viewInventoryLevel(User $user): bool
    {
        return $user->can('order-item.inventory.view');
    }

    /**
     * Determine whether the user can reserve inventory.
     */
    public function reserveInventory(User $user): bool
    {
        return $user->can('order-item.inventory.manage');
    }

    /**
     * Determine whether the user can release inventory.
     */
    public function releaseInventory(User $user): bool
    {
        return $user->can('order-item.inventory.manage');
    }
}
