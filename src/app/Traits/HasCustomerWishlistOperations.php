<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\DTOs\CustomerWishlistDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

trait HasCustomerWishlistOperations
{
    /**
     * Add a product to customer's wishlist
     */
    public function addToWishlist(int $customerId, int $productId, array $data = []): ?CustomerWishlistDTO
    {
        try {
            return $this->repository->addToWishlistDTO($customerId, $productId, $data);
        } catch (\Exception $e) {
            Log::error('Failed to add product to wishlist', [
                'customer_id' => $customerId,
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Remove a product from customer's wishlist
     */
    public function removeFromWishlist(int $customerId, int $productId): bool
    {
        try {
            return $this->repository->removeFromWishlist($customerId, $productId);
        } catch (\Exception $e) {
            Log::error('Failed to remove product from wishlist', [
                'customer_id' => $customerId,
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if product is in customer's wishlist
     */
    public function isInWishlist(int $customerId, int $productId): bool
    {
        return $this->repository->isInWishlist($customerId, $productId);
    }

    /**
     * Get customer's wishlist
     */
    public function getCustomerWishlist(int $customerId): Collection
    {
        return $this->repository->findByCustomerIdDTO($customerId);
    }

    /**
     * Get customer's wishlist by priority
     */
    public function getCustomerWishlistByPriority(int $customerId): Collection
    {
        return $this->repository->getWishlistByPriorityDTO($customerId);
    }

    /**
     * Get customer's wishlist by date added
     */
    public function getCustomerWishlistByDateAdded(int $customerId, string $order = 'desc'): Collection
    {
        return $this->repository->getWishlistByDateAddedDTO($customerId, $order);
    }

    /**
     * Get customer's wishlist by price
     */
    public function getCustomerWishlistByPrice(int $customerId, string $order = 'desc'): Collection
    {
        return $this->repository->getWishlistByPriceDTO($customerId, $order);
    }

    /**
     * Search customer's wishlist
     */
    public function searchCustomerWishlist(int $customerId, string $query): Collection
    {
        return $this->repository->searchByCustomerDTO($customerId, $query);
    }

    /**
     * Make wishlist item public
     */
    public function makeWishlistPublic(int $wishlistId): bool
    {
        try {
            $wishlist = $this->repository->find($wishlistId);

            return $wishlist ? $this->repository->makePublic($wishlist) : false;
        } catch (\Exception $e) {
            Log::error('Failed to make wishlist public', [
                'wishlist_id' => $wishlistId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Make wishlist item private
     */
    public function makeWishlistPrivate(int $wishlistId): bool
    {
        try {
            $wishlist = $this->repository->find($wishlistId);

            return $wishlist ? $this->repository->makePrivate($wishlist) : false;
        } catch (\Exception $e) {
            Log::error('Failed to make wishlist private', [
                'wishlist_id' => $wishlistId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Set wishlist priority
     */
    public function setWishlistPriority(int $wishlistId, string $priority): bool
    {
        try {
            $wishlist = $this->repository->find($wishlistId);

            return $wishlist ? $this->repository->setPriority($wishlist, $priority) : false;
        } catch (\Exception $e) {
            Log::error('Failed to set wishlist priority', [
                'wishlist_id' => $wishlistId,
                'priority' => $priority,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Update wishlist notes
     */
    public function updateWishlistNotes(int $wishlistId, string $notes): ?CustomerWishlistDTO
    {
        try {
            $wishlist = $this->repository->find($wishlistId);

            return $wishlist ? $this->repository->updateAndReturnDTO($wishlist, ['notes' => $notes]) : null;
        } catch (\Exception $e) {
            Log::error('Failed to update wishlist notes', [
                'wishlist_id' => $wishlistId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Update current price for wishlist item
     */
    public function updateWishlistCurrentPrice(int $wishlistId, float $currentPrice): bool
    {
        try {
            $wishlist = $this->repository->find($wishlistId);

            return $wishlist ? $this->repository->updateCurrentPrice($wishlist, $currentPrice) : false;
        } catch (\Exception $e) {
            Log::error('Failed to update wishlist current price', [
                'wishlist_id' => $wishlistId,
                'current_price' => $currentPrice,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check for price drops in wishlist
     */
    public function checkWishlistPriceDrops(int $customerId): Collection
    {
        return $this->repository->findWithPriceDropsDTO()
            ->filter(function ($wishlist) use ($customerId) {
                return $wishlist->customer_id === $customerId;
            });
    }

    /**
     * Mark wishlist item as notified
     */
    public function markWishlistAsNotified(int $wishlistId): bool
    {
        try {
            $wishlist = $this->repository->find($wishlistId);

            return $wishlist ? $this->repository->markAsNotified($wishlist) : false;
        } catch (\Exception $e) {
            Log::error('Failed to mark wishlist as notified', [
                'wishlist_id' => $wishlistId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark wishlist item as not notified
     */
    public function markWishlistAsNotNotified(int $wishlistId): bool
    {
        try {
            $wishlist = $this->repository->find($wishlistId);

            return $wishlist ? $this->repository->markAsNotNotified($wishlist) : false;
        } catch (\Exception $e) {
            Log::error('Failed to mark wishlist as not notified', [
                'wishlist_id' => $wishlistId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Clear customer's wishlist
     */
    public function clearCustomerWishlist(int $customerId): bool
    {
        try {
            return $this->repository->clearWishlist($customerId);
        } catch (\Exception $e) {
            Log::error('Failed to clear customer wishlist', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Export customer's wishlist
     */
    public function exportCustomerWishlist(int $customerId): array
    {
        try {
            return $this->repository->exportWishlist($customerId);
        } catch (\Exception $e) {
            Log::error('Failed to export customer wishlist', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Import wishlist items to customer
     */
    public function importCustomerWishlist(int $customerId, array $wishlistItems): bool
    {
        try {
            return $this->repository->importWishlist($customerId, $wishlistItems);
        } catch (\Exception $e) {
            Log::error('Failed to import customer wishlist', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Duplicate wishlist from one customer to another
     */
    public function duplicateCustomerWishlist(int $sourceCustomerId, int $targetCustomerId): bool
    {
        try {
            return $this->repository->duplicateWishlist($sourceCustomerId, $targetCustomerId);
        } catch (\Exception $e) {
            Log::error('Failed to duplicate customer wishlist', [
                'source_customer_id' => $sourceCustomerId,
                'target_customer_id' => $targetCustomerId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get wishlist recommendations for customer
     */
    public function getWishlistRecommendations(int $customerId, int $limit = 10): Collection
    {
        return $this->repository->getWishlistRecommendationsDTO($customerId, $limit);
    }

    /**
     * Get similar wishlists for customer
     */
    public function getSimilarWishlists(int $customerId, int $limit = 10): Collection
    {
        return $this->repository->getSimilarWishlistsDTO($customerId, $limit);
    }

    /**
     * Get wishlist analytics for customer
     */
    public function getWishlistAnalytics(int $customerId): array
    {
        return $this->repository->getWishlistAnalytics($customerId);
    }

    /**
     * Get wishlist statistics
     */
    public function getWishlistStats(): array
    {
        return $this->repository->getWishlistStats();
    }

    /**
     * Get wishlist statistics by customer
     */
    public function getWishlistStatsByCustomer(int $customerId): array
    {
        return $this->repository->getWishlistStatsByCustomer($customerId);
    }

    /**
     * Get price drop statistics
     */
    public function getPriceDropStats(): array
    {
        return $this->repository->getPriceDropStats();
    }

    /**
     * Get price drop statistics by customer
     */
    public function getPriceDropStatsByCustomer(int $customerId): array
    {
        return $this->repository->getPriceDropStatsByCustomer($customerId);
    }

    /**
     * Get most wishlisted products
     */
    public function getMostWishlistedProducts(int $limit = 10): Collection
    {
        return $this->repository->getMostWishlistedProductsDTO($limit);
    }

    /**
     * Get most wishlisted products by category
     */
    public function getMostWishlistedProductsByCategory(int $categoryId, int $limit = 10): Collection
    {
        return $this->repository->getMostWishlistedProductsByCategoryDTO($categoryId, $limit);
    }

    /**
     * Get recent wishlist additions
     */
    public function getRecentWishlistAdditions(int $limit = 10): Collection
    {
        return $this->repository->getRecentWishlistAdditionsDTO($limit);
    }

    /**
     * Get recent wishlist additions by customer
     */
    public function getRecentWishlistAdditionsByCustomer(int $customerId, int $limit = 10): Collection
    {
        return $this->repository->getRecentWishlistAdditionsByCustomerDTO($customerId, $limit);
    }

    /**
     * Validate wishlist data
     */
    public function validateWishlistData(array $data): bool
    {
        return $this->repository->validateWishlist($data);
    }

    /**
     * Get wishlist count by customer
     */
    public function getWishlistCountByCustomer(int $customerId): int
    {
        return $this->repository->getWishlistCountByCustomer($customerId);
    }

    /**
     * Get total wishlist value by customer
     */
    public function getTotalWishlistValueByCustomer(int $customerId): float
    {
        return $this->repository->getTotalWishlistValueByCustomer($customerId);
    }

    /**
     * Get average wishlist value by customer
     */
    public function getAverageWishlistValueByCustomer(int $customerId): float
    {
        return $this->repository->getAverageWishlistValueByCustomer($customerId);
    }

    /**
     * Get wishlist growth stats by customer
     */
    public function getWishlistGrowthStatsByCustomer(int $customerId, string $period = 'monthly'): array
    {
        return $this->repository->getWishlistGrowthStatsByCustomer($customerId, $period);
    }

    /**
     * Get wishlist analytics by product
     */
    public function getWishlistAnalyticsByProduct(int $productId): array
    {
        return $this->repository->getWishlistAnalyticsByProduct($productId);
    }

    /**
     * Get wishlist analytics by category
     */
    public function getWishlistAnalyticsByCategory(int $categoryId): array
    {
        return $this->repository->getWishlistAnalyticsByCategory($categoryId);
    }
}
