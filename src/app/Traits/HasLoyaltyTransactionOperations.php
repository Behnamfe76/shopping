<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\DTOs\LoyaltyTransactionDTO;
use Fereydooni\Shopping\app\Enums\LoyaltyReferenceType;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionStatus;
use Fereydooni\Shopping\app\Enums\LoyaltyTransactionType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

trait HasLoyaltyTransactionOperations
{
    // Loyalty transaction-specific find methods
    public function findByCustomerId(int $customerId): Collection
    {
        return $this->model::byCustomer($customerId)->get();
    }

    public function findByCustomerIdDTO(int $customerId): Collection
    {
        return $this->findByCustomerId($customerId)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByUserId(int $userId): Collection
    {
        return $this->model::where('user_id', $userId)->get();
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        return $this->findByUserId($userId)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByType(LoyaltyTransactionType $type): Collection
    {
        return $this->model::byType($type)->get();
    }

    public function findByTypeDTO(LoyaltyTransactionType $type): Collection
    {
        return $this->findByType($type)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByStatus(LoyaltyTransactionStatus $status): Collection
    {
        return $this->model::byStatus($status)->get();
    }

    public function findByStatusDTO(LoyaltyTransactionStatus $status): Collection
    {
        return $this->findByStatus($status)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByReferenceType(LoyaltyReferenceType $referenceType): Collection
    {
        return $this->model::byReferenceType($referenceType)->get();
    }

    public function findByReferenceTypeDTO(LoyaltyReferenceType $referenceType): Collection
    {
        return $this->findByReferenceType($referenceType)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByReferenceId(int $referenceId): Collection
    {
        return $this->model::where('reference_id', $referenceId)->get();
    }

    public function findByReferenceIdDTO(int $referenceId): Collection
    {
        return $this->findByReferenceId($referenceId)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model::whereBetween('created_at', [$startDate, $endDate])->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByDateRange($startDate, $endDate)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findByPointsRange(int $minPoints, int $maxPoints): Collection
    {
        return $this->model::whereBetween('points', [$minPoints, $maxPoints])->get();
    }

    public function findByPointsRangeDTO(int $minPoints, int $maxPoints): Collection
    {
        return $this->findByPointsRange($minPoints, $maxPoints)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    // Specialized queries
    public function findExpiringTransactions(string $date): Collection
    {
        return $this->model::expired()->where('expires_at', '<=', $date)->get();
    }

    public function findExpiringTransactionsDTO(string $date): Collection
    {
        return $this->findExpiringTransactions($date)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function findReversedTransactions(): Collection
    {
        return $this->model::reversed()->get();
    }

    public function findReversedTransactionsDTO(): Collection
    {
        return $this->findReversedTransactions()->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    // Transaction analytics and statistics
    public function getTransactionCount(): int
    {
        return $this->model::count();
    }

    public function getTransactionCountByCustomer(int $customerId): int
    {
        return $this->model::byCustomer($customerId)->count();
    }

    public function getTransactionCountByType(LoyaltyTransactionType $type): int
    {
        return $this->model::byType($type)->count();
    }

    public function getTransactionCountByStatus(LoyaltyTransactionStatus $status): int
    {
        return $this->model::byStatus($status)->count();
    }

    public function getTransactionCountByReferenceType(LoyaltyReferenceType $referenceType): int
    {
        return $this->model::byReferenceType($referenceType)->count();
    }

    public function getTotalPointsIssued(): int
    {
        return $this->model::earned()->sum('points');
    }

    public function getTotalPointsRedeemed(): int
    {
        return $this->model::redeemed()->sum('points');
    }

    public function getTotalPointsExpired(): int
    {
        return $this->model::expired()->sum('points');
    }

    public function getTotalPointsReversed(): int
    {
        return $this->model::reversed()->sum('points');
    }

    public function getAveragePointsPerTransaction(): float
    {
        return $this->model::avg('points') ?? 0;
    }

    public function getAveragePointsPerCustomer(): float
    {
        return $this->model::select('customer_id')
            ->selectRaw('AVG(points) as avg_points')
            ->groupBy('customer_id')
            ->avg('avg_points') ?? 0;
    }

    // Search functionality
    public function search(string $query): Collection
    {
        return $this->model::where(function ($q) use ($query) {
            $q->where('description', 'like', "%{$query}%")
                ->orWhere('reason', 'like', "%{$query}%")
                ->orWhereHas('customer', function ($customerQuery) use ($query) {
                    $customerQuery->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
        })->get();
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function searchByCustomer(int $customerId, string $query): Collection
    {
        return $this->model::byCustomer($customerId)->where(function ($q) use ($query) {
            $q->where('description', 'like', "%{$query}%")
                ->orWhere('reason', 'like', "%{$query}%");
        })->get();
    }

    public function searchByCustomerDTO(int $customerId, string $query): Collection
    {
        return $this->searchByCustomer($customerId, $query)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    // Recent transactions
    public function getRecentTransactions(int $limit = 10): Collection
    {
        return $this->model::latest()->limit($limit)->get();
    }

    public function getRecentTransactionsDTO(int $limit = 10): Collection
    {
        return $this->getRecentTransactions($limit)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function getRecentTransactionsByCustomer(int $customerId, int $limit = 10): Collection
    {
        return $this->model::byCustomer($customerId)->latest()->limit($limit)->get();
    }

    public function getRecentTransactionsByCustomerDTO(int $customerId, int $limit = 10): Collection
    {
        return $this->getRecentTransactionsByCustomer($customerId, $limit)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function getTransactionsByType(int $customerId, LoyaltyTransactionType $type, int $limit = 10): Collection
    {
        return $this->model::byCustomer($customerId)->byType($type)->latest()->limit($limit)->get();
    }

    public function getTransactionsByTypeDTO(int $customerId, LoyaltyTransactionType $type, int $limit = 10): Collection
    {
        return $this->getTransactionsByType($customerId, $type, $limit)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function getTransactionsByStatus(int $customerId, LoyaltyTransactionStatus $status, int $limit = 10): Collection
    {
        return $this->model::byCustomer($customerId)->byStatus($status)->latest()->limit($limit)->get();
    }

    public function getTransactionsByStatusDTO(int $customerId, LoyaltyTransactionStatus $status, int $limit = 10): Collection
    {
        return $this->getTransactionsByStatus($customerId, $status, $limit)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    // Customer history and summary
    public function getCustomerTransactionHistory(int $customerId): Collection
    {
        return $this->model::byCustomer($customerId)->with(['customer', 'user'])->latest()->get();
    }

    public function getCustomerTransactionHistoryDTO(int $customerId): Collection
    {
        return $this->getCustomerTransactionHistory($customerId)->map(function ($transaction) {
            return LoyaltyTransactionDTO::fromModel($transaction);
        });
    }

    public function getCustomerTransactionSummary(int $customerId): array
    {
        $transactions = $this->model::byCustomer($customerId);

        return [
            'total_transactions' => $transactions->count(),
            'total_points_earned' => $transactions->earned()->sum('points'),
            'total_points_redeemed' => $transactions->redeemed()->sum('points'),
            'total_points_expired' => $transactions->expired()->sum('points'),
            'total_points_reversed' => $transactions->reversed()->sum('points'),
            'current_balance' => $this->calculateBalance($customerId),
            'current_balance_value' => $this->calculateBalanceValue($customerId),
            'first_transaction_date' => $transactions->min('created_at'),
            'last_transaction_date' => $transactions->max('created_at'),
        ];
    }

    public function getCustomerTransactionSummaryDTO(int $customerId): array
    {
        return $this->getCustomerTransactionSummary($customerId);
    }

    // Import/Export operations
    public function exportCustomerHistory(int $customerId): array
    {
        $transactions = $this->getCustomerTransactionHistory($customerId);

        return $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'customer_id' => $transaction->customer_id,
                'user_id' => $transaction->user_id,
                'transaction_type' => $transaction->transaction_type->value,
                'points' => $transaction->points,
                'points_value' => $transaction->points_value,
                'reference_type' => $transaction->reference_type->value,
                'reference_id' => $transaction->reference_id,
                'description' => $transaction->description,
                'reason' => $transaction->reason,
                'status' => $transaction->status->value,
                'expires_at' => $transaction->expires_at?->toISOString(),
                'reversed_at' => $transaction->reversed_at?->toISOString(),
                'reversed_by' => $transaction->reversed_by,
                'metadata' => $transaction->metadata,
                'created_at' => $transaction->created_at->toISOString(),
                'updated_at' => $transaction->updated_at->toISOString(),
            ];
        })->toArray();
    }

    public function importCustomerHistory(int $customerId, array $transactions): bool
    {
        return DB::transaction(function () use ($customerId, $transactions) {
            foreach ($transactions as $transactionData) {
                $transactionData['customer_id'] = $customerId;
                $this->create($transactionData);
            }

            return true;
        });
    }

    // Analytics and insights
    public function getTransactionAnalytics(int $customerId): array
    {
        $transactions = $this->model::byCustomer($customerId);

        return [
            'total_transactions' => $transactions->count(),
            'points_earned' => $transactions->earned()->sum('points'),
            'points_redeemed' => $transactions->redeemed()->sum('points'),
            'points_expired' => $transactions->expired()->sum('points'),
            'points_reversed' => $transactions->reversed()->sum('points'),
            'current_balance' => $this->calculateBalance($customerId),
            'average_points_per_transaction' => $transactions->avg('points'),
            'most_common_type' => $transactions->selectRaw('transaction_type, COUNT(*) as count')
                ->groupBy('transaction_type')
                ->orderBy('count', 'desc')
                ->first(),
        ];
    }

    public function getTransactionAnalyticsByType(LoyaltyTransactionType $type): array
    {
        $transactions = $this->model::byType($type);

        return [
            'total_transactions' => $transactions->count(),
            'total_points' => $transactions->sum('points'),
            'average_points' => $transactions->avg('points'),
            'total_value' => $transactions->sum('points_value'),
            'average_value' => $transactions->avg('points_value'),
        ];
    }

    public function getTransactionAnalyticsByDateRange(string $startDate, string $endDate): array
    {
        $transactions = $this->model::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_transactions' => $transactions->count(),
            'total_points' => $transactions->sum('points'),
            'average_points' => $transactions->avg('points'),
            'total_value' => $transactions->sum('points_value'),
            'average_value' => $transactions->avg('points_value'),
        ];
    }

    public function getTransactionRecommendations(int $customerId): array
    {
        $balance = $this->calculateBalance($customerId);
        $history = $this->getCustomerTransactionHistory($customerId);

        $recommendations = [];

        if ($balance > 1000) {
            $recommendations[] = 'You have enough points to redeem for rewards!';
        }

        if ($history->count() < 5) {
            $recommendations[] = 'Make more purchases to earn more loyalty points!';
        }

        return $recommendations;
    }

    public function getTransactionInsights(int $customerId): array
    {
        $history = $this->getCustomerTransactionHistory($customerId);

        return [
            'total_transactions' => $history->count(),
            'average_points_per_transaction' => $history->avg('points'),
            'most_active_month' => $history->groupBy(function ($transaction) {
                return $transaction->created_at->format('Y-m');
            })->sortByDesc(function ($group) {
                return $group->count();
            })->keys()->first(),
        ];
    }

    public function getTransactionTrends(int $customerId, string $period = 'monthly'): array
    {
        $transactions = $this->model::byCustomer($customerId);

        $groupBy = match ($period) {
            'daily' => 'Y-m-d',
            'weekly' => 'Y-W',
            'monthly' => 'Y-m',
            'yearly' => 'Y',
            default => 'Y-m',
        };

        return $transactions->get()->groupBy(function ($transaction) use ($groupBy) {
            return $transaction->created_at->format($groupBy);
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'points' => $group->sum('points'),
                'value' => $group->sum('points_value'),
            ];
        })->toArray();
    }

    public function getTransactionComparison(int $customerId1, int $customerId2): array
    {
        $customer1 = $this->getCustomerTransactionSummary($customerId1);
        $customer2 = $this->getCustomerTransactionSummary($customerId2);

        return [
            'customer_1' => $customer1,
            'customer_2' => $customer2,
            'comparison' => [
                'balance_difference' => $customer1['current_balance'] - $customer2['current_balance'],
                'transaction_count_difference' => $customer1['total_transactions'] - $customer2['total_transactions'],
                'points_earned_difference' => $customer1['total_points_earned'] - $customer2['total_points_earned'],
            ],
        ];
    }

    public function getTransactionForecast(int $customerId): array
    {
        $history = $this->getCustomerTransactionHistory($customerId);
        $averagePointsPerMonth = $history->avg('points') * $history->count() / 12;

        return [
            'projected_points_next_month' => $averagePointsPerMonth,
            'projected_balance_next_month' => $this->calculateBalance($customerId) + $averagePointsPerMonth,
            'confidence_level' => 'medium',
        ];
    }

    // Utility methods
    public function calculatePointsValue(int $points): float
    {
        return $points * 0.01; // 1 point = $0.01
    }

    public function generateRecommendations(): array
    {
        return [
            'Increase customer engagement through targeted promotions',
            'Implement tier-based loyalty system',
            'Offer bonus points for referrals',
            'Create seasonal point multipliers',
        ];
    }

    public function calculateInsights(): array
    {
        return [
            'total_customers' => $this->model::distinct('customer_id')->count(),
            'total_points_issued' => $this->getTotalPointsIssued(),
            'total_points_redeemed' => $this->getTotalPointsRedeemed(),
            'redemption_rate' => $this->getTotalPointsRedeemed() / max($this->getTotalPointsIssued(), 1) * 100,
        ];
    }

    public function forecastTrends(string $period = 'monthly'): array
    {
        $transactions = $this->model::all();

        $groupBy = match ($period) {
            'daily' => 'Y-m-d',
            'weekly' => 'Y-W',
            'monthly' => 'Y-m',
            'yearly' => 'Y',
            default => 'Y-m',
        };

        return $transactions->groupBy(function ($transaction) use ($groupBy) {
            return $transaction->created_at->format($groupBy);
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'points' => $group->sum('points'),
                'value' => $group->sum('points_value'),
            ];
        })->toArray();
    }
}
