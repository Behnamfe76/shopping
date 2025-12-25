<?php

namespace Fereydooni\Shopping\App\Facades;

use App\DTOs\ProviderRatingDTO;
use App\Models\ProviderRating;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static ProviderRating|null find(int $id)
 * @method static ProviderRatingDTO|null findDTO(int $id)
 * @method static Collection findByProviderId(int $providerId)
 * @method static Collection findByProviderIdDTO(int $providerId)
 * @method static Collection findByUserId(int $userId)
 * @method static Collection findByUserIdDTO(int $userId)
 * @method static Collection findByCategory(string $category)
 * @method static Collection findByCategoryDTO(string $category)
 * @method static Collection findByStatus(string $status)
 * @method static Collection findByStatusDTO(string $status)
 * @method static Collection findByRatingValue(float $ratingValue)
 * @method static Collection findByRatingValueDTO(float $ratingValue)
 * @method static Collection findByRatingRange(float $minRating, float $maxRating)
 * @method static Collection findByRatingRangeDTO(float $minRating, float $maxRating)
 * @method static Collection findByProviderAndCategory(int $providerId, string $category)
 * @method static Collection findByProviderAndCategoryDTO(int $providerId, string $category)
 * @method static Collection findByProviderAndUser(int $providerId, int $userId)
 * @method static Collection findByProviderAndUserDTO(int $providerId, int $userId)
 * @method static Collection findVerified()
 * @method static Collection findVerifiedDTO()
 * @method static Collection findUnverified()
 * @method static Collection findUnverifiedDTO()
 * @method static Collection findApproved()
 * @method static Collection findApprovedDTO()
 * @method static Collection findPending()
 * @method static Collection findPendingDTO()
 * @method static Collection findFlagged()
 * @method static Collection findFlaggedDTO()
 * @method static Collection findRecommended()
 * @method static Collection findRecommendedDTO()
 * @method static Collection findNotRecommended()
 * @method static Collection findNotRecommendedDTO()
 * @method static Collection findByDateRange(string $startDate, string $endDate)
 * @method static Collection findByDateRangeDTO(string $startDate, string $endDate)
 * @method static ProviderRating create(array $data)
 * @method static ProviderRatingDTO createAndReturnDTO(array $data)
 * @method static bool update(ProviderRating $rating, array $data)
 * @method static ProviderRatingDTO|null updateAndReturnDTO(ProviderRating $rating, array $data)
 * @method static bool delete(ProviderRating $rating)
 * @method static bool approve(ProviderRating $rating)
 * @method static bool reject(ProviderRating $rating, string $reason = null)
 * @method static bool flag(ProviderRating $rating, string $reason = null)
 * @method static bool verify(ProviderRating $rating)
 * @method static bool addHelpfulVote(ProviderRating $rating, int $userId)
 * @method static bool removeHelpfulVote(ProviderRating $rating, int $userId)
 * @method static float getProviderAverageRating(int $providerId, string $category = null)
 * @method static int getProviderRatingCount(int $providerId, string $category = null)
 * @method static array getProviderRatingBreakdown(int $providerId)
 * @method static float getProviderRecommendationPercentage(int $providerId)
 * @method static int getUserRatingCount(int $userId)
 * @method static float getUserAverageRating(int $userId)
 * @method static int getTotalRatingCount()
 * @method static float getAverageRating()
 * @method static int getRatingCountByValue(float $ratingValue)
 * @method static int getRatingCountByCategory(string $category)
 * @method static int getVerifiedRatingCount()
 * @method static int getPendingRatingCount()
 * @method static int getFlaggedRatingCount()
 * @method static float getRecommendationPercentage()
 * @method static Collection searchRatings(string $query)
 * @method static Collection searchRatingsDTO(string $query)
 * @method static Collection searchRatingsByProvider(int $providerId, string $query)
 * @method static Collection searchRatingsByProviderDTO(int $providerId, string $query)
 * @method static string exportRatingData(array $filters = [])
 * @method static bool importRatingData(string $data)
 * @method static array getRatingStatistics()
 * @method static array getProviderRatingStatistics(int $providerId)
 * @method static array getRatingTrends(string $startDate = null, string $endDate = null)
 * @method static array calculateRatingMetrics(int $providerId)
 * @method static Collection getMostHelpfulRatings(int $limit = 10)
 * @method static Collection getMostHelpfulRatingsDTO(int $limit = 10)
 * @method static Collection getTopRatedProviders(int $limit = 10)
 * @method static Collection getTopRatedProvidersDTO(int $limit = 10)
 * @method static Collection getPendingRatingsForModeration()
 * @method static Collection getFlaggedRatingsForReview()
 * @method static Collection getRejectedRatings()
 * @method static bool approveRating(int $ratingId, int $moderatorId, string $notes = null)
 * @method static bool rejectRating(int $ratingId, int $moderatorId, string $reason, string $notes = null)
 * @method static bool flagRating(int $ratingId, int $moderatorId, string $reason, string $notes = null)
 * @method static bool verifyRating(int $ratingId)
 * @method static array getModerationStatistics()
 * @method static array bulkApproveRatings(array $ratingIds, int $moderatorId)
 * @method static array bulkRejectRatings(array $ratingIds, int $moderatorId, string $reason)
 * @method static Collection getRatingsRequiringAttention()
 * @method static bool isRatingContentAppropriate(string $content)
 * @method static int autoFlagSuspiciousRatings()
 * @method static bool addHelpfulVote(int $ratingId, int $userId)
 * @method static bool removeHelpfulVote(int $ratingId, int $userId)
 * @method static bool addVote(int $ratingId, int $userId, bool $isHelpful)
 * @method static Collection getMostHelpfulRatings(int $limit = 10)
 * @method static Collection getMostHelpfulRatingsDTO(int $limit = 10)
 * @method static Collection getRatingsByHelpfulness(float $minPercentage = 80.0)
 * @method static Collection getUserVotingHistory(int $userId)
 * @method static bool hasUserVoted(int $ratingId, int $userId)
 * @method static bool|null getUserVote(int $ratingId, int $userId)
 * @method static array getRatingVoteStatistics(int $ratingId)
 * @method static array getProviderVoteStatistics(int $providerId)
 * @method static Collection getTrendingRatings(int $limit = 10)
 * @method static Collection getControversialRatings(int $limit = 10)
 * @method static float getUserVotingImpactScore(int $userId)
 * @method static float getRatingQualityScore(int $ratingId)
 * @method static array getRatingStatistics()
 * @method static array getProviderRatingStatistics(int $providerId)
 * @method static array getRatingTrends(string $startDate = null, string $endDate = null)
 * @method static array calculateRatingMetrics(int $providerId)
 * @method static Collection getTopRatedProviders(int $limit = 10)
 * @method static Collection getTopRatedProvidersDTO(int $limit = 10)
 * @method static array getRatingDistribution()
 * @method static array getCategoryDistribution()
 * @method static array getStatusDistribution()
 * @method static array getProviderCategoryBreakdown(int $providerId)
 * @method static array getProviderRatingTrends(int $providerId, int $months = 6)
 * @method static array calculateProviderQualityMetrics(int $providerId)
 * @method static array getRatingPerformanceComparison(int $providerId)
 */
