<?php

namespace App\Repositories;

use App\DTOs\ProviderRatingDTO;
use App\Enums\RatingStatus;
use App\Models\ProviderRating;
use App\Repositories\Interfaces\ProviderRatingRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProviderRatingRepository implements ProviderRatingRepositoryInterface
{
    protected $model;

    protected $cachePrefix = 'provider_rating_';

    protected $cacheTtl = 3600; // 1 hour

    public function __construct(ProviderRating $model)
    {
        $this->model = $model;
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return Cache::remember($this->cachePrefix.'all', $this->cacheTtl, function () {
            return $this->model->with(['provider', 'user'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->with(['provider', 'user'])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    // Find operations
    public function find(int $id): ?ProviderRating
    {
        return Cache::remember($this->cachePrefix.'find_'.$id, $this->cacheTtl, function () use ($id) {
            return $this->model->with(['provider', 'user'])->find($id);
        });
    }

    public function findDTO(int $id): ?ProviderRatingDTO
    {
        $rating = $this->find($id);

        return $rating ? ProviderRatingDTO::fromModel($rating) : null;
    }

    public function findByProviderId(int $providerId): Collection
    {
        return Cache::remember($this->cachePrefix.'provider_'.$providerId, $this->cacheTtl, function () use ($providerId) {
            return $this->model->with(['provider', 'user'])
                ->where('provider_id', $providerId)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        $ratings = $this->findByProviderId($providerId);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findByUserId(int $userId): Collection
    {
        return Cache::remember($this->cachePrefix.'user_'.$userId, $this->cacheTtl, function () use ($userId) {
            return $this->model->with(['provider', 'user'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByUserIdDTO(int $userId): Collection
    {
        $ratings = $this->findByUserId($userId);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findByCategory(string $category): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->where('category', $category)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByCategoryDTO(string $category): Collection
    {
        $ratings = $this->findByCategory($category);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        $ratings = $this->findByStatus($status);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findByRatingValue(float $ratingValue): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->where('rating_value', $ratingValue)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByRatingValueDTO(float $ratingValue): Collection
    {
        $ratings = $this->findByRatingValue($ratingValue);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findByRatingRange(float $minRating, float $maxRating): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->whereBetween('rating_value', [$minRating, $maxRating])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByRatingRangeDTO(float $minRating, float $maxRating): Collection
    {
        $ratings = $this->findByRatingRange($minRating, $maxRating);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findByProviderAndCategory(int $providerId, string $category): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->where('provider_id', $providerId)
            ->where('category', $category)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByProviderAndCategoryDTO(int $providerId, string $category): Collection
    {
        $ratings = $this->findByProviderAndCategory($providerId, $category);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findByProviderAndUser(int $providerId, int $userId): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->where('provider_id', $providerId)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByProviderAndUserDTO(int $providerId, int $userId): Collection
    {
        $ratings = $this->findByProviderAndUser($providerId, $userId);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    // Status-based queries
    public function findVerified(): Collection
    {
        return $this->findByStatus(RatingStatus::VERIFIED->value);
    }

    public function findVerifiedDTO(): Collection
    {
        $ratings = $this->findVerified();

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findUnverified(): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->where('is_verified', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findUnverifiedDTO(): Collection
    {
        $ratings = $this->findUnverified();

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findApproved(): Collection
    {
        return $this->findByStatus(RatingStatus::APPROVED->value);
    }

    public function findApprovedDTO(): Collection
    {
        $ratings = $this->findApproved();

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findPending(): Collection
    {
        return $this->findByStatus(RatingStatus::PENDING->value);
    }

    public function findPendingDTO(): Collection
    {
        $ratings = $this->findPending();

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findFlagged(): Collection
    {
        return $this->findByStatus(RatingStatus::FLAGGED->value);
    }

    public function findFlaggedDTO(): Collection
    {
        $ratings = $this->findFlagged();

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    // Recommendation queries
    public function findRecommended(): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->where('would_recommend', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findRecommendedDTO(): Collection
    {
        $ratings = $this->findRecommended();

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function findNotRecommended(): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->where('would_recommend', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findNotRecommendedDTO(): Collection
    {
        $ratings = $this->findNotRecommended();

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    // Date-based queries
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $ratings = $this->findByDateRange($startDate, $endDate);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    // Create, Update, Delete operations
    public function create(array $data): ProviderRating
    {
        try {
            DB::beginTransaction();

            $rating = $this->model->create($data);

            // Clear relevant caches
            $this->clearProviderCache($rating->provider_id);
            $this->clearUserCache($rating->user_id);
            $this->clearGeneralCache();

            DB::commit();

            return $rating;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider rating: '.$e->getMessage());
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): ProviderRatingDTO
    {
        $rating = $this->create($data);

        return ProviderRatingDTO::fromModel($rating);
    }

    public function update(ProviderRating $rating, array $data): bool
    {
        try {
            DB::beginTransaction();

            $result = $rating->update($data);

            if ($result) {
                // Clear relevant caches
                $this->clearProviderCache($rating->provider_id);
                $this->clearUserCache($rating->user_id);
                $this->clearGeneralCache();
            }

            DB::commit();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider rating: '.$e->getMessage());
            throw $e;
        }
    }

    public function updateAndReturnDTO(ProviderRating $rating, array $data): ?ProviderRatingDTO
    {
        $result = $this->update($rating, $data);

        return $result ? ProviderRatingDTO::fromModel($rating->fresh()) : null;
    }

    public function delete(ProviderRating $rating): bool
    {
        try {
            DB::beginTransaction();

            $providerId = $rating->provider_id;
            $userId = $rating->user_id;

            $result = $rating->delete();

            if ($result) {
                // Clear relevant caches
                $this->clearProviderCache($providerId);
                $this->clearUserCache($userId);
                $this->clearGeneralCache();
            }

            DB::commit();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete provider rating: '.$e->getMessage());
            throw $e;
        }
    }

    // Moderation operations
    public function approve(ProviderRating $rating): bool
    {
        return $this->update($rating, ['status' => RatingStatus::APPROVED->value]);
    }

    public function reject(ProviderRating $rating, ?string $reason = null): bool
    {
        $data = ['status' => RatingStatus::REJECTED->value];
        if ($reason) {
            $data['rejection_reason'] = $reason;
        }

        return $this->update($rating, $data);
    }

    public function flag(ProviderRating $rating, ?string $reason = null): bool
    {
        $data = ['status' => RatingStatus::FLAGGED->value];
        if ($reason) {
            $data['flag_reason'] = $reason;
        }

        return $this->update($rating, $data);
    }

    public function verify(ProviderRating $rating): bool
    {
        return $this->update($rating, ['is_verified' => true]);
    }

    // Voting operations
    public function addHelpfulVote(ProviderRating $rating, int $userId): bool
    {
        try {
            $rating->increment('helpful_votes');
            $rating->increment('total_votes');
            $this->clearProviderCache($rating->provider_id);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to add helpful vote: '.$e->getMessage());

            return false;
        }
    }

    public function removeHelpfulVote(ProviderRating $rating, int $userId): bool
    {
        try {
            if ($rating->helpful_votes > 0) {
                $rating->decrement('helpful_votes');
            }
            if ($rating->total_votes > 0) {
                $rating->decrement('total_votes');
            }
            $this->clearProviderCache($rating->provider_id);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to remove helpful vote: '.$e->getMessage());

            return false;
        }
    }

    // Analytics and statistics
    public function getProviderAverageRating(int $providerId, ?string $category = null): float
    {
        $cacheKey = $this->cachePrefix.'avg_'.$providerId.'_'.($category ?? 'all');

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId, $category) {
            $query = $this->model->where('provider_id', $providerId)
                ->where('status', RatingStatus::APPROVED->value);

            if ($category) {
                $query->where('category', $category);
            }

            return $query->avg('rating_value') ?? 0.0;
        });
    }

    public function getProviderRatingCount(int $providerId, ?string $category = null): int
    {
        $cacheKey = $this->cachePrefix.'count_'.$providerId.'_'.($category ?? 'all');

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId, $category) {
            $query = $this->model->where('provider_id', $providerId)
                ->where('status', RatingStatus::APPROVED->value);

            if ($category) {
                $query->where('category', $category);
            }

            return $query->count();
        });
    }

    public function getProviderRatingBreakdown(int $providerId): array
    {
        $cacheKey = $this->cachePrefix.'breakdown_'.$providerId;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId) {
            return $this->model->where('provider_id', $providerId)
                ->where('status', RatingStatus::APPROVED->value)
                ->selectRaw('rating_value, COUNT(*) as count')
                ->groupBy('rating_value')
                ->orderBy('rating_value')
                ->pluck('count', 'rating_value')
                ->toArray();
        });
    }

    public function getProviderRecommendationPercentage(int $providerId): float
    {
        $cacheKey = $this->cachePrefix.'recommend_'.$providerId;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId) {
            $total = $this->model->where('provider_id', $providerId)
                ->where('status', RatingStatus::APPROVED->value)
                ->count();

            if ($total === 0) {
                return 0.0;
            }

            $recommended = $this->model->where('provider_id', $providerId)
                ->where('status', RatingStatus::APPROVED->value)
                ->where('would_recommend', true)
                ->count();

            return round(($recommended / $total) * 100, 2);
        });
    }

    public function getUserRatingCount(int $userId): int
    {
        return Cache::remember($this->cachePrefix.'user_count_'.$userId, $this->cacheTtl, function () use ($userId) {
            return $this->model->where('user_id', $userId)->count();
        });
    }

    public function getUserAverageRating(int $userId): float
    {
        return Cache::remember($this->cachePrefix.'user_avg_'.$userId, $this->cacheTtl, function () use ($userId) {
            return $this->model->where('user_id', $userId)
                ->where('status', RatingStatus::APPROVED->value)
                ->avg('rating_value') ?? 0.0;
        });
    }

    public function getTotalRatingCount(): int
    {
        return Cache::remember($this->cachePrefix.'total_count', $this->cacheTtl, function () {
            return $this->model->count();
        });
    }

    public function getAverageRating(): float
    {
        return Cache::remember($this->cachePrefix.'total_avg', $this->cacheTtl, function () {
            return $this->model->where('status', RatingStatus::APPROVED->value)
                ->avg('rating_value') ?? 0.0;
        });
    }

    public function getRatingCountByValue(float $ratingValue): int
    {
        return $this->model->where('rating_value', $ratingValue)
            ->where('status', RatingStatus::APPROVED->value)
            ->count();
    }

    public function getRatingCountByCategory(string $category): int
    {
        return $this->model->where('category', $category)
            ->where('status', RatingStatus::APPROVED->value)
            ->count();
    }

    public function getVerifiedRatingCount(): int
    {
        return Cache::remember($this->cachePrefix.'verified_count', $this->cacheTtl, function () {
            return $this->model->where('is_verified', true)->count();
        });
    }

    public function getPendingRatingCount(): int
    {
        return Cache::remember($this->cachePrefix.'pending_count', $this->cacheTtl, function () {
            return $this->model->where('status', RatingStatus::PENDING->value)->count();
        });
    }

    public function getFlaggedRatingCount(): int
    {
        return Cache::remember($this->cachePrefix.'flagged_count', $this->cacheTtl, function () {
            return $this->model->where('status', RatingStatus::FLAGGED->value)->count();
        });
    }

    public function getRecommendationPercentage(): float
    {
        return Cache::remember($this->cachePrefix.'total_recommend', $this->cacheTtl, function () {
            $total = $this->model->where('status', RatingStatus::APPROVED->value)->count();

            if ($total === 0) {
                return 0.0;
            }

            $recommended = $this->model->where('status', RatingStatus::APPROVED->value)
                ->where('would_recommend', true)
                ->count();

            return round(($recommended / $total) * 100, 2);
        });
    }

    // Search operations
    public function searchRatings(string $query): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('comment', 'like', "%{$query}%")
                    ->orWhere('pros', 'like', "%{$query}%")
                    ->orWhere('cons', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchRatingsDTO(string $query): Collection
    {
        $ratings = $this->searchRatings($query);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function searchRatingsByProvider(int $providerId, string $query): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->where('provider_id', $providerId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('comment', 'like', "%{$query}%")
                    ->orWhere('pros', 'like', "%{$query}%")
                    ->orWhere('cons', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchRatingsByProviderDTO(int $providerId, string $query): Collection
    {
        $ratings = $this->searchRatingsByProvider($providerId, $query);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    // Import/Export operations
    public function exportRatingData(array $filters = []): string
    {
        $query = $this->model->with(['provider', 'user']);

        // Apply filters
        if (isset($filters['provider_id'])) {
            $query->where('provider_id', $filters['provider_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        $ratings = $query->get();

        // Convert to CSV format
        $csv = "ID,Provider ID,User ID,Rating Value,Category,Title,Comment,Status,Created At\n";

        foreach ($ratings as $rating) {
            $csv .= "{$rating->id},{$rating->provider_id},{$rating->user_id},{$rating->rating_value},";
            $csv .= "{$rating->category},{$rating->title},";
            $csv .= '"'.str_replace('"', '""', $rating->comment)."\",{$rating->status},{$rating->created_at}\n";
        }

        return $csv;
    }

    public function importRatingData(string $data): bool
    {
        try {
            $lines = explode("\n", trim($data));
            $headers = str_getcsv(array_shift($lines));

            DB::beginTransaction();

            foreach ($lines as $line) {
                if (empty(trim($line))) {
                    continue;
                }

                $row = array_combine($headers, str_getcsv($line));

                if (isset($row['ID']) && ! empty($row['ID'])) {
                    // Update existing
                    $rating = $this->model->find($row['ID']);
                    if ($rating) {
                        $rating->update($row);
                    }
                } else {
                    // Create new
                    unset($row['ID']);
                    $this->model->create($row);
                }
            }

            DB::commit();
            $this->clearGeneralCache();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to import rating data: '.$e->getMessage());

            return false;
        }
    }

    // Advanced analytics
    public function getRatingStatistics(): array
    {
        return Cache::remember($this->cachePrefix.'statistics', $this->cacheTtl, function () {
            return [
                'total_ratings' => $this->getTotalRatingCount(),
                'average_rating' => $this->getAverageRating(),
                'verified_ratings' => $this->getVerifiedRatingCount(),
                'pending_ratings' => $this->getPendingRatingCount(),
                'flagged_ratings' => $this->getFlaggedRatingCount(),
                'recommendation_percentage' => $this->getRecommendationPercentage(),
                'rating_breakdown' => $this->getRatingBreakdown(),
                'category_breakdown' => $this->getCategoryBreakdown(),
            ];
        });
    }

    public function getProviderRatingStatistics(int $providerId): array
    {
        $cacheKey = $this->cachePrefix.'provider_stats_'.$providerId;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId) {
            return [
                'total_ratings' => $this->getProviderRatingCount($providerId),
                'average_rating' => $this->getProviderAverageRating($providerId),
                'rating_breakdown' => $this->getProviderRatingBreakdown($providerId),
                'recommendation_percentage' => $this->getProviderRecommendationPercentage($providerId),
                'category_breakdown' => $this->getProviderCategoryBreakdown($providerId),
            ];
        });
    }

    public function getRatingTrends(?string $startDate = null, ?string $endDate = null): array
    {
        $query = $this->model->where('status', RatingStatus::APPROVED->value);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->selectRaw('DATE(created_at) as date, AVG(rating_value) as avg_rating, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    public function calculateRatingMetrics(int $providerId): array
    {
        $cacheKey = $this->cachePrefix.'metrics_'.$providerId;

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($providerId) {
            $ratings = $this->model->where('provider_id', $providerId)
                ->where('status', RatingStatus::APPROVED->value)
                ->get();

            $totalRatings = $ratings->count();
            if ($totalRatings === 0) {
                return [
                    'average_rating' => 0,
                    'total_ratings' => 0,
                    'rating_distribution' => [],
                    'category_performance' => [],
                    'trend_analysis' => [],
                ];
            }

            $averageRating = $ratings->avg('rating_value');
            $ratingDistribution = $ratings->groupBy('rating_value')
                ->map(fn ($group) => $group->count())
                ->toArray();

            $categoryPerformance = $ratings->groupBy('category')
                ->map(fn ($group) => [
                    'count' => $group->count(),
                    'average' => $group->avg('rating_value'),
                ])
                ->toArray();

            // Trend analysis (last 30 days vs previous 30 days)
            $now = now();
            $last30Days = $ratings->where('created_at', '>=', $now->subDays(30));
            $previous30Days = $ratings->whereBetween('created_at', [
                $now->subDays(60),
                $now->subDays(30),
            ]);

            $trendAnalysis = [
                'current_period' => [
                    'count' => $last30Days->count(),
                    'average' => $last30Days->avg('rating_value') ?? 0,
                ],
                'previous_period' => [
                    'count' => $previous30Days->count(),
                    'average' => $previous30Days->avg('rating_value') ?? 0,
                ],
            ];

            return [
                'average_rating' => round($averageRating, 2),
                'total_ratings' => $totalRatings,
                'rating_distribution' => $ratingDistribution,
                'category_performance' => $categoryPerformance,
                'trend_analysis' => $trendAnalysis,
            ];
        });
    }

    // Top ratings and providers
    public function getMostHelpfulRatings(int $limit = 10): Collection
    {
        return $this->model->with(['provider', 'user'])
            ->where('status', RatingStatus::APPROVED->value)
            ->orderBy('helpful_votes', 'desc')
            ->orderBy('total_votes', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMostHelpfulRatingsDTO(int $limit = 10): Collection
    {
        $ratings = $this->getMostHelpfulRatings($limit);

        return $ratings->map(fn ($rating) => ProviderRatingDTO::fromModel($rating));
    }

    public function getTopRatedProviders(int $limit = 10): Collection
    {
        return $this->model->select('provider_id')
            ->selectRaw('AVG(rating_value) as avg_rating, COUNT(*) as rating_count')
            ->where('status', RatingStatus::APPROVED->value)
            ->groupBy('provider_id')
            ->having('rating_count', '>=', 5) // Minimum 5 ratings
            ->orderBy('avg_rating', 'desc')
            ->orderBy('rating_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTopRatedProvidersDTO(int $limit = 10): Collection
    {
        return $this->getTopRatedProviders($limit);
    }

    // Helper methods
    protected function getRatingBreakdown(): array
    {
        return $this->model->where('status', RatingStatus::APPROVED->value)
            ->selectRaw('rating_value, COUNT(*) as count')
            ->groupBy('rating_value')
            ->orderBy('rating_value')
            ->pluck('count', 'rating_value')
            ->toArray();
    }

    protected function getCategoryBreakdown(): array
    {
        return $this->model->where('status', RatingStatus::APPROVED->value)
            ->selectRaw('category, COUNT(*) as count, AVG(rating_value) as avg_rating')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    protected function getProviderCategoryBreakdown(int $providerId): array
    {
        return $this->model->where('provider_id', $providerId)
            ->where('status', RatingStatus::APPROVED->value)
            ->selectRaw('category, COUNT(*) as count, AVG(rating_value) as avg_rating')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
    }

    protected function clearProviderCache(int $providerId): void
    {
        Cache::forget($this->cachePrefix.'provider_'.$providerId);
        Cache::forget($this->cachePrefix.'avg_'.$providerId.'_all');
        Cache::forget($this->cachePrefix.'count_'.$providerId.'_all');
        Cache::forget($this->cachePrefix.'breakdown_'.$providerId);
        Cache::forget($this->cachePrefix.'recommend_'.$providerId);
        Cache::forget($this->cachePrefix.'provider_stats_'.$providerId);
        Cache::forget($this->cachePrefix.'metrics_'.$providerId);
    }

    protected function clearUserCache(int $userId): void
    {
        Cache::forget($this->cachePrefix.'user_'.$userId);
        Cache::forget($this->cachePrefix.'user_count_'.$userId);
        Cache::forget($this->cachePrefix.'user_avg_'.$userId);
    }

    protected function clearGeneralCache(): void
    {
        Cache::forget($this->cachePrefix.'all');
        Cache::forget($this->cachePrefix.'total_count');
        Cache::forget($this->cachePrefix.'total_avg');
        Cache::forget($this->cachePrefix.'verified_count');
        Cache::forget($this->cachePrefix.'pending_count');
        Cache::forget($this->cachePrefix.'flagged_count');
        Cache::forget($this->cachePrefix.'total_recommend');
        Cache::forget($this->cachePrefix.'statistics');
    }
}
