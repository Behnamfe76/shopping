<?php

namespace Fereydooni\Shopping\App\Traits;

use App\DTOs\ProviderRatingDTO;
use App\Models\ProviderRating;
use App\Repositories\Interfaces\ProviderRatingRepositoryInterface;
use Exception;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

trait HasProviderRatingOperations
{
    protected ProviderRatingRepositoryInterface $providerRatingRepository;

    /**
     * Get all provider ratings
     */
    public function getAllProviderRatings(): Collection
    {
        try {
            return $this->providerRatingRepository->all();
        } catch (Exception $e) {
            Log::error('Failed to get all provider ratings', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Get paginated provider ratings
     */
    public function getPaginatedProviderRatings(int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $this->providerRatingRepository->paginate($perPage);
        } catch (Exception $e) {
            Log::error('Failed to get paginated provider ratings', ['error' => $e->getMessage()]);

            return new LengthAwarePaginator([], 0, $perPage);
        }
    }

    /**
     * Get simple paginated provider ratings
     */
    public function getSimplePaginatedProviderRatings(int $perPage = 15): Paginator
    {
        try {
            return $this->providerRatingRepository->simplePaginate($perPage);
        } catch (Exception $e) {
            Log::error('Failed to get simple paginated provider ratings', ['error' => $e->getMessage()]);

            return new Paginator([], $perPage);
        }
    }

    /**
     * Get cursor paginated provider ratings
     */
    public function getCursorPaginatedProviderRatings(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        try {
            return $this->providerRatingRepository->cursorPaginate($perPage, $cursor);
        } catch (Exception $e) {
            Log::error('Failed to get cursor paginated provider ratings', ['error' => $e->getMessage()]);

            return new CursorPaginator([], $perPage);
        }
    }

    /**
     * Find provider rating by ID
     */
    public function findProviderRating(int $id): ?ProviderRating
    {
        try {
            return $this->providerRatingRepository->find($id);
        } catch (Exception $e) {
            Log::error('Failed to find provider rating', ['id' => $id, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Find provider rating by ID and return DTO
     */
    public function findProviderRatingDTO(int $id): ?ProviderRatingDTO
    {
        try {
            return $this->providerRatingRepository->findDTO($id);
        } catch (Exception $e) {
            Log::error('Failed to find provider rating DTO', ['id' => $id, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Find ratings by provider ID
     */
    public function findProviderRatingsByProvider(int $providerId): Collection
    {
        try {
            return $this->providerRatingRepository->findByProviderId($providerId);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings by provider', ['provider_id' => $providerId, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Find ratings by provider ID and return DTOs
     */
    public function findProviderRatingsByProviderDTO(int $providerId): Collection
    {
        try {
            return $this->providerRatingRepository->findByProviderIdDTO($providerId);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings DTOs by provider', ['provider_id' => $providerId, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Find ratings by user ID
     */
    public function findProviderRatingsByUser(int $userId): Collection
    {
        try {
            return $this->providerRatingRepository->findByUserId($userId);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings by user', ['user_id' => $userId, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Find ratings by user ID and return DTOs
     */
    public function findProviderRatingsByUserDTO(int $userId): Collection
    {
        try {
            return $this->providerRatingRepository->findByUserIdDTO($userId);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings DTOs by user', ['user_id' => $userId, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Find ratings by category
     */
    public function findProviderRatingsByCategory(string $category): Collection
    {
        try {
            return $this->providerRatingRepository->findByCategory($category);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings by category', ['category' => $category, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Find ratings by category and return DTOs
     */
    public function findProviderRatingsByCategoryDTO(string $category): Collection
    {
        try {
            return $this->providerRatingRepository->findByCategoryDTO($category);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings DTOs by category', ['category' => $category, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Find ratings by status
     */
    public function findProviderRatingsByStatus(string $status): Collection
    {
        try {
            return $this->providerRatingRepository->findByStatus($status);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings by status', ['status' => $status, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Find ratings by status and return DTOs
     */
    public function findProviderRatingsByStatusDTO(string $status): Collection
    {
        try {
            return $this->providerRatingRepository->findByStatusDTO($status);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings DTOs by status', ['status' => $status, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Find ratings by rating value
     */
    public function findProviderRatingsByValue(float $ratingValue): Collection
    {
        try {
            return $this->providerRatingRepository->findByRatingValue($ratingValue);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings by value', ['rating_value' => $ratingValue, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Find ratings by rating range
     */
    public function findProviderRatingsByRange(float $minRating, float $maxRating): Collection
    {
        try {
            return $this->providerRatingRepository->findByRatingRange($minRating, $maxRating);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings by range', ['min' => $minRating, 'max' => $maxRating, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Find ratings by provider and category
     */
    public function findProviderRatingsByProviderAndCategory(int $providerId, string $category): Collection
    {
        try {
            return $this->providerRatingRepository->findByProviderAndCategory($providerId, $category);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings by provider and category', ['provider_id' => $providerId, 'category' => $category, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Find ratings by provider and user
     */
    public function findProviderRatingsByProviderAndUser(int $providerId, int $userId): Collection
    {
        try {
            return $this->providerRatingRepository->findByProviderAndUser($providerId, $userId);
        } catch (Exception $e) {
            Log::error('Failed to find provider ratings by provider and user', ['provider_id' => $providerId, 'user_id' => $userId, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Create a new provider rating
     */
    public function createProviderRating(array $data): ?ProviderRating
    {
        try {
            return $this->providerRatingRepository->create($data);
        } catch (Exception $e) {
            Log::error('Failed to create provider rating', ['data' => $data, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Create a new provider rating and return DTO
     */
    public function createProviderRatingAndReturnDTO(array $data): ?ProviderRatingDTO
    {
        try {
            return $this->providerRatingRepository->createAndReturnDTO($data);
        } catch (Exception $e) {
            Log::error('Failed to create provider rating DTO', ['data' => $data, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Update a provider rating
     */
    public function updateProviderRating(ProviderRating $rating, array $data): bool
    {
        try {
            return $this->providerRatingRepository->update($rating, $data);
        } catch (Exception $e) {
            Log::error('Failed to update provider rating', ['rating_id' => $rating->id, 'data' => $data, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Update a provider rating and return DTO
     */
    public function updateProviderRatingAndReturnDTO(ProviderRating $rating, array $data): ?ProviderRatingDTO
    {
        try {
            return $this->providerRatingRepository->updateAndReturnDTO($rating, $data);
        } catch (Exception $e) {
            Log::error('Failed to update provider rating DTO', ['rating_id' => $rating->id, 'data' => $data, 'error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Delete a provider rating
     */
    public function deleteProviderRating(ProviderRating $rating): bool
    {
        try {
            return $this->providerRatingRepository->delete($rating);
        } catch (Exception $e) {
            Log::error('Failed to delete provider rating', ['rating_id' => $rating->id, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Search provider ratings
     */
    public function searchProviderRatings(string $query): Collection
    {
        try {
            return $this->providerRatingRepository->searchRatings($query);
        } catch (Exception $e) {
            Log::error('Failed to search provider ratings', ['query' => $query, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Search provider ratings and return DTOs
     */
    public function searchProviderRatingsDTO(string $query): Collection
    {
        try {
            return $this->providerRatingRepository->searchRatingsDTO($query);
        } catch (Exception $e) {
            Log::error('Failed to search provider ratings DTOs', ['query' => $query, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Search ratings by provider
     */
    public function searchProviderRatingsByProvider(int $providerId, string $query): Collection
    {
        try {
            return $this->providerRatingRepository->searchRatingsByProvider($providerId, $query);
        } catch (Exception $e) {
            Log::error('Failed to search provider ratings by provider', ['provider_id' => $providerId, 'query' => $query, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Search ratings by provider and return DTOs
     */
    public function searchProviderRatingsByProviderDTO(int $providerId, string $query): Collection
    {
        try {
            return $this->providerRatingRepository->searchRatingsByProviderDTO($providerId, $query);
        } catch (Exception $e) {
            Log::error('Failed to search provider ratings DTOs by provider', ['provider_id' => $providerId, 'query' => $query, 'error' => $e->getMessage()]);

            return collect();
        }
    }

    /**
     * Get total rating count
     */
    public function getTotalProviderRatingCount(): int
    {
        try {
            return $this->providerRatingRepository->getTotalRatingCount();
        } catch (Exception $e) {
            Log::error('Failed to get total provider rating count', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    /**
     * Get average rating
     */
    public function getAverageProviderRating(): float
    {
        try {
            return $this->providerRatingRepository->getAverageRating();
        } catch (Exception $e) {
            Log::error('Failed to get average provider rating', ['error' => $e->getMessage()]);

            return 0.0;
        }
    }

    /**
     * Get rating count by value
     */
    public function getProviderRatingCountByValue(float $ratingValue): int
    {
        try {
            return $this->providerRatingRepository->getRatingCountByValue($ratingValue);
        } catch (Exception $e) {
            Log::error('Failed to get provider rating count by value', ['rating_value' => $ratingValue, 'error' => $e->getMessage()]);

            return 0;
        }
    }

    /**
     * Get rating count by category
     */
    public function getProviderRatingCountByCategory(string $category): int
    {
        try {
            return $this->providerRatingRepository->getRatingCountByCategory($category);
        } catch (Exception $e) {
            Log::error('Failed to get provider rating count by category', ['category' => $category, 'error' => $e->getMessage()]);

            return 0;
        }
    }

    /**
     * Get recommendation percentage
     */
    public function getProviderRecommendationPercentage(): float
    {
        try {
            return $this->providerRatingRepository->getRecommendationPercentage();
        } catch (Exception $e) {
            Log::error('Failed to get provider recommendation percentage', ['error' => $e->getMessage()]);

            return 0.0;
        }
    }

    /**
     * Export rating data
     */
    public function exportProviderRatingData(array $filters = []): string
    {
        try {
            return $this->providerRatingRepository->exportRatingData($filters);
        } catch (Exception $e) {
            Log::error('Failed to export provider rating data', ['filters' => $filters, 'error' => $e->getMessage()]);

            return '';
        }
    }

    /**
     * Import rating data
     */
    public function importProviderRatingData(string $data): bool
    {
        try {
            return $this->providerRatingRepository->importRatingData($data);
        } catch (Exception $e) {
            Log::error('Failed to import provider rating data', ['data' => $data, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
