<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Fereydooni\Shopping\app\Repositories\Interfaces\CustomerWishlistRepositoryInterface;
use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Fereydooni\Shopping\app\DTOs\CustomerWishlistDTO;
use Fereydooni\Shopping\app\Enums\WishlistPriority;

class CustomerWishlistRepository implements CustomerWishlistRepositoryInterface
{
    public function __construct(
        private CustomerWishlist $model
    ) {}

    // Basic CRUD operations
    public function all(): Collection
    {
        return $this->model->with(['customer', 'product'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['customer', 'product'])->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['customer', 'product'])->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with(['customer', 'product'])->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    // Find operations
    public function find(int $id): ?CustomerWishlist
    {
        return $this->model->with(['customer', 'product'])->find($id);
    }

    public function findDTO(int $id): ?CustomerWishlistDTO
    {
        $wishlist = $this->find($id);
        return $wishlist ? CustomerWishlistDTO::fromModel($wishlist) : null;
    }

    public function findByCustomerId(int $customerId): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->byCustomer($customerId)
            ->orderBy('priority', 'desc')
            ->orderBy('added_at', 'desc')
            ->get();
    }

    public function findByCustomerIdDTO(int $customerId): Collection
    {
        return $this->findByCustomerId($customerId)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    public function findByProductId(int $productId): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->byProduct($productId)
            ->get();
    }

    public function findByProductIdDTO(int $productId): Collection
    {
        return $this->findByProductId($productId)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    public function findByCustomerAndProduct(int $customerId, int $productId): ?CustomerWishlist
    {
        return $this->model->with(['customer', 'product'])
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();
    }

    public function findByCustomerAndProductDTO(int $customerId, int $productId): ?CustomerWishlistDTO
    {
        $wishlist = $this->findByCustomerAndProduct($customerId, $productId);
        return $wishlist ? CustomerWishlistDTO::fromModel($wishlist) : null;
    }

    // Visibility-based queries
    public function findPublic(): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->public()
            ->orderBy('added_at', 'desc')
            ->get();
    }

    public function findPublicDTO(): Collection
    {
        return $this->findPublic()->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    public function findPrivate(): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->private()
            ->orderBy('added_at', 'desc')
            ->get();
    }

    public function findPrivateDTO(): Collection
    {
        return $this->findPrivate()->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    // Priority-based queries
    public function findByPriority(string $priority): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->byPriority($priority)
            ->orderBy('added_at', 'desc')
            ->get();
    }

    public function findByPriorityDTO(string $priority): Collection
    {
        return $this->findByPriority($priority)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    // Date range queries
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->byDateRange($startDate, $endDate)
            ->orderBy('added_at', 'desc')
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->findByDateRange($startDate, $endDate)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    // Price range queries
    public function findByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->byPriceRange($minPrice, $maxPrice)
            ->orderBy('current_price', 'asc')
            ->get();
    }

    public function findByPriceRangeDTO(float $minPrice, float $maxPrice): Collection
    {
        return $this->findByPriceRange($minPrice, $maxPrice)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    // Price drop queries
    public function findWithPriceDrops(): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->withPriceDrops()
            ->orderBy('price_drop_percentage', 'desc')
            ->get();
    }

    public function findWithPriceDropsDTO(): Collection
    {
        return $this->findWithPriceDrops()->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    // Notification queries
    public function findNotified(): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->notified()
            ->orderBy('notification_sent_at', 'desc')
            ->get();
    }

    public function findNotifiedDTO(): Collection
    {
        return $this->findNotified()->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    public function findNotNotified(): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->notNotified()
            ->orderBy('added_at', 'desc')
            ->get();
    }

