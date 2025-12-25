<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\CustomerCommunication;

trait HasCustomerCommunicationAnalytics
{
    /**
     * Calculate delivery rate.
     */
    public function getDeliveryRate(): float
    {
        $sentCount = $this->repository->getSentCount();
        $deliveredCount = $this->repository->getDeliveredCount();

        if ($sentCount === 0) {
            return 0.0;
        }

        return round(($deliveredCount / $sentCount) * 100, 2);
    }

    /**
     * Calculate delivery rate by campaign.
     */
    public function getDeliveryRateByCampaign(int $campaignId): float
    {
        return $this->repository->getDeliveryRateByCampaign($campaignId);
    }

    /**
     * Calculate delivery rate by segment.
     */
    public function getDeliveryRateBySegment(int $segmentId): float
    {
        return $this->repository->getDeliveryRateBySegment($segmentId);
    }

    /**
     * Calculate open rate.
     */
    public function getOpenRate(): float
    {
        $deliveredCount = $this->repository->getDeliveredCount();
        $openedCount = $this->repository->getOpenedCount();

        if ($deliveredCount === 0) {
            return 0.0;
        }

        return round(($openedCount / $deliveredCount) * 100, 2);
    }

    /**
     * Calculate open rate by campaign.
     */
    public function getOpenRateByCampaign(int $campaignId): float
    {
        return $this->repository->getOpenRateByCampaign($campaignId);
    }

    /**
     * Calculate open rate by segment.
     */
    public function getOpenRateBySegment(int $segmentId): float
    {
        return $this->repository->getOpenRateBySegment($segmentId);
    }

    /**
     * Calculate click rate.
     */
    public function getClickRate(): float
    {
        $openedCount = $this->repository->getOpenedCount();
        $clickedCount = $this->repository->getClickedCount();

        if ($openedCount === 0) {
            return 0.0;
        }

        return round(($clickedCount / $openedCount) * 100, 2);
    }

    /**
     * Calculate click rate by campaign.
     */
    public function getClickRateByCampaign(int $campaignId): float
    {
        return $this->repository->getClickRateByCampaign($campaignId);
    }

    /**
     * Calculate click rate by segment.
     */
    public function getClickRateBySegment(int $segmentId): float
    {
        return $this->repository->getClickRateBySegment($segmentId);
    }

    /**
     * Calculate bounce rate.
     */
    public function getBounceRate(): float
    {
        $sentCount = $this->repository->getSentCount();
        $bouncedCount = $this->repository->getBouncedCount();

        if ($sentCount === 0) {
            return 0.0;
        }

        return round(($bouncedCount / $sentCount) * 100, 2);
    }

    /**
     * Calculate bounce rate by campaign.
     */
    public function getBounceRateByCampaign(int $campaignId): float
    {
        return $this->repository->getBounceRateByCampaign($campaignId);
    }

    /**
     * Calculate bounce rate by segment.
     */
    public function getBounceRateBySegment(int $segmentId): float
    {
        return $this->repository->getBounceRateBySegment($segmentId);
    }

    /**
     * Calculate unsubscribe rate.
     */
    public function getUnsubscribeRate(): float
    {
        $sentCount = $this->repository->getSentCount();
        $unsubscribedCount = $this->repository->getUnsubscribedCount();

        if ($sentCount === 0) {
            return 0.0;
        }

        return round(($unsubscribedCount / $sentCount) * 100, 2);
    }

    /**
     * Calculate unsubscribe rate by campaign.
     */
    public function getUnsubscribeRateByCampaign(int $campaignId): float
    {
        return $this->repository->getUnsubscribeRateByCampaign($campaignId);
    }

    /**
     * Calculate unsubscribe rate by segment.
     */
    public function getUnsubscribeRateBySegment(int $segmentId): float
    {
        return $this->repository->getUnsubscribeRateBySegment($segmentId);
    }

