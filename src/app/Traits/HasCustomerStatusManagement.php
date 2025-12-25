<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Enums\CustomerStatus;
use Fereydooni\Shopping\app\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

trait HasCustomerStatusManagement
{
    /**
     * Activate customer
     */
    public function activateCustomer(Customer $customer): bool
    {
        return $this->changeStatus($customer, CustomerStatus::ACTIVE);
    }

    /**
     * Deactivate customer
     */
    public function deactivateCustomer(Customer $customer): bool
    {
        return $this->changeStatus($customer, CustomerStatus::INACTIVE);
    }

    /**
     * Suspend customer
     */
    public function suspendCustomer(Customer $customer, ?string $reason = null): bool
    {
        return $this->changeStatus($customer, CustomerStatus::SUSPENDED, $reason);
    }

    /**
     * Unsuspend customer
     */
    public function unsuspendCustomer(Customer $customer): bool
    {
        return $this->changeStatus($customer, CustomerStatus::ACTIVE);
    }

    /**
     * Get active customers
     */
    public function getActiveCustomers(): Collection
    {
        return $this->repository->findActive();
    }

    /**
     * Get inactive customers
     */
    public function getInactiveCustomers(): Collection
    {
        return $this->repository->findInactive();
    }

    /**
     * Get customers by status
     */
    public function getCustomersByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    /**
     * Get customers by status as DTOs
     */
    public function getCustomersByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    /**
     * Get customer count by status
     */
    public function getCustomerCountByStatus(string $status): int
    {
        return $this->repository->getCustomerCountByStatus($status);
    }

    /**
     * Get active customer count
     */
    public function getActiveCustomerCount(): int
    {
        return $this->repository->getActiveCustomerCount();
    }

    /**
     * Get inactive customer count
     */
    public function getInactiveCustomerCount(): int
    {
        return $this->repository->getInactiveCustomerCount();
    }

    /**
     * Validate customer status change
     */
    protected function validateCustomerStatusChange(Customer $customer, string $newStatus): void
    {
        $validator = \Illuminate\Support\Facades\Validator::make([], []);

        // Check if status change is valid
        $validStatuses = [
            CustomerStatus::ACTIVE,
            CustomerStatus::INACTIVE,
            CustomerStatus::SUSPENDED,
            CustomerStatus::PENDING,
        ];

        if (! in_array($newStatus, $validStatuses)) {
            $validator->errors()->add('status', 'Invalid customer status.');
        }

        // Check if customer can be suspended
        if ($newStatus === CustomerStatus::SUSPENDED) {
            if ($customer->total_orders > 0) {
                $validator->errors()->add('status', 'Cannot suspend customer with existing orders.');
            }
        }

        // Check if customer can be activated
        if ($newStatus === CustomerStatus::ACTIVE) {
            if ($customer->status === CustomerStatus::SUSPENDED) {
                // Check if suspension reason is resolved
                // This could be enhanced with more specific logic
            }
        }

        if ($validator->errors()->isNotEmpty()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    /**
     * Fire customer status changed event
     */
    protected function fireCustomerStatusChangedEvent(Customer $customer, string $newStatus, ?string $reason = null): void
    {
        switch ($newStatus) {
            case CustomerStatus::ACTIVE:
                if ($customer->status === CustomerStatus::SUSPENDED) {
                    event(new \Fereydooni\Shopping\app\Events\Customer\CustomerActivated($customer));
                }
                break;
            case CustomerStatus::INACTIVE:
                event(new \Fereydooni\Shopping\app\Events\Customer\CustomerDeactivated($customer));
                break;
            case CustomerStatus::SUSPENDED:
                event(new \Fereydooni\Shopping\app\Events\Customer\CustomerSuspended($customer, $reason));
                break;
        }
    }

    /**
     * Add status change note
     */
    protected function addCustomerStatusChangeNote(Customer $customer, string $newStatus, ?string $reason = null): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $oldStatus = $customer->status;

        $note = "[{$timestamp}] Status changed from {$oldStatus} to {$newStatus}";

        if ($reason) {
            $note .= " - Reason: {$reason}";
        }

        $currentNotes = $customer->notes ?? '';

        return $currentNotes ? $currentNotes."\n".$note : $note;
    }
}