    public function findNotNotifiedDTO(): Collection
    {
        return $this->findNotNotified()->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    // Create and Update operations
    public function create(array $data): CustomerWishlist
    {
        return $this->model->create($data);
    }

    public function createAndReturnDTO(array $data): CustomerWishlistDTO
    {
        $wishlist = $this->create($data);
        return CustomerWishlistDTO::fromModel($wishlist->load(['customer', 'product']));
    }

    public function update(CustomerWishlist $wishlist, array $data): bool
    {
        return $wishlist->update($data);
    }

    public function updateAndReturnDTO(CustomerWishlist $wishlist, array $data): ?CustomerWishlistDTO
    {
        $updated = $this->update($wishlist, $data);
        return $updated ? CustomerWishlistDTO::fromModel($wishlist->fresh()->load(['customer', 'product'])) : null;
    }

    public function delete(CustomerWishlist $wishlist): bool
    {
        return $wishlist->delete();
    }

    // Wishlist management operations
    public function addToWishlist(int $customerId, int $productId, array $data = []): bool
    {
        // Check if already in wishlist
        if ($this->isInWishlist($customerId, $productId)) {
            return false;
        }

        $wishlistData = array_merge([
            'customer_id' => $customerId,
            'product_id' => $productId,
            'added_at' => now(),
        ], $data);

        return (bool) $this->create($wishlistData);
    }

    public function addToWishlistDTO(int $customerId, int $productId, array $data = []): ?CustomerWishlistDTO
    {
        // Check if already in wishlist
        if ($this->isInWishlist($customerId, $productId)) {
            return null;
        }

        $wishlistData = array_merge([
            'customer_id' => $customerId,
            'product_id' => $productId,
            'added_at' => now(),
        ], $data);

        return $this->createAndReturnDTO($wishlistData);
    }

    public function removeFromWishlist(int $customerId, int $productId): bool
    {
        $wishlist = $this->findByCustomerAndProduct($customerId, $productId);
        return $wishlist ? $this->delete($wishlist) : false;
    }

    public function isInWishlist(int $customerId, int $productId): bool
    {
        return $this->model->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->exists();
    }

    // Status management operations
    public function makePublic(CustomerWishlist $wishlist): bool
    {
        return $wishlist->makePublic();
    }

    public function makePrivate(CustomerWishlist $wishlist): bool
    {
        return $wishlist->makePrivate();
    }

    public function setPriority(CustomerWishlist $wishlist, string $priority): bool
    {
        $priorityEnum = WishlistPriority::tryFrom($priority);
        return $priorityEnum ? $wishlist->setPriority($priorityEnum) : false;
    }

    public function markAsNotified(CustomerWishlist $wishlist): bool
    {
        return $wishlist->markAsNotified();
    }

    public function markAsNotNotified(CustomerWishlist $wishlist): bool
    {
        return $wishlist->markAsNotNotified();
    }

    // Price management operations
    public function updateCurrentPrice(CustomerWishlist $wishlist, float $currentPrice): bool
    {
        return $wishlist->updateCurrentPrice($currentPrice);
    }

    public function checkPriceDrop(CustomerWishlist $wishlist): bool
    {
        return $wishlist->checkPriceDrop();
    }

    // Statistics operations
    public function getWishlistCount(): int
    {
        return $this->model->count();
    }

    public function getWishlistCountByCustomer(int $customerId): int
    {
        return $this->model->byCustomer($customerId)->count();
    }

    public function getWishlistCountByProduct(int $productId): int
    {
        return $this->model->byProduct($productId)->count();
    }

    public function getPublicWishlistCount(): int
    {
        return $this->model->public()->count();
    }

    public function getPrivateWishlistCount(): int
    {
        return $this->model->private()->count();
    }

    public function getWishlistCountByPriority(string $priority): int
    {
        return $this->model->byPriority($priority)->count();
    }

    // Value calculations
    public function getTotalWishlistValue(): float
    {
        return $this->model->whereNotNull('current_price')->sum('current_price');
    }

    public function getTotalWishlistValueByCustomer(int $customerId): float
    {
        return $this->model->byCustomer($customerId)
            ->whereNotNull('current_price')
            ->sum('current_price');
    }

    public function getAverageWishlistValue(): float
    {
        return $this->model->whereNotNull('current_price')->avg('current_price') ?? 0;
    }

    public function getAverageWishlistValueByCustomer(int $customerId): float
    {
        return $this->model->byCustomer($customerId)
            ->whereNotNull('current_price')
            ->avg('current_price') ?? 0;
    }

    // Search operations
    public function search(string $query): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->whereHas('product', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orWhere('notes', 'like', "%{$query}%")
            ->orderBy('added_at', 'desc')
            ->get();
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    public function searchByCustomer(int $customerId, string $query): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->byCustomer($customerId)
            ->whereHas('product', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orWhere('notes', 'like', "%{$query}%")
            ->orderBy('added_at', 'desc')
            ->get();
    }

    public function searchByCustomerDTO(int $customerId, string $query): Collection
    {
        return $this->searchByCustomer($customerId, $query)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    // Popular products
    public function getMostWishlistedProducts(int $limit = 10): Collection
    {
        return $this->model->with('product')
            ->select('product_id', DB::raw('count(*) as wishlist_count'))
            ->groupBy('product_id')
            ->orderBy('wishlist_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMostWishlistedProductsDTO(int $limit = 10): Collection
    {
        return $this->getMostWishlistedProducts($limit)->map(function ($item) {
            $wishlist = $this->model->with(['customer', 'product'])
                ->where('product_id', $item->product_id)
                ->first();
            return $wishlist ? CustomerWishlistDTO::fromModel($wishlist) : null;
        })->filter();
    }

    public function getMostWishlistedProductsByCategory(int $categoryId, int $limit = 10): Collection
    {
        return $this->model->with('product')
            ->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->select('product_id', DB::raw('count(*) as wishlist_count'))
            ->groupBy('product_id')
            ->orderBy('wishlist_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getMostWishlistedProductsByCategoryDTO(int $categoryId, int $limit = 10): Collection
    {
        return $this->getMostWishlistedProductsByCategory($categoryId, $limit)->map(function ($item) {
            $wishlist = $this->model->with(['customer', 'product'])
                ->where('product_id', $item->product_id)
                ->first();
            return $wishlist ? CustomerWishlistDTO::fromModel($wishlist) : null;
        })->filter();
    }

    // Recent additions
    public function getRecentWishlistAdditions(int $limit = 10): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->orderBy('added_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentWishlistAdditionsDTO(int $limit = 10): Collection
    {
        return $this->getRecentWishlistAdditions($limit)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    public function getRecentWishlistAdditionsByCustomer(int $customerId, int $limit = 10): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->byCustomer($customerId)
            ->orderBy('added_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentWishlistAdditionsByCustomerDTO(int $customerId, int $limit = 10): Collection
    {
        return $this->getRecentWishlistAdditionsByCustomer($customerId, $limit)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    // Sorting operations
    public function getWishlistByPriority(int $customerId): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->byCustomer($customerId)
            ->orderBy('priority', 'desc')
            ->orderBy('added_at', 'desc')
            ->get();
    }

    public function getWishlistByPriorityDTO(int $customerId): Collection
    {
        return $this->getWishlistByPriority($customerId)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    public function getWishlistByDateAdded(int $customerId, string $order = 'desc'): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->byCustomer($customerId)
            ->orderBy('added_at', $order)
            ->get();
    }

    public function getWishlistByDateAddedDTO(int $customerId, string $order = 'desc'): Collection
    {
        return $this->getWishlistByDateAdded($customerId, $order)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    public function getWishlistByPrice(int $customerId, string $order = 'desc'): Collection
    {
        return $this->model->with(['customer', 'product'])
            ->byCustomer($customerId)
            ->whereNotNull('current_price')
            ->orderBy('current_price', $order)
            ->get();
    }

    public function getWishlistByPriceDTO(int $customerId, string $order = 'desc'): Collection
    {
        return $this->getWishlistByPrice($customerId, $order)->map(function ($wishlist) {
            return CustomerWishlistDTO::fromModel($wishlist);
        });
    }

    // Analytics operations
    public function getWishlistStats(): array
    {
        return [
            'total_wishlists' => $this->getWishlistCount(),
            'public_wishlists' => $this->getPublicWishlistCount(),
            'private_wishlists' => $this->getPrivateWishlistCount(),
            'total_value' => $this->getTotalWishlistValue(),
            'average_value' => $this->getAverageWishlistValue(),
            'price_drops' => $this->model->withPriceDrops()->count(),
            'notifications_sent' => $this->model->notified()->count(),
        ];
    }

    public function getWishlistStatsByCustomer(int $customerId): array
    {
        return [
            'total_wishlists' => $this->getWishlistCountByCustomer($customerId),
            'total_value' => $this->getTotalWishlistValueByCustomer($customerId),
            'average_value' => $this->getAverageWishlistValueByCustomer($customerId),
            'price_drops' => $this->model->byCustomer($customerId)->withPriceDrops()->count(),
            'notifications_sent' => $this->model->byCustomer($customerId)->notified()->count(),
        ];
    }

    public function getWishlistStatsByProduct(int $productId): array
    {
        return [
            'total_wishlists' => $this->getWishlistCountByProduct($productId),
            'public_wishlists' => $this->model->byProduct($productId)->public()->count(),
            'private_wishlists' => $this->model->byProduct($productId)->private()->count(),
        ];
    }

    public function getWishlistStatsByCategory(int $categoryId): array
    {
        $wishlists = $this->model->whereHas('product', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        });

        return [
            'total_wishlists' => $wishlists->count(),
            'public_wishlists' => $wishlists->public()->count(),
            'private_wishlists' => $wishlists->private()->count(),
        ];
    }

    public function getWishlistGrowthStats(string $period = 'monthly'): array
    {
        $query = $this->model->select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as period"),
            DB::raw('count(*) as count')
        )
        ->groupBy('period')
        ->orderBy('period', 'desc')
        ->limit(12);

        return $query->get()->toArray();
    }

    public function getWishlistGrowthStatsByCustomer(int $customerId, string $period = 'monthly'): array
    {
        $query = $this->model->byCustomer($customerId)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as period"),
                DB::raw('count(*) as count')
            )
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit(12);

        return $query->get()->toArray();
    }

    public function getPriceDropStats(): array
    {
        $priceDrops = $this->model->withPriceDrops();
        
        return [
            'total_price_drops' => $priceDrops->count(),
            'average_drop_percentage' => $priceDrops->avg(DB::raw('((price_when_added - current_price) / price_when_added) * 100')) ?? 0,
            'total_savings' => $priceDrops->sum(DB::raw('price_when_added - current_price')) ?? 0,
        ];
    }

    public function getPriceDropStatsByCustomer(int $customerId): array
    {
        $priceDrops = $this->model->byCustomer($customerId)->withPriceDrops();
        
        return [
            'total_price_drops' => $priceDrops->count(),
            'average_drop_percentage' => $priceDrops->avg(DB::raw('((price_when_added - current_price) / price_when_added) * 100')) ?? 0,
            'total_savings' => $priceDrops->sum(DB::raw('price_when_added - current_price')) ?? 0,
        ];
    }

    // Validation operations
    public function validateWishlist(array $data): bool
    {
        $rules = CustomerWishlistDTO::rules();
        
        $validator = validator($data, $rules, CustomerWishlistDTO::messages());
        
        return !$validator->fails();
    }

    // Recommendation operations
    public function getWishlistRecommendations(int $customerId, int $limit = 10): Collection
    {
        // Get customer's wishlist categories
        $customerCategories = $this->model->byCustomer($customerId)
            ->with('product')
            ->get()
            ->pluck('product.category_id')
            ->unique();

        // Get products from same categories that customer doesn't have
        return $this->model->with('product')
            ->whereNotIn('customer_id', [$customerId])
            ->whereHas('product', function ($q) use ($customerCategories) {
                $q->whereIn('category_id', $customerCategories);
            })
            ->select('product_id', DB::raw('count(*) as recommendation_score'))
            ->groupBy('product_id')
            ->orderBy('recommendation_score', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getWishlistRecommendationsDTO(int $customerId, int $limit = 10): Collection
    {
        return $this->getWishlistRecommendations($customerId, $limit)->map(function ($item) {
            $wishlist = $this->model->with(['customer', 'product'])
                ->where('product_id', $item->product_id)
                ->first();
            return $wishlist ? CustomerWishlistDTO::fromModel($wishlist) : null;
        })->filter();
    }

    public function getSimilarWishlists(int $customerId, int $limit = 10): Collection
    {
        // Get customers with similar wishlist items
        $customerProducts = $this->model->byCustomer($customerId)->pluck('product_id');
        
        return $this->model->with(['customer', 'product'])
            ->whereNotIn('customer_id', [$customerId])
            ->whereIn('product_id', $customerProducts)
            ->select('customer_id', DB::raw('count(*) as similarity_score'))
            ->groupBy('customer_id')
            ->orderBy('similarity_score', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getSimilarWishlistsDTO(int $customerId, int $limit = 10): Collection
    {
        return $this->getSimilarWishlists($customerId, $limit)->map(function ($item) {
            $wishlist = $this->model->with(['customer', 'product'])
                ->where('customer_id', $item->customer_id)
                ->first();
            return $wishlist ? CustomerWishlistDTO::fromModel($wishlist) : null;
        })->filter();
    }

    // Analytics operations
    public function getWishlistAnalytics(int $customerId): array
    {
        $wishlists = $this->model->byCustomer($customerId);
        
        return [
            'total_items' => $wishlists->count(),
            'public_items' => $wishlists->public()->count(),
            'private_items' => $wishlists->private()->count(),
            'total_value' => $wishlists->whereNotNull('current_price')->sum('current_price'),
            'price_drops' => $wishlists->withPriceDrops()->count(),
            'notifications_sent' => $wishlists->notified()->count(),
            'priority_breakdown' => $wishlists->select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
        ];
    }

    public function getWishlistAnalyticsByProduct(int $productId): array
    {
        $wishlists = $this->model->byProduct($productId);
        
        return [
            'total_wishlists' => $wishlists->count(),
            'public_wishlists' => $wishlists->public()->count(),
            'private_wishlists' => $wishlists->private()->count(),
            'price_drops' => $wishlists->withPriceDrops()->count(),
            'notifications_sent' => $wishlists->notified()->count(),
        ];
    }

    public function getWishlistAnalyticsByCategory(int $categoryId): array
    {
        $wishlists = $this->model->whereHas('product', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        });
        
        return [
            'total_wishlists' => $wishlists->count(),
            'public_wishlists' => $wishlists->public()->count(),
            'private_wishlists' => $wishlists->private()->count(),
            'price_drops' => $wishlists->withPriceDrops()->count(),
            'notifications_sent' => $wishlists->notified()->count(),
        ];
    }

    // Import/Export operations
    public function exportWishlist(int $customerId): array
    {
        $wishlists = $this->findByCustomerId($customerId);
        
        return $wishlists->map(function ($wishlist) {
            return [
                'product_id' => $wishlist->product_id,
                'product_name' => $wishlist->product->name ?? '',
                'notes' => $wishlist->notes,
                'priority' => $wishlist->priority?->value,
                'is_public' => $wishlist->is_public,
                'price_when_added' => $wishlist->price_when_added,
                'current_price' => $wishlist->current_price,
                'added_at' => $wishlist->added_at?->toISOString(),
            ];
        })->toArray();
    }

    public function importWishlist(int $customerId, array $wishlistItems): bool
    {
        try {
            DB::beginTransaction();
            
            foreach ($wishlistItems as $item) {
                $this->addToWishlist($customerId, $item['product_id'], [
                    'notes' => $item['notes'] ?? null,
                    'priority' => $item['priority'] ?? WishlistPriority::MEDIUM->value,
                    'is_public' => $item['is_public'] ?? false,
                    'price_when_added' => $item['price_when_added'] ?? null,
                    'current_price' => $item['current_price'] ?? null,
                ]);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import wishlist', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    // Bulk operations
    public function clearWishlist(int $customerId): bool
    {
        try {
            return $this->model->byCustomer($customerId)->delete() > 0;
        } catch (\Exception $e) {
            Log::error('Failed to clear wishlist', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function moveWishlistItem(int $wishlistId, int $newPosition): bool
    {
        // This would require implementing a position field in the database
        // For now, we'll return false as this feature needs additional implementation
        return false;
    }

    public function duplicateWishlist(int $sourceCustomerId, int $targetCustomerId): bool
    {
        try {
            DB::beginTransaction();
            
            $sourceWishlists = $this->findByCustomerId($sourceCustomerId);
            
            foreach ($sourceWishlists as $wishlist) {
                $this->addToWishlist($targetCustomerId, $wishlist->product_id, [
                    'notes' => $wishlist->notes,
                    'priority' => $wishlist->priority?->value,
                    'is_public' => $wishlist->is_public,
                    'price_when_added' => $wishlist->price_when_added,
                    'current_price' => $wishlist->current_price,
                ]);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to duplicate wishlist', [
                'source_customer_id' => $sourceCustomerId,
                'target_customer_id' => $targetCustomerId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
