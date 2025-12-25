<?php

namespace Fereydooni\Shopping\app\Services;

use Fereydooni\Shopping\app\DTOs\CustomerWishlistDTO;
use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Fereydooni\Shopping\app\Repositories\Interfaces\CustomerWishlistRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasCustomerWishlistOperations;
use Fereydooni\Shopping\app\Traits\HasCustomerWishlistStatusManagement;
use Fereydooni\Shopping\app\Traits\HasNotesManagement;
use Fereydooni\Shopping\app\Traits\HasSearchOperations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class CustomerWishlistService
{
    use HasCrudOperations,
        HasCustomerWishlistOperations,
        HasCustomerWishlistStatusManagement,
        HasNotesManagement,
        HasSearchOperations;

    public function __construct(
        private CustomerWishlistRepositoryInterface $repository
    ) {
        $this->model = CustomerWishlist::class;
        $this->dtoClass = CustomerWishlistDTO::class;
    }

    // Customer wishlist-specific methods that extend the traits

    /**
     * Add product to customer's wishlist with price tracking
     */
    public function addProductToWishlist(int $customerId, int $productId, array $data = []): ?CustomerWishlistDTO
    {
        try {
            // Get current product price if not provided
            if (! isset($data['price_when_added'])) {
                $product = app(\Fereydooni\Shopping\app\Models\Product::class)->find($productId);
                if ($product && $product->price) {
                    $data['price_when_added'] = $product->price;
                    $data['current_price'] = $product->price;
                }
            }

            $wishlist = $this->addToWishlist($customerId, $productId, $data);

            if ($wishlist) {
                // Trigger event for product added to wishlist
                event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\ProductAddedToWishlist($wishlist));
            }

            return $wishlist;
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
     * Remove product from customer's wishlist
     */
    public function removeProductFromWishlist(int $customerId, int $productId): bool
    {
        try {
            $wishlist = $this->repository->findByCustomerAndProduct($customerId, $productId);

            if ($wishlist) {
                $result = $this->removeFromWishlist($customerId, $productId);

                if ($result) {
                    // Trigger event for product removed from wishlist
                    event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\ProductRemovedFromWishlist($wishlist));
                }

                return $result;
            }

            return false;
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
     * Update wishlist item with comprehensive data
     */
    public function updateWishlistItem(int $wishlistId, array $data): ?CustomerWishlistDTO
    {
        try {
            $wishlist = $this->repository->find($wishlistId);

            if (! $wishlist) {
                return null;
            }

            // Validate status changes
            if (! $this->validateWishlistStatusChange($wishlist, $data)) {
                return null;
            }

            $updatedWishlist = $this->updateCustomerWishlist($wishlist, $data);

            if ($updatedWishlist) {
                // Trigger event for wishlist updated
                event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\CustomerWishlistUpdated($wishlist));
            }

            return $updatedWishlist;
        } catch (\Exception $e) {
            Log::error('Failed to update wishlist item', [
                'wishlist_id' => $wishlistId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get customer's wishlist with filtering options
     */
    public function getCustomerWishlistWithFilters(int $customerId, array $filters = []): Collection
    {
        try {
            $wishlists = $this->getCustomerWishlist($customerId);

            // Apply filters
            if (isset($filters['priority'])) {
                $wishlists = $wishlists->filter(function ($wishlist) use ($filters) {
                    return $wishlist->priority?->value === $filters['priority'];
                });
            }

            if (isset($filters['is_public'])) {
                $wishlists = $wishlists->filter(function ($wishlist) use ($filters) {
                    return $wishlist->is_public === $filters['is_public'];
                });
            }

            if (isset($filters['has_price_drop'])) {
                $wishlists = $wishlists->filter(function ($wishlist) use ($filters) {
                    return $wishlist->hasPriceDrop() === $filters['has_price_drop'];
                });
            }

            if (isset($filters['min_price']) || isset($filters['max_price'])) {
                $wishlists = $wishlists->filter(function ($wishlist) use ($filters) {
                    $currentPrice = $wishlist->current_price ?? 0;
                    $minPrice = $filters['min_price'] ?? 0;
                    $maxPrice = $filters['max_price'] ?? PHP_FLOAT_MAX;

                    return $currentPrice >= $minPrice && $currentPrice <= $maxPrice;
                });
            }

            return $wishlists;
        } catch (\Exception $e) {
            Log::error('Failed to get customer wishlist with filters', [
                'customer_id' => $customerId,
                'filters' => $filters,
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Get wishlist recommendations based on customer preferences
     */
    public function getPersonalizedWishlistRecommendations(int $customerId, int $limit = 10): Collection
    {
        try {
            $recommendations = $this->getWishlistRecommendations($customerId, $limit);

            // Get customer's wishlist categories for better recommendations
            $customerWishlists = $this->getCustomerWishlist($customerId);
            $customerCategories = $customerWishlists->pluck('product.category_id')->unique();

            // Filter recommendations by customer's preferred categories
            if ($customerCategories->isNotEmpty()) {
                $recommendations = $recommendations->filter(function ($wishlist) use ($customerCategories) {
                    return $customerCategories->contains($wishlist->product?->category_id);
                });
            }

            return $recommendations->take($limit);
        } catch (\Exception $e) {
            Log::error('Failed to get personalized wishlist recommendations', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Process price updates for all wishlist items
     */
    public function processWishlistPriceUpdates(): array
    {
        try {
            $results = [];
            $wishlists = $this->repository->all();

            foreach ($wishlists as $wishlist) {
                if ($wishlist->product && $wishlist->product->price) {
                    $oldPrice = $wishlist->current_price;
                    $newPrice = $wishlist->product->price;

                    if ($oldPrice !== $newPrice) {
                        $success = $this->updateWishlistCurrentPrice($wishlist, $newPrice);

                        $results[] = [
                            'wishlist_id' => $wishlist->id,
                            'customer_id' => $wishlist->customer_id,
                            'product_id' => $wishlist->product_id,
                            'old_price' => $oldPrice,
                            'new_price' => $newPrice,
                            'price_updated' => $success,
                            'has_price_drop' => $success ? $this->checkPriceDrop($wishlist) : false,
                        ];
                    }
                }
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('Failed to process wishlist price updates', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get wishlist analytics dashboard data
     */
    public function getWishlistDashboardData(int $customerId): array
    {
        try {
            $analytics = $this->getWishlistAnalytics($customerId);
            $stats = $this->getWishlistStatsByCustomer($customerId);
            $priceDrops = $this->getPriceDropStatsByCustomer($customerId);
            $recentAdditions = $this->getRecentWishlistAdditionsByCustomer($customerId, 5);
            $recommendations = $this->getPersonalizedWishlistRecommendations($customerId, 5);

            return [
                'analytics' => $analytics,
                'stats' => $stats,
                'price_drops' => $priceDrops,
                'recent_additions' => $recentAdditions,
                'recommendations' => $recommendations,
                'total_items' => $analytics['total_items'],
                'total_value' => $analytics['total_value'],
                'price_drop_count' => $analytics['price_drops'],
                'public_items' => $analytics['public_items'],
                'private_items' => $analytics['private_items'],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get wishlist dashboard data', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Share wishlist with another customer
     */
    public function shareWishlist(int $sourceCustomerId, int $targetCustomerId, array $options = []): bool
    {
        try {
            $sourceWishlists = $this->getCustomerWishlist($sourceCustomerId);

            if ($sourceWishlists->isEmpty()) {
                return false;
            }

            $sharedCount = 0;
            $sharePublicOnly = $options['public_only'] ?? true;

            foreach ($sourceWishlists as $wishlist) {
                // Only share public items if specified
                if ($sharePublicOnly && ! $wishlist->is_public) {
                    continue;
                }

                $success = $this->addProductToWishlist($targetCustomerId, $wishlist->product_id, [
                    'notes' => $wishlist->notes,
                    'priority' => $wishlist->priority?->value,
                    'is_public' => false, // Shared items are private by default
                    'price_when_added' => $wishlist->current_price,
                    'current_price' => $wishlist->current_price,
                ]);

                if ($success) {
                    $sharedCount++;
                }
            }

            return $sharedCount > 0;
        } catch (\Exception $e) {
            Log::error('Failed to share wishlist', [
                'source_customer_id' => $sourceCustomerId,
                'target_customer_id' => $targetCustomerId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get wishlist comparison between customers
     */
    public function compareWishlists(int $customerId1, int $customerId2): array
    {
        try {
            $wishlist1 = $this->getCustomerWishlist($customerId1);
            $wishlist2 = $this->getCustomerWishlist($customerId2);

            $products1 = $wishlist1->pluck('product_id')->toArray();
            $products2 = $wishlist2->pluck('product_id')->toArray();

            $commonProducts = array_intersect($products1, $products2);
            $uniqueToCustomer1 = array_diff($products1, $products2);
            $uniqueToCustomer2 = array_diff($products2, $products1);

            return [
                'common_products' => count($commonProducts),
                'unique_to_customer_1' => count($uniqueToCustomer1),
                'unique_to_customer_2' => count($uniqueToCustomer2),
                'similarity_percentage' => count($commonProducts) > 0
                    ? round((count($commonProducts) / max(count($products1), count($products2))) * 100, 2)
                    : 0,
                'common_product_ids' => array_values($commonProducts),
                'unique_to_customer_1_ids' => array_values($uniqueToCustomer1),
                'unique_to_customer_2_ids' => array_values($uniqueToCustomer2),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to compare wishlists', [
                'customer_id_1' => $customerId1,
                'customer_id_2' => $customerId2,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get wishlist insights and trends
     */
    public function getWishlistInsights(int $customerId): array
    {
        try {
            $wishlists = $this->getCustomerWishlist($customerId);
            $analytics = $this->getWishlistAnalytics($customerId);

            // Calculate insights
            $insights = [
                'total_items' => $analytics['total_items'],
                'total_value' => $analytics['total_value'],
                'average_item_value' => $analytics['total_items'] > 0
                    ? round($analytics['total_value'] / $analytics['total_items'], 2)
                    : 0,
                'price_drops' => $analytics['price_drops'],
                'potential_savings' => $wishlists->sum(function ($wishlist) {
                    return $wishlist->getPriceDropAmount() ?? 0;
                }),
                'most_expensive_item' => $wishlists->max('current_price'),
                'least_expensive_item' => $wishlists->min('current_price'),
                'priority_distribution' => $analytics['priority_breakdown'] ?? [],
                'public_vs_private' => [
                    'public' => $analytics['public_items'],
                    'private' => $analytics['private_items'],
                ],
            ];

            return $insights;
        } catch (\Exception $e) {
            Log::error('Failed to get wishlist insights', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Override trait methods for wishlist-specific behavior
     */

    /**
     * Create customer wishlist with additional validation
     */
    public function createCustomerWishlist(array $data): CustomerWishlistDTO
    {
        // Validate wishlist data
        if (! $this->validateWishlistData($data)) {
            throw new \InvalidArgumentException('Invalid wishlist data provided');
        }

        // Check if already in wishlist
        if ($this->isInWishlist($data['customer_id'], $data['product_id'])) {
            throw new \InvalidArgumentException('Product already in wishlist');
        }

        return $this->createCustomerWishlistItem($data);
    }

    /**
     * Update customer wishlist with additional validation
     */
    public function updateCustomerWishlist(CustomerWishlist $wishlist, array $data): ?CustomerWishlistDTO
    {
        // Validate status changes
        if (! $this->validateWishlistStatusChange($wishlist, $data)) {
            throw new \InvalidArgumentException('Invalid status change data provided');
        }

        return $this->updateCustomerWishlistItem($wishlist, $data);
    }

    /**
     * Delete customer wishlist with cleanup
     */
    public function deleteCustomerWishlist(CustomerWishlist $wishlist): bool
    {
        try {
            $result = $this->deleteCustomerWishlistItem($wishlist);

            if ($result) {
                // Trigger event for wishlist deleted
                event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\CustomerWishlistDeleted($wishlist));
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete customer wishlist', [
                'wishlist_id' => $wishlist->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
