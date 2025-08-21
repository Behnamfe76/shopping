<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\DTOs\CustomerDTO;
use Fereydooni\Shopping\app\Enums\CustomerStatus;
use Fereydooni\Shopping\app\Enums\CustomerType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;

trait HasCustomerOperations
{
    protected Customer $model;
    protected string $dtoClass = CustomerDTO::class;

    // Customer-specific CRUD operations
    public function findByUserId(int $userId): ?Customer
    {
        return $this->repository->findByUserId($userId);
    }

    public function findByUserIdDTO(int $userId): ?CustomerDTO
    {
        return $this->repository->findByUserIdDTO($userId);
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->repository->findByEmail($email);
    }

    public function findByEmailDTO(string $email): ?CustomerDTO
    {
        return $this->repository->findByEmailDTO($email);
    }

    public function findByPhone(string $phone): ?Customer
    {
        return $this->repository->findByPhone($phone);
    }

    public function findByPhoneDTO(string $phone): ?CustomerDTO
    {
        return $this->repository->findByPhoneDTO($phone);
    }

    public function findByCustomerNumber(string $customerNumber): ?Customer
    {
        return $this->repository->findByCustomerNumber($customerNumber);
    }

    public function findByCustomerNumberDTO(string $customerNumber): ?CustomerDTO
    {
        return $this->repository->findByCustomerNumberDTO($customerNumber);
    }

    public function findByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    public function findByType(string $type): Collection
    {
        return $this->repository->findByType($type);
    }

    public function findByTypeDTO(string $type): Collection
    {
        return $this->repository->findByTypeDTO($type);
    }

    public function findActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function findActiveDTO(): Collection
    {
        return $this->repository->findActiveDTO();
    }

    public function findInactive(): Collection
    {
        return $this->repository->findInactive();
    }

    public function findInactiveDTO(): Collection
    {
        return $this->repository->findInactiveDTO();
    }

    // Customer analytics
    public function getCustomerStats(): array
    {
        return $this->repository->getCustomerStats();
    }

    public function getCustomerStatsByStatus(): array
    {
        return $this->repository->getCustomerStatsByStatus();
    }

    public function getCustomerStatsByType(): array
    {
        return $this->repository->getCustomerStatsByType();
    }

    public function getCustomerGrowthStats(string $period = 'monthly'): array
    {
        return $this->repository->getCustomerGrowthStats($period);
    }

    public function getCustomerRetentionStats(): array
    {
        return $this->repository->getCustomerRetentionStats();
    }

    public function getCustomerLifetimeValue(int $customerId): float
    {
        return $this->repository->getCustomerLifetimeValue($customerId);
    }

    // Customer segmentation
    public function getTopSpenders(int $limit = 10): Collection
    {
        return $this->repository->getTopSpenders($limit);
    }

    public function getTopSpendersDTO(int $limit = 10): Collection
    {
        return $this->repository->getTopSpendersDTO($limit);
    }

    public function getMostLoyal(int $limit = 10): Collection
    {
        return $this->repository->getMostLoyal($limit);
    }

    public function getMostLoyalDTO(int $limit = 10): Collection
    {
        return $this->repository->getMostLoyalDTO($limit);
    }

    public function getNewestCustomers(int $limit = 10): Collection
    {
        return $this->repository->getNewestCustomers($limit);
    }

    public function getNewestCustomersDTO(int $limit = 10): Collection
    {
        return $this->repository->getNewestCustomersDTO($limit);
    }

    public function getOldestCustomers(int $limit = 10): Collection
    {
        return $this->repository->getOldestCustomers($limit);
    }

    public function getOldestCustomersDTO(int $limit = 10): Collection
    {
        return $this->repository->getOldestCustomersDTO($limit);
    }

    public function getCustomersWithBirthdayThisMonth(): Collection
    {
        return $this->repository->getCustomersWithBirthdayThisMonth();
    }

    public function getCustomersWithBirthdayThisMonthDTO(): Collection
    {
        return $this->repository->getCustomersWithBirthdayThisMonthDTO();
    }

    public function getCustomersByMarketingConsent(bool $consent): Collection
    {
        return $this->repository->getCustomersByMarketingConsent($consent);
    }

    public function getCustomersByMarketingConsentDTO(bool $consent): Collection
    {
        return $this->repository->getCustomersByMarketingConsentDTO($consent);
    }

    public function getCustomersByNewsletterSubscription(bool $subscribed): Collection
    {
        return $this->repository->getCustomersByNewsletterSubscription($subscribed);
    }

    public function getCustomersByNewsletterSubscriptionDTO(bool $subscribed): Collection
    {
        return $this->repository->getCustomersByNewsletterSubscriptionDTO($subscribed);
    }

    // Customer search
    public function searchByCompany(string $companyName): Collection
    {
        return $this->repository->searchByCompany($companyName);
    }

    public function searchByCompanyDTO(string $companyName): Collection
    {
        return $this->repository->searchByCompanyDTO($companyName);
    }

    // Customer relationships
    public function getCustomerOrderHistory(int $customerId): Collection
    {
        return $this->repository->getCustomerOrderHistory($customerId);
    }

    public function getCustomerAddresses(int $customerId): Collection
    {
        return $this->repository->getCustomerAddresses($customerId);
    }

    public function getCustomerReviews(int $customerId): Collection
    {
        return $this->repository->getCustomerReviews($customerId);
    }

    public function getCustomerWishlist(int $customerId): Collection
    {
        return $this->repository->getCustomerWishlist($customerId);
    }

    // Customer preferences
    public function getCustomerPreferences(int $customerId): array
    {
        return $this->repository->getCustomerPreferences($customerId);
    }

    public function updateCustomerPreferences(Customer $customer, array $preferences): bool
    {
        return $this->repository->updateCustomerPreferences($customer, $preferences);
    }

    // Customer notes
    public function addCustomerNote(Customer $customer, string $note, string $type = 'general'): bool
    {
        return $this->repository->addCustomerNote($customer, $note, $type);
    }

    public function getCustomerNotes(Customer $customer): Collection
    {
        return $this->repository->getCustomerNotes($customer);
    }

    // Utility methods
    public function generateCustomerNumber(): string
    {
        return $this->repository->generateCustomerNumber();
    }

    public function isCustomerNumberUnique(string $customerNumber): bool
    {
        return $this->repository->isCustomerNumberUnique($customerNumber);
    }

    public function validateCustomerData(array $data): bool
    {
        return $this->repository->validateCustomer($data);
    }

    // Override getSearchableFields for Customer-specific fields
    protected function getSearchableFields(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'phone',
            'customer_number',
            'company_name'
        ];
    }

    // Override convertToDTO for Customer-specific conversion
    protected function convertToDTO(object $item): CustomerDTO
    {
        return CustomerDTO::fromModel($item);
    }

    // Override getDtoClass for Customer-specific DTO
    protected function getDtoClass(): string
    {
        return CustomerDTO::class;
    }
}