    /**
     * Get communication performance analytics.
     */
    public function getCommunicationPerformanceStats(): array
    {
        return [
            'total_communications' => $this->repository->getCommunicationCount(),
            'scheduled_count' => $this->repository->getScheduledCount(),
            'sent_count' => $this->repository->getSentCount(),
            'delivered_count' => $this->repository->getDeliveredCount(),
            'opened_count' => $this->repository->getOpenedCount(),
            'clicked_count' => $this->repository->getClickedCount(),
            'bounced_count' => $this->repository->getBouncedCount(),
            'unsubscribed_count' => $this->repository->getUnsubscribedCount(),
            'delivery_rate' => $this->getDeliveryRate(),
            'open_rate' => $this->getOpenRate(),
            'click_rate' => $this->getClickRate(),
            'bounce_rate' => $this->getBounceRate(),
            'unsubscribe_rate' => $this->getUnsubscribeRate(),
        ];
    }

    /**
     * Get communication performance analytics by campaign.
     */
    public function getCommunicationPerformanceStatsByCampaign(int $campaignId): array
    {
        return [
            'campaign_id' => $campaignId,
            'total_communications' => $this->repository->getCommunicationCountByCampaign($campaignId),
            'delivery_rate' => $this->getDeliveryRateByCampaign($campaignId),
            'open_rate' => $this->getOpenRateByCampaign($campaignId),
            'click_rate' => $this->getClickRateByCampaign($campaignId),
            'bounce_rate' => $this->getBounceRateByCampaign($campaignId),
            'unsubscribe_rate' => $this->getUnsubscribeRateByCampaign($campaignId),
        ];
    }

    /**
     * Get communication performance analytics by segment.
     */
    public function getCommunicationPerformanceStatsBySegment(int $segmentId): array
    {
        return [
            'segment_id' => $segmentId,
            'total_communications' => $this->repository->getCommunicationCountBySegment($segmentId),
            'delivery_rate' => $this->getDeliveryRateBySegment($segmentId),
            'open_rate' => $this->getOpenRateBySegment($segmentId),
            'click_rate' => $this->getClickRateBySegment($segmentId),
            'bounce_rate' => $this->getBounceRateBySegment($segmentId),
            'unsubscribe_rate' => $this->getUnsubscribeRateBySegment($segmentId),
        ];
    }

    /**
     * Get communication engagement analytics.
     */
    public function getCommunicationEngagementStats(): array
    {
        $stats = $this->repository->getCommunicationStats();

        return [
            'total_communications' => $stats['total'] ?? 0,
            'engagement_rate' => $this->calculateEngagementRate(),
            'average_open_time' => $this->calculateAverageOpenTime(),
            'average_click_time' => $this->calculateAverageClickTime(),
            'most_engaged_customers' => $this->getMostEngagedCustomers(),
            'least_engaged_customers' => $this->getLeastEngagedCustomers(),
            'engagement_trends' => $this->getEngagementTrends(),
        ];
    }

    /**
     * Get communication engagement analytics by customer.
     */
    public function getCommunicationEngagementStatsByCustomer(int $customerId): array
    {
        $customerCommunications = $this->repository->findByCustomerId($customerId);

        return [
            'customer_id' => $customerId,
            'total_communications' => $customerCommunications->count(),
            'engagement_rate' => $this->calculateCustomerEngagementRate($customerId),
            'average_open_time' => $this->calculateCustomerAverageOpenTime($customerId),
            'average_click_time' => $this->calculateCustomerAverageClickTime($customerId),
            'preferred_channels' => $this->getCustomerPreferredChannels($customerId),
            'engagement_history' => $this->getCustomerEngagementHistory($customerId),
        ];
    }

    /**
     * Get communication growth analytics.
     */
    public function getCommunicationGrowthStats(string $period = 'monthly'): array
    {
        return $this->repository->getCommunicationGrowthStats($period);
    }

    /**
     * Get communication growth analytics by customer.
     */
    public function getCommunicationGrowthStatsByCustomer(int $customerId, string $period = 'monthly'): array
    {
        return $this->repository->getCommunicationGrowthStatsByCustomer($customerId, $period);
    }

    /**
     * Get communication recommendations.
     */
    public function getCommunicationRecommendations(int $customerId): array
    {
        return $this->repository->getCommunicationRecommendations($customerId);
    }

    /**
     * Get communication insights.
     */
    public function getCommunicationInsights(int $customerId): array
    {
        return $this->repository->getCommunicationInsights($customerId);
    }

