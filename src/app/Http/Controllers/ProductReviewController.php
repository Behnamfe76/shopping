<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Fereydooni\Shopping\app\Http\Requests\ApproveProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\FlagProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\RejectProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\VoteProductReviewRequest;
use Fereydooni\Shopping\app\Models\ProductReview;
use Fereydooni\Shopping\app\Services\ProductReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    protected ProductReviewService $productReviewService;

    public function __construct(ProductReviewService $productReviewService)
    {
        $this->productReviewService = $productReviewService;
        $this->authorizeResource(ProductReview::class, 'review');
    }

    /**
     * Display a listing of product reviews.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $perPage = $request->get('per_page', 15);
        $reviews = $this->productReviewService->paginate($perPage);

        return view('shopping::product-reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new product review.
     */
    public function create(): View
    {
        $this->authorize('create', ProductReview::class);

        return view('shopping::product-reviews.create');
    }

    /**
     * Store a newly created product review in storage.
     */
    public function store(StoreProductReviewRequest $request): RedirectResponse
    {
        $this->authorize('create', ProductReview::class);

        $review = $this->productReviewService->create($request->validated());

        return redirect()->route('product-reviews.show', $review)
            ->with('success', 'Product review created successfully.');
    }

    /**
     * Display the specified product review.
     */
    public function show(ProductReview $review): View
    {
        $this->authorize('view', $review);

        return view('shopping::product-reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified product review.
     */
    public function edit(ProductReview $review): View
    {
        $this->authorize('update', $review);

        return view('shopping::product-reviews.edit', compact('review'));
    }

    /**
     * Update the specified product review in storage.
     */
    public function update(UpdateProductReviewRequest $request, ProductReview $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $this->productReviewService->update($review, $request->validated());

        return redirect()->route('product-reviews.show', $review)
            ->with('success', 'Product review updated successfully.');
    }

    /**
     * Remove the specified product review from storage.
     */
    public function destroy(ProductReview $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $this->productReviewService->delete($review);

        return redirect()->route('product-reviews.index')
            ->with('success', 'Product review deleted successfully.');
    }

    /**
     * Approve the specified product review.
     */
    public function approve(ApproveProductReviewRequest $request, ProductReview $review): RedirectResponse
    {
        $this->authorize('approve', $review);

        $this->productReviewService->approve($review);

        return redirect()->back()
            ->with('success', 'Product review approved successfully.');
    }

    /**
     * Reject the specified product review.
     */
    public function reject(RejectProductReviewRequest $request, ProductReview $review): RedirectResponse
    {
        $this->authorize('reject', $review);

        $this->productReviewService->reject($review, $request->get('reason'));

        return redirect()->back()
            ->with('success', 'Product review rejected successfully.');
    }

    /**
     * Feature the specified product review.
     */
    public function feature(ProductReview $review): RedirectResponse
    {
        $this->authorize('feature', $review);

        $this->productReviewService->feature($review);

        return redirect()->back()
            ->with('success', 'Product review featured successfully.');
    }

    /**
     * Unfeature the specified product review.
     */
    public function unfeature(ProductReview $review): RedirectResponse
    {
        $this->authorize('feature', $review);

        $this->productReviewService->unfeature($review);

        return redirect()->back()
            ->with('success', 'Product review unfeatured successfully.');
    }

    /**
     * Verify the specified product review.
     */
    public function verify(ProductReview $review): RedirectResponse
    {
        $this->authorize('verify', $review);

        $this->productReviewService->verify($review);

        return redirect()->back()
            ->with('success', 'Product review verified successfully.');
    }

    /**
     * Unverify the specified product review.
     */
    public function unverify(ProductReview $review): RedirectResponse
    {
        $this->authorize('verify', $review);

        $this->productReviewService->unverify($review);

        return redirect()->back()
            ->with('success', 'Product review unverified successfully.');
    }

    /**
     * Vote on the specified product review.
     */
    public function vote(VoteProductReviewRequest $request, ProductReview $review): RedirectResponse
    {
        $this->authorize('vote', $review);

        $isHelpful = $request->get('is_helpful', true);
        $this->productReviewService->addVote($review, $isHelpful);

        return redirect()->back()
            ->with('success', 'Vote recorded successfully.');
    }

    /**
     * Flag the specified product review.
     */
    public function flag(FlagProductReviewRequest $request, ProductReview $review): RedirectResponse
    {
        $this->authorize('flag', $review);

        $this->productReviewService->flagReview($review, $request->get('reason'));

        return redirect()->back()
            ->with('success', 'Product review flagged successfully.');
    }

    /**
     * Search product reviews.
     */
    public function search(SearchProductReviewRequest $request): View
    {
        $this->authorize('search', ProductReview::class);

        $query = $request->get('query');
        $reviews = $this->productReviewService->search($query);

        return view('shopping::product-reviews.search', compact('reviews', 'query'));
    }

    /**
     * Display approved product reviews.
     */
    public function approved(): View
    {
        $this->authorize('viewApproved', ProductReview::class);

        $reviews = $this->productReviewService->findApproved();

        return view('shopping::product-reviews.approved', compact('reviews'));
    }

    /**
     * Display pending product reviews.
     */
    public function pending(): View
    {
        $this->authorize('viewPending', ProductReview::class);

        $reviews = $this->productReviewService->findPending();

        return view('shopping::product-reviews.pending', compact('reviews'));
    }

    /**
     * Display rejected product reviews.
     */
    public function rejected(): View
    {
        $this->authorize('viewRejected', ProductReview::class);

        $reviews = $this->productReviewService->findRejected();

        return view('shopping::product-reviews.rejected', compact('reviews'));
    }

    /**
     * Display featured product reviews.
     */
    public function featured(): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findFeatured();

        return view('shopping::product-reviews.featured', compact('reviews'));
    }

    /**
     * Display verified product reviews.
     */
    public function verified(): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findVerified();

        return view('shopping::product-reviews.verified', compact('reviews'));
    }

    /**
     * Display product reviews by product.
     */
    public function byProduct(Request $request, $productId): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findByProductId($productId);

        return view('shopping::product-reviews.by-product', compact('reviews', 'productId'));
    }

    /**
     * Display product reviews by user.
     */
    public function byUser(Request $request, $userId): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findByUserId($userId);

        return view('shopping::product-reviews.by-user', compact('reviews', 'userId'));
    }

    /**
     * Display product reviews by rating.
     */
    public function byRating(Request $request, $rating): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findByRating($rating);

        return view('shopping::product-reviews.by-rating', compact('reviews', 'rating'));
    }

    /**
     * Display recent product reviews.
     */
    public function recent(Request $request): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $days = $request->get('days', 30);
        $reviews = $this->productReviewService->findRecent($days);

        return view('shopping::product-reviews.recent', compact('reviews', 'days'));
    }

    /**
     * Display popular product reviews.
     */
    public function popular(Request $request): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $limit = $request->get('limit', 10);
        $reviews = $this->productReviewService->findPopular($limit);

        return view('shopping::product-reviews.popular', compact('reviews'));
    }

    /**
     * Display helpful product reviews.
     */
    public function helpful(Request $request): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $limit = $request->get('limit', 10);
        $reviews = $this->productReviewService->findHelpful($limit);

        return view('shopping::product-reviews.helpful', compact('reviews'));
    }

    /**
     * Display positive product reviews.
     */
    public function positive(): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findPositive();

        return view('shopping::product-reviews.positive', compact('reviews'));
    }

    /**
     * Display negative product reviews.
     */
    public function negative(): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findNegative();

        return view('shopping::product-reviews.negative', compact('reviews'));
    }

    /**
     * Display neutral product reviews.
     */
    public function neutral(): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findNeutral();

        return view('shopping::product-reviews.neutral', compact('reviews'));
    }

    /**
     * Display flagged product reviews.
     */
    public function flagged(): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->getFlaggedReviews();

        return view('shopping::product-reviews.flagged', compact('reviews'));
    }

    /**
     * Display moderation queue.
     */
    public function moderationQueue(): View
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->getModerationQueue();

        return view('shopping::product-reviews.moderation-queue', compact('reviews'));
    }

    /**
     * Display review statistics for a product.
     */
    public function stats(Request $request, $productId): View
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $stats = $this->productReviewService->getReviewStats($productId);

        return view('shopping::product-reviews.stats', compact('stats', 'productId'));
    }

    /**
     * Display rating distribution for a product.
     */
    public function ratingDistribution(Request $request, $productId): View
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $distribution = $this->productReviewService->getRatingDistribution($productId);

        return view('shopping::product-reviews.rating-distribution', compact('distribution', 'productId'));
    }

    /**
     * Display average rating for a product.
     */
    public function averageRating(Request $request, $productId): View
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $averageRating = $this->productReviewService->getAverageRating($productId);

        return view('shopping::product-reviews.average-rating', compact('averageRating', 'productId'));
    }

    /**
     * Display analytics for a specific review.
     */
    public function analytics(Request $request, ProductReview $review): View
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $analytics = $this->productReviewService->getReviewAnalytics($review->id);

        return view('shopping::product-reviews.analytics', compact('analytics', 'review'));
    }

    /**
     * Display analytics for reviews by product.
     */
    public function analyticsByProduct(Request $request, $productId): View
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $analytics = $this->productReviewService->getReviewAnalyticsByProduct($productId);

        return view('shopping::product-reviews.analytics-by-product', compact('analytics', 'productId'));
    }

    /**
     * Display analytics for reviews by user.
     */
    public function analyticsByUser(Request $request, $userId): View
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $analytics = $this->productReviewService->getReviewAnalyticsByUser($userId);

        return view('shopping::product-reviews.analytics-by-user', compact('analytics', 'userId'));
    }
}
