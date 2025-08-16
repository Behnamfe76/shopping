<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Services\ProductReviewService;
use Fereydooni\Shopping\app\Models\ProductReview;
use Fereydooni\Shopping\app\DTOs\ProductReviewDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static ProductReview|null find(int $id)
 * @method static ProductReviewDTO|null findDTO(int $id)
 * @method static ProductReview create(array $data)
 * @method static ProductReviewDTO createAndReturnDTO(array $data)
 * @method static bool update(ProductReview $review, array $data)
 * @method static bool delete(ProductReview $review)
 * @method static Collection findByProductId(int $productId)
 * @method static Collection findByUserId(int $userId)
 * @method static Collection findByRating(int $rating)
 * @method static Collection findByStatus(string $status)
 * @method static ProductReview|null findByProductIdAndUserId(int $productId, int $userId)
 * @method static Collection findByProductIdAndRating(int $productId, int $rating)
 * @method static Collection findByProductIdAndStatus(int $productId, string $status)
 * @method static Collection findApproved()
 * @method static Collection findPending()
 * @method static Collection findRejected()
 * @method static Collection findFeatured()
 * @method static Collection findVerified()
 * @method static Collection findByDateRange(string $startDate, string $endDate)
 * @method static Collection findRecent(int $days = 30)
 * @method static Collection findPopular(int $limit = 10)
 * @method static Collection findHelpful(int $limit = 10)
 * @method static Collection findBySentimentScore(float $minScore, float $maxScore)
 * @method static Collection findPositive()
 * @method static Collection findNegative()
 * @method static Collection findNeutral()
 * @method static bool approve(ProductReview $review)
 * @method static bool reject(ProductReview $review, string $reason = null)
 * @method static bool feature(ProductReview $review)
 * @method static bool unfeature(ProductReview $review)
 * @method static bool verify(ProductReview $review)
 * @method static bool unverify(ProductReview $review)
 * @method static bool incrementHelpfulVotes(ProductReview $review)
 * @method static bool decrementHelpfulVotes(ProductReview $review)
 * @method static bool addVote(ProductReview $review, bool $isHelpful)
 * @method static bool removeVote(ProductReview $review, int $userId)
 * @method static Collection search(string $query)
 * @method static int getReviewCount()
 * @method static int getReviewCountByProductId(int $productId)
 * @method static int getReviewCountByUserId(int $userId)
 * @method static int getReviewCountByRating(int $rating)
 * @method static int getReviewCountByStatus(string $status)
 * @method static float getAverageRating(int $productId)
 * @method static array getRatingDistribution(int $productId)
 * @method static array getReviewStats(int $productId)
 * @method static array getReviewAnalytics(int $reviewId)
 * @method static array getReviewAnalyticsByProduct(int $productId)
 * @method static array getReviewAnalyticsByUser(int $userId)
 * @method static float analyzeSentiment(string $text)
 * @method static bool moderateReview(ProductReview $review)
 * @method static bool flagReview(ProductReview $review, string $reason)
 * @method static Collection getFlaggedReviews()
 * @method static Collection getModerationQueue()
 * @method static array validateReview(array $data)
 * @method static array getValidationMessages()
 * @method static bool canUserReviewProduct(int $userId, int $productId)
 * @method static array getProductReviewSummary(int $productId)
 * @method static Collection getTopReviewedProducts(int $limit = 10)
 * @method static array getReviewTrends(int $productId, int $days = 30)
 */
class ProductReview extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'shopping.product-review.facade';
    }
}
