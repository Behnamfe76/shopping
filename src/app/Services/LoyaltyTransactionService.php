<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\Repositories\Interfaces\LoyaltyTransactionRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasLoyaltyPointsManagement;
use Fereydooni\Shopping\app\Traits\HasLoyaltyTransactionOperations;
use Fereydooni\Shopping\app\Traits\HasLoyaltyTransactionStatusManagement;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;

class LoyaltyTransactionService
{
    use HasCrudOperations,
        HasLoyaltyPointsManagement,
        HasLoyaltyTransactionOperations,
        HasLoyaltyTransactionStatusManagement,
        HasSearchOperations;

    protected LoyaltyTransactionRepositoryInterface $repository;

    public function __construct(LoyaltyTransactionRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->model = $repository->getModel();
        $this->dtoClass = \Fereydooni\Shopping\app\DTOs\LoyaltyTransactionDTO::class;
    }

    /**
     * Get the underlying repository
     */
    public function getRepository(): LoyaltyTransactionRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Get the model instance
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Override trait methods to delegate to repository
     */
    public function all()
    {
        return $this->repository->all();
    }

    public function paginate(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15)
    {
        return $this->repository->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null)
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id)
    {
        return $this->repository->findDTO($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function createAndReturnDTO(array $data)
    {
        return $this->repository->createAndReturnDTO($data);
    }

    public function update($model, array $data): bool
    {
        return $this->repository->update($model, $data);
    }

    public function updateAndReturnDTO($model, array $data)
    {
        return $this->repository->updateAndReturnDTO($model, $data);
    }

    public function delete($model): bool
    {
        return $this->repository->delete($model);
    }

    // Loyalty transaction-specific methods
    public function findByCustomerId(int $customerId)
    {
        return $this->repository->findByCustomerId($customerId);
    }

    public function findByCustomerIdDTO(int $customerId)
    {
        return $this->repository->findByCustomerIdDTO($customerId);
    }

    public function findByUserId(int $userId)
    {
        return $this->repository->findByUserId($userId);
    }

    public function findByUserIdDTO(int $userId)
    {
        return $this->repository->findByUserIdDTO($userId);
    }

    public function findByType($type)
    {
        return $this->repository->findByType($type);
    }

    public function findByTypeDTO($type)
    {
        return $this->repository->findByTypeDTO($type);
    }

    public function findByStatus($status)
    {
        return $this->repository->findByStatus($status);
    }

    public function findByStatusDTO($status)
    {
        return $this->repository->findByStatusDTO($status);
    }

    public function findByReferenceType($referenceType)
    {
        return $this->repository->findByReferenceType($referenceType);
    }

    public function findByReferenceTypeDTO($referenceType)
    {
        return $this->repository->findByReferenceTypeDTO($referenceType);
    }

    public function findByReferenceId(int $referenceId)
    {
        return $this->repository->findByReferenceId($referenceId);
    }

    public function findByReferenceIdDTO(int $referenceId)
    {
        return $this->repository->findByReferenceIdDTO($referenceId);
    }

    public function findByDateRange(string $startDate, string $endDate)
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    public function findByDateRangeDTO(string $startDate, string $endDate)
    {
        return $this->repository->findByDateRangeDTO($startDate, $endDate);
    }

    public function findByPointsRange(int $minPoints, int $maxPoints)
    {
        return $this->repository->findByPointsRange($minPoints, $maxPoints);
    }

    public function findByPointsRangeDTO(int $minPoints, int $maxPoints)
    {
        return $this->repository->findByPointsRangeDTO($minPoints, $maxPoints);
    }

    public function findExpiringTransactions(string $date)
    {
        return $this->repository->findExpiringTransactions($date);
    }

    public function findExpiringTransactionsDTO(string $date)
    {
        return $this->repository->findExpiringTransactionsDTO($date);
    }

    public function findReversedTransactions()
    {
        return $this->repository->findReversedTransactions();
    }

    public function findReversedTransactionsDTO()
    {
        return $this->repository->findReversedTransactionsDTO();
    }

    // Transaction-specific operations
    public function reverse($transaction, ?string $reason = null): bool
    {
        return $this->repository->reverse($transaction, $reason);
    }

    public function addPoints(int $customerId, int $points, ?string $reason = null, array $metadata = [])
    {
        return $this->repository->addPoints($customerId, $points, $reason, $metadata);
    }

    public function deductPoints(int $customerId, int $points, ?string $reason = null, array $metadata = [])
    {
        return $this->repository->deductPoints($customerId, $points, $reason, $metadata);
    }

    // Balance and calculations
    public function calculateBalance(int $customerId): int
    {
        return $this->repository->calculateBalance($customerId);
    }

    public function calculateBalanceValue(int $customerId): float
    {
        return $this->repository->calculateBalanceValue($customerId);
    }

    public function checkExpiration(int $customerId): int
    {
        return $this->repository->checkExpiration($customerId);
    }

    public function calculateTier(int $customerId): string
    {
        return $this->repository->calculateTier($customerId);
    }

    public function validateTransaction(array $data): bool
    {
        return $this->repository->validateTransaction($data);
    }

    // Statistics and counts
    public function getTransactionCount(): int
    {
        return $this->repository->getTransactionCount();
    }

    public function getTransactionCountByCustomer(int $customerId): int
    {
        return $this->repository->getTransactionCountByCustomer($customerId);
    }

    public function getTransactionCountByType($type): int
    {
        return $this->repository->getTransactionCountByType($type);
    }

    public function getTransactionCountByStatus($status): int
    {
        return $this->repository->getTransactionCountByStatus($status);
    }

    public function getTransactionCountByReferenceType($referenceType): int
    {
        return $this->repository->getTransactionCountByReferenceType($referenceType);
    }

    public function getTotalPointsIssued(): int
    {
        return $this->repository->getTotalPointsIssued();
    }

    public function getTotalPointsRedeemed(): int
    {
        return $this->repository->getTotalPointsRedeemed();
    }

    public function getTotalPointsExpired(): int
    {
        return $this->repository->getTotalPointsExpired();
    }

    public function getTotalPointsReversed(): int
    {
        return $this->repository->getTotalPointsReversed();
    }

    public function getAveragePointsPerTransaction(): float
    {
        return $this->repository->getAveragePointsPerTransaction();
    }

    public function getAveragePointsPerCustomer(): float
    {
        return $this->repository->getAveragePointsPerCustomer();
    }

    // Search operations
    public function search(string $query)
    {
        return $this->repository->search($query);
    }

    public function searchDTO(string $query)
    {
        return $this->repository->searchDTO($query);
    }

    public function searchByCustomer(int $customerId, string $query)
    {
        return $this->repository->searchByCustomer($customerId, $query);
    }

    public function searchByCustomerDTO(int $customerId, string $query)
    {
        return $this->repository->searchByCustomerDTO($customerId, $query);
    }

    // Recent transactions
    public function getRecentTransactions(int $limit = 10)
    {
        return $this->repository->getRecentTransactions($limit);
    }

    public function getRecentTransactionsDTO(int $limit = 10)
    {
        return $this->repository->getRecentTransactionsDTO($limit);
    }

    public function getRecentTransactionsByCustomer(int $customerId, int $limit = 10)
    {
        return $this->repository->getRecentTransactionsByCustomer($customerId, $limit);
    }

    public function getRecentTransactionsByCustomerDTO(int $customerId, int $limit = 10)
    {
        return $this->repository->getRecentTransactionsByCustomerDTO($customerId, $limit);
    }

    public function getTransactionsByType(int $customerId, $type, int $limit = 10)
    {
        return $this->repository->getTransactionsByType($customerId, $type, $limit);
    }

    public function getTransactionsByTypeDTO(int $customerId, $type, int $limit = 10)
    {
        return $this->repository->getTransactionsByTypeDTO($customerId, $type, $limit);
    }

    public function getTransactionsByStatus(int $customerId, $status, int $limit = 10)
    {
        return $this->repository->getTransactionsByStatus($customerId, $status, $limit);
    }

    public function getTransactionsByStatusDTO(int $customerId, $status, int $limit = 10)
    {
        return $this->repository->getTransactionsByStatusDTO($customerId, $status, $limit);
    }

    // Customer history and summary
    public function getCustomerTransactionHistory(int $customerId)
    {
        return $this->repository->getCustomerTransactionHistory($customerId);
    }

    public function getCustomerTransactionHistoryDTO(int $customerId)
    {
        return $this->repository->getCustomerTransactionHistoryDTO($customerId);
    }

    public function getCustomerTransactionSummary(int $customerId): array
    {
        return $this->repository->getCustomerTransactionSummary($customerId);
    }

    public function getCustomerTransactionSummaryDTO(int $customerId): array
    {
        return $this->repository->getCustomerTransactionSummaryDTO($customerId);
    }

    // Import/Export operations
    public function exportCustomerHistory(int $customerId): array
    {
        return $this->repository->exportCustomerHistory($customerId);
    }

    public function importCustomerHistory(int $customerId, array $transactions): bool
    {
        return $this->repository->importCustomerHistory($customerId, $transactions);
    }

    // Analytics and insights
    public function getTransactionAnalytics(int $customerId): array
    {
        return $this->repository->getTransactionAnalytics($customerId);
    }

    public function getTransactionAnalyticsByType($type): array
    {
        return $this->repository->getTransactionAnalyticsByType($type);
    }

    public function getTransactionAnalyticsByDateRange(string $startDate, string $endDate): array
    {
        return $this->repository->getTransactionAnalyticsByDateRange($startDate, $endDate);
    }

    public function getTransactionRecommendations(int $customerId): array
    {
        return $this->repository->getTransactionRecommendations($customerId);
    }

    public function getTransactionInsights(int $customerId): array
    {
        return $this->repository->getTransactionInsights($customerId);
    }

    public function getTransactionTrends(int $customerId, string $period = 'monthly'): array
    {
        return $this->repository->getTransactionTrends($customerId, $period);
    }

    public function getTransactionComparison(int $customerId1, int $customerId2): array
    {
        return $this->repository->getTransactionComparison($customerId1, $customerId2);
    }

    public function getTransactionForecast(int $customerId): array
    {
        return $this->repository->getTransactionForecast($customerId);
    }

    // Utility methods
    public function calculatePointsValue(int $points): float
    {
        return $this->repository->calculatePointsValue($points);
    }

    public function generateRecommendations(): array
    {
        return $this->repository->generateRecommendations();
    }

    public function calculateInsights(): array
    {
        return $this->repository->calculateInsights();
    }

    public function forecastTrends(string $period = 'monthly'): array
    {
        return $this->repository->forecastTrends($period);
    }
}
