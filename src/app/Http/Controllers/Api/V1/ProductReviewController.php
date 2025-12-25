<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Fereydooni\Shopping\app\Http\Requests\ApproveProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\FlagProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\RejectProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateProductReviewRequest;
use Fereydooni\Shopping\app\Http\Requests\VoteProductReviewRequest;
use Fereydooni\Shopping\app\Http\Resources\ProductReviewCollection;
use Fereydooni\Shopping\app\Http\Resources\ProductReviewResource;
use Fereydooni\Shopping\app\Models\ProductReview;
use Fereydooni\Shopping\app\Services\ProductReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $perPage = $request->get('per_page', 15);
        $reviews = $this->productReviewService->paginate($perPage);

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Store a newly created product review in storage.
     */
    public function store(StoreProductReviewRequest $request): JsonResponse
    {
        $this->authorize('create', ProductReview::class);

        $review = $this->productReviewService->create($request->validated());

        return response()->json(new ProductReviewResource($review), 201);
    }

    /**
     * Display the specified product review.
     */
    public function show(ProductReview $review): JsonResponse
    {
        $this->authorize('view', $review);

        return response()->json(new ProductReviewResource($review));
    }

    /**
     * Update the specified product review in storage.
     */
    public function update(UpdateProductReviewRequest $request, ProductReview $review): JsonResponse
    {
        $this->authorize('update', $review);

        $this->productReviewService->update($review, $request->validated());

        return response()->json(new ProductReviewResource($review));
    }

    /**
     * Remove the specified product review from storage.
     */
    public function destroy(ProductReview $review): JsonResponse
    {
        $this->authorize('delete', $review);

        $this->productReviewService->delete($review);

        return response()->json(['message' => 'Product review deleted successfully']);
    }

    /**
     * Approve the specified product review.
     */
    public function approve(ApproveProductReviewRequest $request, ProductReview $review): JsonResponse
    {
        $this->authorize('approve', $review);

        $this->productReviewService->approve($review);

        return response()->json(['message' => 'Product review approved successfully']);
    }

    /**
     * Reject the specified product review.
     */
    public function reject(RejectProductReviewRequest $request, ProductReview $review): JsonResponse
    {
        $this->authorize('reject', $review);

        $this->productReviewService->reject($review, $request->get('reason'));

        return response()->json(['message' => 'Product review rejected successfully']);
    }

    /**
     * Feature the specified product review.
     */
    public function feature(ProductReview $review): JsonResponse
    {
        $this->authorize('feature', $review);

        $this->productReviewService->feature($review);

        return response()->json(['message' => 'Product review featured successfully']);
    }

    /**
     * Unfeature the specified product review.
     */
    public function unfeature(ProductReview $review): JsonResponse
    {
        $this->authorize('feature', $review);

        $this->productReviewService->unfeature($review);

        return response()->json(['message' => 'Product review unfeatured successfully']);
    }

    /**
     * Verify the specified product review.
     */
    public function verify(ProductReview $review): JsonResponse
    {
        $this->authorize('verify', $review);

        $this->productReviewService->verify($review);

        return response()->json(['message' => 'Product review verified successfully']);
    }

    /**
     * Unverify the specified product review.
     */
    public function unverify(ProductReview $review): JsonResponse
    {
        $this->authorize('verify', $review);

        $this->productReviewService->unverify($review);

        return response()->json(['message' => 'Product review unverified successfully']);
    }

    /**
     * Vote on the specified product review.
     */
    public function vote(VoteProductReviewRequest $request, ProductReview $review): JsonResponse
    {
        $this->authorize('vote', $review);

        $isHelpful = $request->get('is_helpful', true);
        $this->productReviewService->addVote($review, $isHelpful);

        return response()->json(['message' => 'Vote recorded successfully']);
    }

    /**
     * Flag the specified product review.
     */
    public function flag(FlagProductReviewRequest $request, ProductReview $review): JsonResponse
    {
        $this->authorize('flag', $review);

        $this->productReviewService->flagReview($review, $request->get('reason'));

        return response()->json(['message' => 'Product review flagged successfully']);
    }

    /**
     * Search product reviews.
     */
    public function search(SearchProductReviewRequest $request): JsonResponse
    {
        $this->authorize('search', ProductReview::class);

        $query = $request->get('query');
        $reviews = $this->productReviewService->search($query);

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Get review count.
     */
    public function getCount(): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $count = $this->productReviewService->getReviewCount();

        return response()->json(['count' => $count]);
    }

    /**
     * Display approved product reviews.
     */
    public function approved(): JsonResponse
    {
        $this->authorize('viewApproved', ProductReview::class);

        $reviews = $this->productReviewService->findApproved();

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display pending product reviews.
     */
    public function pending(): JsonResponse
    {
        $this->authorize('viewPending', ProductReview::class);

        $reviews = $this->productReviewService->findPending();

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display rejected product reviews.
     */
    public function rejected(): JsonResponse
    {
        $this->authorize('viewRejected', ProductReview::class);

        $reviews = $this->productReviewService->findRejected();

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display featured product reviews.
     */
    public function featured(): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findFeatured();

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display verified product reviews.
     */
    public function verified(): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findVerified();

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display product reviews by product.
     */
    public function byProduct(Request $request, $productId): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findByProductId($productId);

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display product reviews by user.
     */
    public function byUser(Request $request, $userId): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findByUserId($userId);

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display product reviews by rating.
     */
    public function byRating(Request $request, $rating): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findByRating($rating);

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display recent product reviews.
     */
    public function recent(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $days = $request->get('days', 30);
        $reviews = $this->productReviewService->findRecent($days);

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display popular product reviews.
     */
    public function popular(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $limit = $request->get('limit', 10);
        $reviews = $this->productReviewService->findPopular($limit);

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display helpful product reviews.
     */
    public function helpful(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $limit = $request->get('limit', 10);
        $reviews = $this->productReviewService->findHelpful($limit);

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display positive product reviews.
     */
    public function positive(): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findPositive();

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display negative product reviews.
     */
    public function negative(): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findNegative();

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display neutral product reviews.
     */
    public function neutral(): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->findNeutral();

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display flagged product reviews.
     */
    public function flagged(): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->getFlaggedReviews();

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display moderation queue.
     */
    public function moderationQueue(): JsonResponse
    {
        $this->authorize('viewAny', ProductReview::class);

        $reviews = $this->productReviewService->getModerationQueue();

        return response()->json(new ProductReviewCollection($reviews));
    }

    /**
     * Display review statistics for a product.
     */
    public function stats(Request $request, $productId): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $stats = $this->productReviewService->getReviewStats($productId);

        return response()->json($stats);
    }

    /**
     * Display rating distribution for a product.
     */
    public function ratingDistribution(Request $request, $productId): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $distribution = $this->productReviewService->getRatingDistribution($productId);

        return response()->json($distribution);
    }

    /**
     * Display average rating for a product.
     */
    public function averageRating(Request $request, $productId): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $averageRating = $this->productReviewService->getAverageRating($productId);

        return response()->json(['average_rating' => $averageRating]);
    }

    /**
     * Display analytics for a specific review.
     */
    public function analytics(Request $request, ProductReview $review): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $analytics = $this->productReviewService->getReviewAnalytics($review->id);

        return response()->json($analytics);
    }

    /**
     * Display analytics for reviews by product.
     */
    public function analyticsByProduct(Request $request, $productId): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $analytics = $this->productReviewService->getReviewAnalyticsByProduct($productId);

        return response()->json($analytics);
    }

    /**
     * Display analytics for reviews by user.
     */
    public function analyticsByUser(Request $request, $userId): JsonResponse
    {
        $this->authorize('viewAnalytics', ProductReview::class);

        $analytics = $this->productReviewService->getReviewAnalyticsByUser($userId);

        return response()->json($analytics);
    }
}