    /**
     * Get communication trends.
     */
    public function getCommunicationTrends(int $customerId, string $period = 'monthly'): array
    {
        return $this->repository->getCommunicationTrends($customerId, $period);
    }

    /**
     * Get communication comparison between two customers.
     */
    public function getCommunicationComparison(int $customerId1, int $customerId2): array
    {
        return $this->repository->getCommunicationComparison($customerId1, $customerId2);
    }

    /**
     * Get communication forecast.
     */
    public function getCommunicationForecast(int $customerId): array
    {
        return $this->repository->getCommunicationForecast($customerId);
    }

    /**
     * Calculate overall engagement rate.
     */
    private function calculateEngagementRate(): float
    {
        $totalCommunications = $this->repository->getCommunicationCount();
        $engagedCommunications = $this->repository->getOpenedCount() + $this->repository->getClickedCount();

        if ($totalCommunications === 0) {
            return 0.0;
        }

        return round(($engagedCommunications / $totalCommunications) * 100, 2);
    }

    /**
     * Calculate average open time.
     */
    private function calculateAverageOpenTime(): float
    {
        // This would require more complex logic to calculate time between sent and opened
        // For now, return a placeholder
        return 0.0;
    }

    /**
     * Calculate average click time.
     */
    private function calculateAverageClickTime(): float
    {
        // This would require more complex logic to calculate time between opened and clicked
        // For now, return a placeholder
        return 0.0;
    }

    /**
     * Get most engaged customers.
     */
    private function getMostEngagedCustomers(): array
    {
        // This would require complex logic to identify most engaged customers
        // For now, return a placeholder
        return [];
    }

    /**
     * Get least engaged customers.
     */
    private function getLeastEngagedCustomers(): array
    {
        // This would require complex logic to identify least engaged customers
        // For now, return a placeholder
        return [];
    }

    /**
     * Get engagement trends.
     */
    private function getEngagementTrends(): array
    {
        // This would require complex logic to calculate engagement trends over time
        // For now, return a placeholder
        return [];
    }

    /**
     * Calculate customer engagement rate.
     */
    private function calculateCustomerEngagementRate(int $customerId): float
    {
        $customerCommunications = $this->repository->findByCustomerId($customerId);
        $totalCommunications = $customerCommunications->count();

        if ($totalCommunications === 0) {
            return 0.0;
        }

        $engagedCommunications = $customerCommunications->filter(function ($communication) {
            return $communication->isOpened() || $communication->isClicked();
        })->count();

        return round(($engagedCommunications / $totalCommunications) * 100, 2);
    }

    /**
     * Calculate customer average open time.
     */
    private function calculateCustomerAverageOpenTime(int $customerId): float
    {
        // This would require more complex logic
        // For now, return a placeholder
        return 0.0;
    }

    /**
     * Calculate customer average click time.
     */
    private function calculateCustomerAverageClickTime(int $customerId): float
    {
        // This would require more complex logic
        // For now, return a placeholder
        return 0.0;
    }

    /**
     * Get customer preferred channels.
     */
    private function getCustomerPreferredChannels(int $customerId): array
    {
        $customerCommunications = $this->repository->findByCustomerId($customerId);

        return $customerCommunications->groupBy('channel')
            ->map(function ($communications) {
                return $communications->count();
            })
            ->sortDesc()
            ->toArray();
    }

    /**
     * Get customer engagement history.
     */
    private function getCustomerEngagementHistory(int $customerId): array
    {
        $customerCommunications = $this->repository->findByCustomerId($customerId);

        return $customerCommunications->map(function ($communication) {
            return [
                'id' => $communication->id,
                'type' => $communication->communication_type,
                'status' => $communication->status,
                'sent_at' => $communication->sent_at,
                'opened_at' => $communication->opened_at,
                'clicked_at' => $communication->clicked_at,
                'engagement_level' => $this->calculateCommunicationEngagementLevel($communication),
            ];
        })->toArray();
    }

    /**
     * Calculate communication engagement level.
     */
    private function calculateCommunicationEngagementLevel(CustomerCommunication $communication): string
    {
        if ($communication->isClicked()) {
            return 'high';
        } elseif ($communication->isOpened()) {
            return 'medium';
        } elseif ($communication->isDelivered()) {
            return 'low';
        } else {
            return 'none';
        }
    }
}
