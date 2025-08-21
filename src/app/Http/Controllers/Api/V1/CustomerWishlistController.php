<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\DTOs\CustomerWishlistDTO;
use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Fereydooni\Shopping\app\Services\CustomerWishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class CustomerWishlistController extends Controller
{
    public function __construct(
        private CustomerWishlistService $wishlistService
    ) {}

    /**
     * Display a listing of customer wishlists
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $perPage = $request->get('per_page', 15);
        $wishlists = $this->wishlistService->paginate($perPage);

        return JsonResource::collection($wishlists);
    }

    /**
     * Store a newly created customer wishlist
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', CustomerWishlist::class);

        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'product_id' => 'required|integer|exists:products,id',
            'notes' => 'nullable|string|max:1000',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'is_public' => 'nullable|boolean',
            'is_notified' => 'nullable|boolean',
            'price_when_added' => 'nullable|numeric|min:0',
            'current_price' => 'nullable|numeric|min:0',
            'price_drop_notification' => 'nullable|boolean',
        ]);

        $wishlist = $this->wishlistService->create($validated);

        return response()->json([
            'message' => 'Wishlist item created successfully',
            'data' => CustomerWishlistDTO::fromModel($wishlist)
        ], 201);
    }

    /**
     * Display the specified customer wishlist
     */
    public function show(CustomerWishlist $customerWishlist): JsonResponse
    {
        $this->authorize('view', $customerWishlist);

        return response()->json([
            'data' => CustomerWishlistDTO::fromModel($customerWishlist)
        ]);
    }

    /**
     * Update the specified customer wishlist
     */
    public function update(Request $request, CustomerWishlist $customerWishlist): JsonResponse
    {
        $this->authorize('update', $customerWishlist);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'is_public' => 'nullable|boolean',
            'is_notified' => 'nullable|boolean',
            'current_price' => 'nullable|numeric|min:0',
            'price_drop_notification' => 'nullable|boolean',
        ]);

        $updated = $this->wishlistService->update($customerWishlist, $validated);

        if (!$updated) {
            return response()->json(['message' => 'Failed to update wishlist'], 500);
        }

        return response()->json([
            'message' => 'Wishlist updated successfully',
            'data' => CustomerWishlistDTO::fromModel($customerWishlist->fresh())
        ]);
    }

    /**
     * Remove the specified customer wishlist
     */
    public function destroy(CustomerWishlist $customerWishlist): JsonResponse
    {
        $this->authorize('delete', $customerWishlist);

        $deleted = $this->wishlistService->delete($customerWishlist);

        if (!$deleted) {
            return response()->json(['message' => 'Failed to delete wishlist'], 500);
        }

        return response()->json(['message' => 'Wishlist deleted successfully']);
    }

    /**
     * Make wishlist public
     */
    public function makePublic(CustomerWishlist $customerWishlist): JsonResponse
    {
        $this->authorize('makePublic', $customerWishlist);

        $updated = $this->wishlistService->makePublic($customerWishlist);

        if (!$updated) {
            return response()->json(['message' => 'Failed to make wishlist public'], 500);
        }

        return response()->json(['message' => 'Wishlist made public successfully']);
    }

    /**
     * Make wishlist private
     */
    public function makePrivate(CustomerWishlist $customerWishlist): JsonResponse
    {
        $this->authorize('makePrivate', $customerWishlist);

        $updated = $this->wishlistService->makePrivate($customerWishlist);

        if (!$updated) {
            return response()->json(['message' => 'Failed to make wishlist private'], 500);
        }

        return response()->json(['message' => 'Wishlist made private successfully']);
    }

    /**
     * Set wishlist priority
     */
    public function setPriority(Request $request, CustomerWishlist $customerWishlist): JsonResponse
    {
        $this->authorize('setPriority', $customerWishlist);

        $validated = $request->validate([
            'priority' => 'required|string|in:low,medium,high,urgent',
        ]);

        $updated = $this->wishlistService->setPriority($customerWishlist, $validated['priority']);

        if (!$updated) {
            return response()->json(['message' => 'Failed to set wishlist priority'], 500);
        }

        return response()->json(['message' => 'Wishlist priority updated successfully']);
    }

    /**
     * Mark wishlist as notified
     */
    public function markNotified(CustomerWishlist $customerWishlist): JsonResponse
    {
        $this->authorize('markNotified', $customerWishlist);

        $updated = $this->wishlistService->markAsNotified($customerWishlist);

        if (!$updated) {
            return response()->json(['message' => 'Failed to mark wishlist as notified'], 500);
        }

        return response()->json(['message' => 'Wishlist marked as notified successfully']);
    }

    /**
     * Update current price
     */
    public function updatePrice(Request $request, CustomerWishlist $customerWishlist): JsonResponse
    {
        $this->authorize('updatePrice', $customerWishlist);

        $validated = $request->validate([
            'current_price' => 'required|numeric|min:0',
        ]);

        $updated = $this->wishlistService->updateCurrentPrice($customerWishlist, $validated['current_price']);

        if (!$updated) {
            return response()->json(['message' => 'Failed to update wishlist price'], 500);
        }

        return response()->json(['message' => 'Wishlist price updated successfully']);
    }

    /**
     * Check price drop
     */
    public function checkPriceDrop(CustomerWishlist $customerWishlist): JsonResponse
    {
        $this->authorize('checkPriceDrop', $customerWishlist);

        $hasPriceDrop = $this->wishlistService->checkPriceDrop($customerWishlist);

        return response()->json([
            'has_price_drop' => $hasPriceDrop,
            'message' => $hasPriceDrop ? 'Price drop detected' : 'No price drop detected'
        ]);
    }

    /**
     * Get customer wishlist
     */
    public function getCustomerWishlist(int $customerId): JsonResponse
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $wishlist = $this->wishlistService->findByCustomerId($customerId);

        return response()->json([
            'data' => $wishlist->map(fn($item) => CustomerWishlistDTO::fromModel($item))
        ]);
    }

    /**
     * Add product to customer wishlist
     */
    public function addToCustomerWishlist(Request $request, int $customerId): JsonResponse
    {
        $this->authorize('create', CustomerWishlist::class);

        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'notes' => 'nullable|string|max:1000',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'is_public' => 'nullable|boolean',
            'price_when_added' => 'nullable|numeric|min:0',
            'current_price' => 'nullable|numeric|min:0',
            'price_drop_notification' => 'nullable|boolean',
        ]);

        $wishlist = $this->wishlistService->addToWishlist($customerId, $validated['product_id'], $validated);

        if (!$wishlist) {
            return response()->json(['message' => 'Failed to add product to wishlist'], 500);
        }

        return response()->json([
            'message' => 'Product added to wishlist successfully',
            'data' => CustomerWishlistDTO::fromModel($wishlist)
        ], 201);
    }

    /**
     * Remove product from customer wishlist
     */
    public function removeFromCustomerWishlist(int $customerId, int $productId): JsonResponse
    {
        $wishlist = $this->wishlistService->findByCustomerAndProduct($customerId, $productId);
        
        if (!$wishlist) {
            return response()->json(['message' => 'Wishlist item not found'], 404);
        }

        $this->authorize('delete', $wishlist);

        $removed = $this->wishlistService->removeFromWishlist($customerId, $productId);

        if (!$removed) {
            return response()->json(['message' => 'Failed to remove product from wishlist'], 500);
        }

        return response()->json(['message' => 'Product removed from wishlist successfully']);
    }

    /**
     * Clear customer wishlist
     */
    public function clearCustomerWishlist(int $customerId): JsonResponse
    {
        $this->authorize('delete', CustomerWishlist::class);

        $cleared = $this->wishlistService->clearWishlist($customerId);

        if (!$cleared) {
            return response()->json(['message' => 'Failed to clear wishlist'], 500);
        }

        return response()->json(['message' => 'Wishlist cleared successfully']);
    }

    /**
     * Export customer wishlist
     */
    public function exportCustomerWishlist(int $customerId): JsonResponse
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $export = $this->wishlistService->exportWishlist($customerId);

        return response()->json([
            'data' => $export
        ]);
    }

    /**
     * Import customer wishlist
     */
    public function importCustomerWishlist(Request $request, int $customerId): JsonResponse
    {
        $this->authorize('create', CustomerWishlist::class);

        $validated = $request->validate([
            'wishlist_items' => 'required|array',
            'wishlist_items.*.product_id' => 'required|integer|exists:products,id',
            'wishlist_items.*.notes' => 'nullable|string|max:1000',
            'wishlist_items.*.priority' => 'nullable|string|in:low,medium,high,urgent',
            'wishlist_items.*.is_public' => 'nullable|boolean',
            'wishlist_items.*.price_when_added' => 'nullable|numeric|min:0',
            'wishlist_items.*.current_price' => 'nullable|numeric|min:0',
            'wishlist_items.*.price_drop_notification' => 'nullable|boolean',
        ]);

        $imported = $this->wishlistService->importWishlist($customerId, $validated['wishlist_items']);

        if (!$imported) {
            return response()->json(['message' => 'Failed to import wishlist'], 500);
        }

        return response()->json(['message' => 'Wishlist imported successfully']);
    }

    /**
     * Duplicate customer wishlist
     */
    public function duplicateCustomerWishlist(Request $request, int $customerId): JsonResponse
    {
        $this->authorize('create', CustomerWishlist::class);

        $validated = $request->validate([
            'target_customer_id' => 'required|integer|exists:customers,id',
        ]);

        $duplicated = $this->wishlistService->duplicateWishlist($customerId, $validated['target_customer_id']);

        if (!$duplicated) {
            return response()->json(['message' => 'Failed to duplicate wishlist'], 500);
        }

        return response()->json(['message' => 'Wishlist duplicated successfully']);
    }

    /**
     * Get wishlist recommendations
     */
    public function getWishlistRecommendations(int $customerId): JsonResponse
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $recommendations = $this->wishlistService->getWishlistRecommendations($customerId);

        return response()->json([
            'data' => $recommendations
        ]);
    }

    /**
     * Get wishlist analytics
     */
    public function getWishlistAnalytics(int $customerId): JsonResponse
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $analytics = $this->wishlistService->getWishlistAnalytics($customerId);

        return response()->json([
            'data' => $analytics
        ]);
    }

    /**
     * Search wishlists
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $validated = $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $results = $this->wishlistService->search($validated['query']);

        return JsonResource::collection($results);
    }

    /**
     * Get wishlist statistics
     */
    public function getStats(): JsonResponse
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $stats = $this->wishlistService->getWishlistStats();

        return response()->json([
            'data' => $stats
        ]);
    }

    /**
     * Get most wishlisted products
     */
    public function getMostWishlisted(): JsonResponse
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $limit = request()->get('limit', 10);
        $products = $this->wishlistService->getMostWishlistedProducts($limit);

        return response()->json([
            'data' => $products
        ]);
    }

    /**
     * Get price drop alerts
     */
    public function getPriceDrops(): JsonResponse
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $priceDrops = $this->wishlistService->findWithPriceDrops();

        return response()->json([
            'data' => $priceDrops->map(fn($item) => CustomerWishlistDTO::fromModel($item))
        ]);
    }
}
