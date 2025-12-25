<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\DTOs\CustomerWishlistDTO;
use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Fereydooni\Shopping\app\Services\CustomerWishlistService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerWishlistController extends Controller
{
    public function __construct(
        private CustomerWishlistService $wishlistService
    ) {}

    /**
     * Display customer wishlist dashboard
     */
    public function dashboard(): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $stats = $this->wishlistService->getWishlistStats();
        $recentAdditions = $this->wishlistService->getRecentWishlistAdditions(5);
        $priceDrops = $this->wishlistService->findWithPriceDrops();

        return view('customer-wishlists.dashboard', compact('stats', 'recentAdditions', 'priceDrops'));
    }

    /**
     * Display customer wishlist management interface
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $perPage = $request->get('per_page', 15);
        $wishlists = $this->wishlistService->paginate($perPage);

        return view('customer-wishlists.index', compact('wishlists'));
    }

    /**
     * Show the form for creating a new customer wishlist
     */
    public function create(): View
    {
        $this->authorize('create', CustomerWishlist::class);

        return view('customer-wishlists.create');
    }

    /**
     * Display the specified customer wishlist
     */
    public function show(CustomerWishlist $customerWishlist): View
    {
        $this->authorize('view', $customerWishlist);

        $wishlist = CustomerWishlistDTO::fromModel($customerWishlist);
        $analytics = $this->wishlistService->getWishlistAnalytics($customerWishlist->customer_id);

        return view('customer-wishlists.show', compact('wishlist', 'analytics'));
    }

    /**
     * Show the form for editing the specified customer wishlist
     */
    public function edit(CustomerWishlist $customerWishlist): View
    {
        $this->authorize('update', $customerWishlist);

        $wishlist = CustomerWishlistDTO::fromModel($customerWishlist);

        return view('customer-wishlists.edit', compact('wishlist'));
    }

    /**
     * Display customer wishlist management interface
     */
    public function manage(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $wishlist = $this->wishlistService->findByCustomerId($customerId);
        $analytics = $this->wishlistService->getWishlistAnalytics($customerId);
        $recommendations = $this->wishlistService->getWishlistRecommendations($customerId);

        return view('customer-wishlists.manage', compact('wishlist', 'analytics', 'recommendations', 'customerId'));
    }

    /**
     * Display wishlist item operations interface
     */
    public function items(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $wishlist = $this->wishlistService->findByCustomerId($customerId);
        $wishlistByPriority = $this->wishlistService->getWishlistByPriority($customerId);
        $wishlistByDate = $this->wishlistService->getWishlistByDateAdded($customerId);
        $wishlistByPrice = $this->wishlistService->getWishlistByPrice($customerId);

        return view('customer-wishlists.items', compact(
            'wishlist',
            'wishlistByPriority',
            'wishlistByDate',
            'wishlistByPrice',
            'customerId'
        ));
    }

    /**
     * Display wishlist privacy settings interface
     */
    public function privacy(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $publicWishlist = $this->wishlistService->findPublic();
        $privateWishlist = $this->wishlistService->findPrivate();

        return view('customer-wishlists.privacy', compact('publicWishlist', 'privateWishlist', 'customerId'));
    }

    /**
     * Display wishlist priority management interface
     */
    public function priority(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $wishlistByPriority = $this->wishlistService->getWishlistByPriority($customerId);
        $priorityStats = $this->wishlistService->getWishlistStatsByCustomer($customerId);

        return view('customer-wishlists.priority', compact('wishlistByPriority', 'priorityStats', 'customerId'));
    }

    /**
     * Display price tracking interface
     */
    public function priceTracking(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $priceDrops = $this->wishlistService->findWithPriceDrops();
        $priceStats = $this->wishlistService->getPriceDropStatsByCustomer($customerId);
        $wishlistByPrice = $this->wishlistService->getWishlistByPrice($customerId);

        return view('customer-wishlists.price-tracking', compact('priceDrops', 'priceStats', 'wishlistByPrice', 'customerId'));
    }

    /**
     * Display price drop notifications interface
     */
    public function notifications(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $notifiedWishlist = $this->wishlistService->findNotified();
        $notNotifiedWishlist = $this->wishlistService->findNotNotified();

        return view('customer-wishlists.notifications', compact('notifiedWishlist', 'notNotifiedWishlist', 'customerId'));
    }

    /**
     * Display wishlist analytics dashboard
     */
    public function analytics(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $analytics = $this->wishlistService->getWishlistAnalytics($customerId);
        $growthStats = $this->wishlistService->getWishlistGrowthStatsByCustomer($customerId);
        $priceDropStats = $this->wishlistService->getPriceDropStatsByCustomer($customerId);

        return view('customer-wishlists.analytics', compact('analytics', 'growthStats', 'priceDropStats', 'customerId'));
    }

    /**
     * Display wishlist import/export interface
     */
    public function importExport(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $exportData = $this->wishlistService->exportWishlist($customerId);

        return view('customer-wishlists.import-export', compact('exportData', 'customerId'));
    }

    /**
     * Display wishlist sharing interface
     */
    public function sharing(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $publicWishlist = $this->wishlistService->findPublic();
        $similarWishlists = $this->wishlistService->getSimilarWishlists($customerId);

        return view('customer-wishlists.sharing', compact('publicWishlist', 'similarWishlists', 'customerId'));
    }

    /**
     * Display wishlist recommendations interface
     */
    public function recommendations(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $recommendations = $this->wishlistService->getWishlistRecommendations($customerId);
        $mostWishlisted = $this->wishlistService->getMostWishlistedProducts(10);

        return view('customer-wishlists.recommendations', compact('recommendations', 'mostWishlisted', 'customerId'));
    }

    /**
     * Display wishlist search interface
     */
    public function search(Request $request): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $query = $request->get('query');
        $results = collect();

        if ($query) {
            $results = $this->wishlistService->search($query);
        }

        return view('customer-wishlists.search', compact('results', 'query'));
    }

    /**
     * Display wishlist statistics interface
     */
    public function statistics(): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $stats = $this->wishlistService->getWishlistStats();
        $growthStats = $this->wishlistService->getWishlistGrowthStats();
        $priceDropStats = $this->wishlistService->getPriceDropStats();
        $mostWishlisted = $this->wishlistService->getMostWishlistedProducts(20);

        return view('customer-wishlists.statistics', compact('stats', 'growthStats', 'priceDropStats', 'mostWishlisted'));
    }

    /**
     * Display wishlist templates interface
     */
    public function templates(): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        return view('customer-wishlists.templates');
    }

    /**
     * Display wishlist backup and restore interface
     */
    public function backup(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $exportData = $this->wishlistService->exportWishlist($customerId);

        return view('customer-wishlists.backup', compact('exportData', 'customerId'));
    }

    /**
     * Display wishlist audit trail interface
     */
    public function auditTrail(int $customerId): View
    {
        $this->authorize('viewAny', CustomerWishlist::class);

        $recentAdditions = $this->wishlistService->getRecentWishlistAdditionsByCustomer($customerId, 50);

        return view('customer-wishlists.audit-trail', compact('recentAdditions', 'customerId'));
    }
}
