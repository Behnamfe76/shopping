<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\DTOs\LoyaltyTransactionDTO;
use Fereydooni\Shopping\app\Enums\LoyaltyReferenceType;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionType;
use Fereydooni\Shopping\app\Models\LoyaltyTransaction;
use Fereydooni\Shopping\app\Repositories\Interfaces\LoyaltyTransactionRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasLoyaltyPointsManagement;
use Fereydooni\Shopping\app\Traits\HasLoyaltyTransactionOperations;
use Fereydooni\Shopping\app\Traits\HasLoyaltyTransactionStatusManagement;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Illuminate\Database\Eloquent\Collection;

class LoyaltyTransactionRepository implements LoyaltyTransactionRepositoryInterface
{
    use HasCrudOperations,
        HasLoyaltyPointsManagement,
        HasLoyaltyTransactionOperations,
        HasLoyaltyTransactionStatusManagement,
        HasSearchOperations;

    public function __construct()
    {
        $this->model = new LoyaltyTransaction;
        $this->dtoClass = LoyaltyTransactionDTO::class;
    }

    // Override trait methods to ensure proper implementation
    public function findDTO(int $id): ?LoyaltyTransactionDTO
    {
        $model = $this->find($id);

        return $model ? LoyaltyTransactionDTO::fromModel($model) : null;
    }

    public function createAndReturnDTO(array $data): LoyaltyTransactionDTO
    {
        $model = $this->create($data);

        return LoyaltyTransactionDTO::fromModel($model);
    }

    public function updateAndReturnDTO(LoyaltyTransaction $transaction, array $data): ?LoyaltyTransactionDTO
    {
        $updated = $this->update($transaction, $data);

        return $updated ? LoyaltyTransactionDTO::fromModel($transaction->fresh()) : null;
    }

    // Additional repository-specific methods
    public function findByCustomerIdDTO(int $customerId): Collection
    {
        return $this->findByCustomerId($customerId)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->findByUserId($userId)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByTypeDTO(LoyaltyTransactionType $type): Collection
    {
        return $this->findByType($type)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByStatusDTO(LoyaltyTransactionStatus $status): Collection
    {
        return $this->findByStatus($status)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByReferenceTypeDTO(LoyaltyReferenceType $referenceType): Collection
    {
        return $this->findByReferenceType($referenceType)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByReferenceIdDTO(int $referenceId): Collection
    {
        return $this->findByReferenceId($referenceId)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByDateRange($startDate, $endDate)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByPointsRangeDTO(int $minPoints, int $maxPoints): Collection
    {
        return $this->findByPointsRange($minPoints, $maxPoints)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findExpiringTransactionsDTO(string $date): Collection
    {
        return $this->findExpiringTransactions($date)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findReversedTransactionsDTO(): Collection
    {
        return $this->findReversedTransactions()->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function searchByCustomerDTO(int $customerId, string $query): Collection
    {
        return $this->searchByCustomer($customerId, $query)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function getRecentTransactionsDTO(int $limit = 10): Collection
    {
        return $this->getRecentTransactions($limit)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function getRecentTransactionsByCustomerDTO(int $customerId, int $limit = 10): Collection
    {
        return $this->getRecentTransactionsByCustomer($customerId, $limit)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function getTransactionsByTypeDTO(int $customerId, LoyaltyTransactionType $type, int $limit = 10): Collection
    {
        return $this->getTransactionsByType($customerId, $type, $limit)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function getTransactionsByStatusDTO(int $customerId, LoyaltyTransactionStatus $status, int $limit = 10): Collection
    {
        return $this->getTransactionsByStatus($customerId, $status, $limit)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function getCustomerTransactionHistoryDTO(int $customerId): Collection
    {
        return $this->getCustomerTransactionHistory($customerId)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function getCustomerTransactionSummaryDTO(int $customerId): array
    {
        return $this->getCustomerTransactionSummary($customerId);
    }

    // Additional business logic methods
    public function getTransactionAnalyticsByType(LoyaltyTransactionType $type): array
    {
        return $this->getTransactionAnalyticsByType($type);
    }

    public function getTransactionAnalyticsByDateRange(string $startDate, string $endDate): array
    {
        return $this->getTransactionAnalyticsByDateRange($startDate, $endDate);
    }

    public function getTransactionRecommendations(int $customerId): array
    {
        return $this->getTransactionRecommendations($customerId);
    }

    public function getTransactionInsights(int $customerId): array
    {
        return $this->getTransactionInsights($customerId);
    }

    public function getTransactionTrends(int $customerId, string $period = 'monthly'): array
    {
        return $this->getTransactionTrends($customerId, $period);
    }

    public function getTransactionComparison(int $customerId1, int $customerId2): array
    {
        return $this->getTransactionComparison($customerId1, $customerId2);
    }

    public function getTransactionForecast(int $customerId): array
    {
        return $this->getTransactionForecast($customerId);
    }

    public function generateRecommendations(): array
    {
        return $this->generateRecommendations();
    }

    public function calculateInsights(): array
    {
        return $this->calculateInsights();
    }

    public function forecastTrends(string $period = 'monthly'): array
    {
        return $this->forecastTrends($period);
    }
}