class ProviderRating extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'provider-rating';
    }

    /**
     * Get the service provider class name.
     */
    protected static function getFacadeAccessorName(): string
    {
        return 'Fereydooni\Shopping\App\Services\ProviderRatingService';
    }

    /**
     * Get the service provider class.
     */
    protected static function getFacadeAccessorClass(): string
    {
        return \Fereydooni\Shopping\App\Services\ProviderRatingService::class;
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorInstance(): object
    {
        return app(static::getFacadeAccessor());
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorService(): object
    {
        return app(static::getFacadeAccessor());
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorRepository(): object
    {
        return app(\App\Repositories\Interfaces\ProviderRatingRepositoryInterface::class);
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorModel(): string
    {
        return \App\Models\ProviderRating::class;
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorDTO(): string
    {
        return \App\DTOs\ProviderRatingDTO::class;
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorInterface(): string
    {
        return \App\Repositories\Interfaces\ProviderRatingRepositoryInterface::class;
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorImplementation(): string
    {
        return \App\Repositories\ProviderRatingRepository::class;
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorTraits(): array
    {
        return [
            \Fereydooni\Shopping\App\Traits\HasProviderRatingOperations::class,
            \Fereydooni\Shopping\App\Traits\HasProviderRatingModeration::class,
            \Fereydooni\Shopping\App\Traits\HasProviderRatingVoting::class,
            \Fereydooni\Shopping\App\Traits\HasProviderRatingAnalytics::class,
        ];
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorEvents(): array
    {
        return [
            \Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingCreated::class,
            \Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingUpdated::class,
            \Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingApproved::class,
            \Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingRejected::class,
            \Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingFlagged::class,
            \Fereydooni\Shopping\App\Events\ProviderRating\ProviderRatingVerified::class,
        ];
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorListeners(): array
    {
        return [
            \Fereydooni\Shopping\App\Listeners\ProviderRating\SendProviderRatingNotification::class,
            \Fereydooni\Shopping\App\Listeners\ProviderRating\UpdateProviderRatingRecord::class,
            \Fereydooni\Shopping\App\Listeners\ProviderRating\LogProviderRatingActivity::class,
            \Fereydooni\Shopping\App\Listeners\ProviderRating\UpdateProviderRatingMetrics::class,
            \Fereydooni\Shopping\App\Listeners\ProviderRating\ProcessRatingModeration::class,
        ];
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorNotifications(): array
    {
        return [
            \Fereydooni\Shopping\App\Notifications\ProviderRating\RatingReceived::class,
            \Fereydooni\Shopping\App\Notifications\ProviderRating\RatingApproved::class,
            \Fereydooni\Shopping\App\Notifications\ProviderRating\RatingRejected::class,
            \Fereydooni\Shopping\App\Notifications\ProviderRating\RatingFlagged::class,
            \Fereydooni\Shopping\App\Notifications\ProviderRating\RatingVerified::class,
            \Fereydooni\Shopping\App\Notifications\ProviderRating\RatingHelpfulVote::class,
        ];
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorPermissions(): array
    {
        return [
            'provider-rating.view',
            'provider-rating.create',
            'provider-rating.edit',
            'provider-rating.delete',
            'provider-rating.moderate',
            'provider-rating.verify',
            'provider-rating.flag',
            'provider-rating.view-own',
            'provider-rating.create-own',
            'provider-rating.edit-own',
            'provider-rating.view-all',
            'provider-rating.manage-all',
            'provider-rating.export',
            'provider-rating.import',
            'provider-rating.statistics',
            'provider-rating.vote',
        ];
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorModels(): array
    {
        return [
            'ProviderRating' => \App\Models\ProviderRating::class,
            'ProviderRatingVote' => \App\Models\ProviderRatingVote::class,
            'Provider' => \App\Models\Provider::class,
            'User' => \App\Models\User::class,
        ];
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorDTOs(): array
    {
        return [
            'ProviderRatingDTO' => \App\DTOs\ProviderRatingDTO::class,
        ];
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorRepositories(): array
    {
        return [
            'ProviderRatingRepositoryInterface' => \App\Repositories\Interfaces\ProviderRatingRepositoryInterface::class,
            'ProviderRatingRepository' => \App\Repositories\ProviderRatingRepository::class,
        ];
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorServices(): array
    {
        return [
            'ProviderRatingService' => \Fereydooni\Shopping\App\Services\ProviderRatingService::class,
        ];
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorProviders(): array
    {
        return [
            'ProviderRatingServiceProvider' => \Fereydooni\Shopping\App\Providers\ProviderRatingServiceProvider::class,
        ];
    }

    /**
     * Get the service provider instance.
     */
    protected static function getFacadeAccessorConfig(): array
    {
        return [
            'provider_rating' => [
                'default_per_page' => 15,
                'max_per_page' => 100,
                'cache_ttl' => 3600,
                'moderation' => [
                    'auto_flag_suspicious' => true,
                    'suspicious_patterns' => [
                        'multiple_ratings_from_same_ip' => 3,
                        'inappropriate_content' => true,
                        'extreme_ratings_from_new_users' => true,
                    ],
                ],
                'voting' => [
                    'require_verification' => true,
                    'max_votes_per_user' => 1,
                ],
                'analytics' => [
                    'confidence_interval_level' => 0.95,
                    'trend_analysis_months' => 6,
                ],
            ],
        ];
    }
}
