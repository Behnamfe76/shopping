<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\LoyaltyTransaction;
use Fereydooni\Shopping\app\DTOs\LoyaltyTransactionDTO;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionType;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus;
use Fereydooni\Shopping\app\Enums\LoyaltyReferenceType;

interface LoyaltyTransactionRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;
    public function find(int $id): ?LoyaltyTransaction;
    public function findDTO(int $id): ?LoyaltyTransactionDTO;

    // Find by specific criteria
    public function findByCustomerId(int $customerId): Collection;
    public function findByCustomerIdDTO(int $customerId): Collection;
    public function findByUserId(int $userId): Collection;
    public function findByUserIdDTO(int $userId): Collection;
    public function findByType(LoyaltyTransactionType $type): Collection;
    public function findByTypeDTO(LoyaltyTransactionType $type): Collection;
    public function findByStatus(LoyaltyTransactionStatus $status): Collection;
    public function findByStatusDTO(LoyaltyTransactionStatus $status): Collection;
    public function findByReferenceType(LoyaltyReferenceType $referenceType): Collection;
    public function findByReferenceTypeDTO(LoyaltyReferenceType $referenceType): Collection;
    public function findByReferenceId(int $referenceId): Collection;
    public function findByReferenceIdDTO(int $referenceId): Collection;
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function findByDateRangeDTO(string $startDate, string $endDate): Collection;
    public function findByPointsRange(int $minPoints, int $maxPoints): Collection;
    public function findByPointsRangeDTO(int $minPoints, int $maxPoints): Collection;

    // Specialized queries
    public function findExpiringTransactions(string $date): Collection;
    public function findExpiringTransactionsDTO(string $date): Collection;
    public function findReversedTransactions(): Collection;
    public function findReversedTransactionsDTO(): Collection;

    // Create and Update operations
    public function create(array $data): LoyaltyTransaction;
    public function createAndReturnDTO(array $data): LoyaltyTransactionDTO;
    public function update(LoyaltyTransaction $transaction, array $data): bool;
    public function updateAndReturnDTO(LoyaltyTransaction $transaction, array $data): ?LoyaltyTransactionDTO;
    public function delete(LoyaltyTransaction $transaction): bool;

    // Transaction-specific operations
    public function reverse(LoyaltyTransaction $transaction, string $reason = null): bool;
    public function addPoints(int $customerId, int $points, string $reason = null, array $metadata = []): LoyaltyTransaction;
    public function deductPoints(int $customerId, int $points, string $reason = null, array $metadata = []): LoyaltyTransaction;

    // Balance and calculations
    public function calculateBalance(int $customerId): int;
    public function calculateBalanceValue(int $customerId): float;
    public function checkExpiration(int $customerId): int;
    public function calculateTier(int $customerId): string;
    public function validateTransaction(array $data): bool;

    // Statistics and counts
    public function getTransactionCount(): int;
    public function getTransactionCountByCustomer(int $customerId): int;
    public function getTransactionCountByType(LoyaltyTransactionType $type): int;
    public function getTransactionCountByStatus(LoyaltyTransactionStatus $status): int;
    public function getTransactionCountByReferenceType(LoyaltyReferenceType $referenceType): int;
    public function getTotalPointsIssued(): int;
    public function getTotalPointsRedeemed(): int;
    public function getTotalPointsExpired(): int;
    public function getTotalPointsReversed(): int;
    public function getAveragePointsPerTransaction(): float;
    public function getAveragePointsPerCustomer(): float;

    // Search operations
    public function search(string $query): Collection;
    public function searchDTO(string $query): Collection;
    public function searchByCustomer(int $customerId, string $query): Collection;
    public function searchByCustomerDTO(int $customerId, string $query): Collection;

    // Recent transactions
    public function getRecentTransactions(int $limit = 10): Collection;
    public function getRecentTransactionsDTO(int $limit = 10): Collection;
    public function getRecentTransactionsByCustomer(int $customerId, int $limit = 10): Collection;
    public function getRecentTransactionsByCustomerDTO(int $customerId, int $limit = 10): Collection;
    public function getTransactionsByType(int $customerId, LoyaltyTransactionType $type, int $limit = 10): Collection;
    public function getTransactionsByTypeDTO(int $customerId, LoyaltyTransactionType $type, int $limit = 10): Collection;
    public function getTransactionsByStatus(int $customerId, LoyaltyTransactionStatus $status, int $limit = 10): Collection;
    public function getTransactionsByStatusDTO(int $customerId, LoyaltyTransactionStatus $status, int $limit = 10): Collection;

    // Customer history and summary
    public function getCustomerTransactionHistory(int $customerId): Collection;
    public function getCustomerTransactionHistoryDTO(int $customerId): Collection;
    public function getCustomerTransactionSummary(int $customerId): array;
    public function getCustomerTransactionSummaryDTO(int $customerId): array;

    // Import/Export operations
    public function exportCustomerHistory(int $customerId): array;
    public function importCustomerHistory(int $customerId, array $transactions): bool;

    // Analytics and insights
    public function getTransactionAnalytics(int $customerId): array;
    public function getTransactionAnalyticsByType(LoyaltyTransactionType $type): array;
    public function getTransactionAnalyticsByDateRange(string $startDate, string $endDate): array;
    public function getTransactionRecommendations(int $customerId): array;
    public function getTransactionInsights(int $customerId): array;
    public function getTransactionTrends(int $customerId, string $period = 'monthly'): array;
    public function getTransactionComparison(int $customerId1, int $customerId2): array;
    public function getTransactionForecast(int $customerId): array;

    // Utility methods
    public function calculatePointsValue(int $points): float;
    public function generateRecommendations(): array;
    public function calculateInsights(): array;
    public function forecastTrends(string $period = 'monthly'): array;
}
