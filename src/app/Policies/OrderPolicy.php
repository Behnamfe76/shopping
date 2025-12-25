<?php

namespace Fereydooni\Shopping\app\Policies;

use Fereydooni\Shopping\app\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('order.view.any');
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // Check if user can view any orders
        if ($user->can('order.view.any')) {
            return true;
        }

        // Check if user can view own orders and owns this order
        if ($user->can('order.view.own') && $order->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        return $user->can('order.create.any') || $user->can('order.create.own');
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Check if user can update any orders
        if ($user->can('order.update.any')) {
            return true;
        }

        // Check if user can update own orders and owns this order
        if ($user->can('order.update.own') && $order->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        // Check if user can delete any orders
        if ($user->can('order.delete.any')) {
            return true;
        }

        // Check if user can delete own orders and owns this order
        if ($user->can('order.delete.own') && $order->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        // Check if user can cancel any orders
        if ($user->can('order.cancel.any')) {
            return true;
        }

        // Check if user can cancel own orders and owns this order
        if ($user->can('order.cancel.own') && $order->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can mark the order as paid.
     */
    public function markPaid(User $user, Order $order): bool
    {
        // Check if user can mark any orders as paid
        if ($user->can('order.mark.paid.any')) {
            return true;
        }

        // Check if user can mark own orders as paid and owns this order
        if ($user->can('order.mark.paid.own') && $order->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can mark the order as shipped.
     */
    public function markShipped(User $user, Order $order): bool
    {
        // Check if user can mark any orders as shipped
        if ($user->can('order.mark.shipped.any')) {
            return true;
        }

        // Check if user can mark own orders as shipped and owns this order
        if ($user->can('order.mark.shipped.own') && $order->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can mark the order as completed.
     */
    public function markCompleted(User $user, Order $order): bool
    {
        // Check if user can mark any orders as completed
        if ($user->can('order.mark.completed.any')) {
            return true;
        }

        // Check if user can mark own orders as completed and owns this order
        if ($user->can('order.mark.completed.own') && $order->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can search orders.
     */
    public function search(User $user): bool
    {
        return $user->can('order.search.any') || $user->can('order.search.own');
    }

    /**
     * Determine whether the user can export orders.
     */
    public function export(User $user): bool
    {
        return $user->can('order.export');
    }

    /**
     * Determine whether the user can import orders.
     */
    public function import(User $user): bool
    {
        return $user->can('order.import');
    }

    /**
     * Determine whether the user can process refunds.
     */
    public function refund(User $user, Order $order): bool
    {
        // Check if user can refund any orders
        if ($user->can('order.refund.any')) {
            return true;
        }

        // Check if user can refund own orders and owns this order
        if ($user->can('order.refund.own') && $order->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can add order notes.
     */
    public function addNote(User $user, Order $order): bool
    {
        // Check if user can add notes to any orders
        if ($user->can('order.notes.add')) {
            return true;
        }

        // Check if user owns this order (for customer notes)
        if ($order->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view order notes.
     */
    public function viewNotes(User $user, Order $order): bool
    {
        // Check if user can view notes for any orders
        if ($user->can('order.notes.view')) {
            return true;
        }

        // Check if user owns this order
        if ($order->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view order reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->can('order.reports.view');
    }

    /**
     * Determine whether the user can generate order reports.
     */
    public function generateReports(User $user): bool
    {
        return $user->can('order.reports.generate');
    }

    /**
     * Determine whether the user can validate orders.
     */
    public function validate(User $user): bool
    {
        return $user->can('order.validate');
    }
}
