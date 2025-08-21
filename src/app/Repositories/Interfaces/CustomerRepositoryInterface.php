<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\DTOs\CustomerDTO;
use Fereydooni\Shopping\app\Enums\CustomerStatus;
use Fereydooni\Shopping\app\Enums\CustomerType;

interface CustomerRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;

    // Find operations
    public function find(int $id): ?Customer;
    public function findDTO(int $id): ?CustomerDTO;
    public function findByUserId(int $userId): ?Customer;
    public function findByUserIdDTO(int $userId): ?CustomerDTO;
    public function findByEmail(string $email): ?Customer;
    public function findByEmailDTO(string $email): ?CustomerDTO;
    public function findByPhone(string $phone): ?Customer;
    public function findByPhoneDTO(string $phone): ?CustomerDTO;
    public function findByCustomerNumber(string $customerNumber): ?Customer;
    public function findByCustomerNumberDTO(string $customerNumber): ?CustomerDTO;

    // Status-based queries
    public function findByStatus(string $status): Collection;
    public function findByStatusDTO(string $status): Collection;
    public function findActive(): Collection;
    public function findActiveDTO(): Collection;
    public function findInactive(): Collection;
    public function findInactiveDTO(): Collection;

    // Type-based queries
    public function findByType(string $type): Collection;
    public function findByTypeDTO(string $type): Collection;

    // Date range queries
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;

    // Range queries
    public function findByLoyaltyPointsRange(int $minPoints, int $maxPoints): Collection;
    public function findByLoyaltyPointsRangeDTO(int $minPoints, int $maxPoints): Collection;
    public function findByTotalSpentRange(float $minSpent, float $maxSpent): Collection;
    public function findByTotalSpentRangeDTO(float $minSpent, float $maxSpent): Collection;
    public function findByOrderCountRange(int $minOrders, int $maxOrders): Collection;
    public function findByOrderCountRangeDTO(int $minOrders, int $maxOrders): Collection;

    // Create and Update operations
    public function create(array $data): Customer;
    public function createAndReturnDTO(array $data): CustomerDTO;
    public function update(Customer $customer, array $data): bool;
    public function updateAndReturnDTO(Customer $customer, array $data): ?CustomerDTO;
    public function delete(Customer $customer): bool;

    // Status management
    public function activate(Customer $customer): bool;
    public function deactivate(Customer $customer): bool;
    public function suspend(Customer $customer, string $reason = null): bool;
    public function unsuspend(Customer $customer): bool;

    // Loyalty points management
    public function addLoyaltyPoints(Customer $customer, int $points, string $reason = null): bool;
    public function deductLoyaltyPoints(Customer $customer, int $points, string $reason = null): bool;
    public function resetLoyaltyPoints(Customer $customer): bool;

    // Statistics
    public function getCustomerCount(): int;
    public function getCustomerCountByStatus(string $status): int;
    public function getCustomerCountByType(string $type): int;
    public function getActiveCustomerCount(): int;
    public function getInactiveCustomerCount(): int;
    public function getTotalLoyaltyPoints(): int;
    public function getAverageLoyaltyPoints(): float;
    public function getTotalCustomerSpending(): float;
    public function getAverageCustomerSpending(): float;

    // Search operations
    public function search(string $query): Collection;
    public function searchDTO(string $query): Collection;
    public function searchByCompany(string $companyName): Collection;
    public function searchByCompanyDTO(string $companyName): Collection;

    // Top customers
    public function getTopSpenders(int $limit = 10): Collection;
    public function getTopSpendersDTO(int $limit = 10): Collection;
    public function getMostLoyal(int $limit = 10): Collection;
    public function getMostLoyalDTO(int $limit = 10): Collection;
    public function getNewestCustomers(int $limit = 10): Collection;
    public function getNewestCustomersDTO(int $limit = 10): Collection;
    public function getOldestCustomers(int $limit = 10): Collection;
    public function getOldestCustomersDTO(int $limit = 10): Collection;

    // Special queries
    public function getCustomersWithBirthdayThisMonth(): Collection;
    public function getCustomersWithBirthdayThisMonthDTO(): Collection;
    public function getCustomersByMarketingConsent(bool $consent): Collection;
    public function getCustomersByMarketingConsentDTO(bool $consent): Collection;
    public function getCustomersByNewsletterSubscription(bool $subscribed): Collection;
    public function getCustomersByNewsletterSubscriptionDTO(bool $subscribed): Collection;

    // Validation and utilities
    public function validateCustomer(array $data): bool;
    public function generateCustomerNumber(): string;
    public function isCustomerNumberUnique(string $customerNumber): bool;

    // Analytics and reporting
    public function getCustomerStats(): array;
    public function getCustomerStatsByStatus(): array;
    public function getCustomerStatsByType(): array;
    public function getCustomerGrowthStats(string $period = 'monthly'): array;
    public function getCustomerRetentionStats(): array;
    public function getCustomerLifetimeValue(int $customerId): float;

    // Related data
    public function getCustomerOrderHistory(int $customerId): Collection;
    public function getCustomerAddresses(int $customerId): Collection;
    public function getCustomerReviews(int $customerId): Collection;
    public function getCustomerWishlist(int $customerId): Collection;

    // Notes and preferences
    public function addCustomerNote(Customer $customer, string $note, string $type = 'general'): bool;
    public function getCustomerNotes(Customer $customer): Collection;
    public function updateCustomerPreferences(Customer $customer, array $preferences): bool;
    public function getCustomerPreferences(int $customerId): array;
}
