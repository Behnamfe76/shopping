<?php

namespace Fereydooni\Shopping\App\Services;

use Fereydooni\Shopping\App\DTOs\ProviderDTO;
use Fereydooni\Shopping\App\Enums\ProviderStatus;
use Fereydooni\Shopping\App\Enums\ProviderType;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderRepositoryInterface;
use Fereydooni\Shopping\App\Traits\HasProviderAnalytics;
use Fereydooni\Shopping\App\Traits\HasProviderContractManagement;
use Fereydooni\Shopping\App\Traits\HasProviderFinancialManagement;
use Fereydooni\Shopping\App\Traits\HasProviderOperations;
use Fereydooni\Shopping\App\Traits\HasProviderRatingManagement;
use Fereydooni\Shopping\App\Traits\HasProviderStatusManagement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ProviderService
{
    use HasProviderAnalytics,
        HasProviderContractManagement,
        HasProviderFinancialManagement,
        HasProviderOperations,
        HasProviderRatingManagement,
        HasProviderStatusManagement;

    public function __construct(ProviderRepositoryInterface $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    /**
     * Provider onboarding and registration
     */
    public function onboardProvider(array $data): ProviderDTO
    {
        // Validate provider data
        if (! $this->validateProviderData($data)) {
            throw new \InvalidArgumentException('Invalid provider data provided');
        }

        // Set default values for new providers
        $data['status'] = ProviderStatus::PENDING;
        $data['provider_number'] = $this->generateProviderNumber();

        // Set default commission and discount rates based on provider type
        if (! isset($data['commission_rate'])) {
            $providerType = ProviderType::from($data['provider_type']);
            $data['commission_rate'] = $providerType->getDefaultCommissionRate();
        }

        if (! isset($data['credit_limit'])) {
            $providerType = ProviderType::from($data['provider_type']);
            $data['credit_limit'] = $providerType->getDefaultCreditLimit();
        }

        // Create provider
        $provider = $this->createProvider($data);

        // Fire provider created event
        Event::dispatch('provider.created', $provider);

        Log::info('Provider onboarded successfully', [
            'provider_id' => $provider->id,
            'provider_number' => $provider->provider_number,
            'company_name' => $provider->company_name,
        ]);

        return ProviderDTO::fromModel($provider);
    }

    /**
     * Provider profile management
     */
    public function updateProviderProfile(int $providerId, array $data): ?ProviderDTO
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        // Remove fields that shouldn't be updated via profile update
        unset($data['status'], $data['provider_number'], $data['user_id']);

        $updated = $this->updateProvider($provider, $data);

        if ($updated) {
            Event::dispatch('provider.updated', $provider->fresh());

            Log::info('Provider profile updated', [
                'provider_id' => $provider->id,
                'updated_fields' => array_keys($data),
            ]);

            return ProviderDTO::fromModel($provider->fresh());
        }

        return null;
    }

    /**
     * Provider status management
     */
    public function activateProvider(int $providerId): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        if ($provider->status === ProviderStatus::ACTIVE) {
            return true; // Already active
        }

        $activated = $this->activate($provider);

        if ($activated) {
            Event::dispatch('provider.activated', $provider);

            Log::info('Provider activated', [
                'provider_id' => $provider->id,
                'company_name' => $provider->company_name,
            ]);
        }

        return $activated;
    }

    public function deactivateProvider(int $providerId, ?string $reason = null): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        if ($provider->status === ProviderStatus::INACTIVE) {
            return true; // Already inactive
        }

        $deactivated = $this->deactivate($provider);

        if ($deactivated) {
            Event::dispatch('provider.deactivated', $provider);

            Log::info('Provider deactivated', [
                'provider_id' => $provider->id,
                'company_name' => $provider->company_name,
                'reason' => $reason,
            ]);
        }

        return $deactivated;
    }

    public function suspendProvider(int $providerId, ?string $reason = null): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        if ($provider->status === ProviderStatus::SUSPENDED) {
            return true; // Already suspended
        }

        $suspended = $this->suspend($provider, $reason);

        if ($suspended) {
            Event::dispatch('provider.suspended', $provider);

            Log::info('Provider suspended', [
                'provider_id' => $provider->id,
                'company_name' => $provider->company_name,
                'reason' => $reason,
            ]);
        }

        return $suspended;
    }

    public function unsuspendProvider(int $providerId): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        if ($provider->status !== ProviderStatus::SUSPENDED) {
            return true; // Not suspended
        }

        $unsuspended = $this->unsuspend($provider);

        if ($unsuspended) {
            Event::dispatch('provider.unsuspended', $provider);

            Log::info('Provider unsuspended', [
                'provider_id' => $provider->id,
                'company_name' => $provider->company_name,
            ]);
        }

        return $unsuspended;
    }

    /**
     * Provider rating and quality management
     */
    public function updateProviderRating(int $providerId, float $rating): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $updated = $this->updateRating($provider, $rating);

        if ($updated) {
            Event::dispatch('provider.rating_updated', $provider);

            Log::info('Provider rating updated', [
                'provider_id' => $provider->id,
                'new_rating' => $rating,
            ]);
        }

        return $updated;
    }

    public function updateProviderQualityRating(int $providerId, float $rating): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $updated = $this->updateQualityRating($provider, $rating);

        if ($updated) {
            Event::dispatch('provider.quality_rating_updated', $provider);

            Log::info('Provider quality rating updated', [
                'provider_id' => $provider->id,
                'new_quality_rating' => $rating,
            ]);
        }

        return $updated;
    }

    public function updateProviderDeliveryRating(int $providerId, float $rating): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $updated = $this->updateDeliveryRating($provider, $rating);

        if ($updated) {
            Event::dispatch('provider.delivery_rating_updated', $provider);

            Log::info('Provider delivery rating updated', [
                'provider_id' => $provider->id,
                'new_delivery_rating' => $rating,
            ]);
        }

        return $updated;
    }

    public function updateProviderCommunicationRating(int $providerId, float $rating): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $updated = $this->updateCommunicationRating($provider, $rating);

        if ($updated) {
            Event::dispatch('provider.communication_rating_updated', $provider);

            Log::info('Provider communication rating updated', [
                'provider_id' => $provider->id,
                'new_communication_rating' => $rating,
            ]);
        }

        return $updated;
    }

    /**
     * Provider financial management
     */
    public function updateProviderCreditLimit(int $providerId, float $newLimit): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $updated = $this->updateCreditLimit($provider, $newLimit);

        if ($updated) {
            Event::dispatch('provider.credit_limit_updated', $provider);

            Log::info('Provider credit limit updated', [
                'provider_id' => $provider->id,
                'old_limit' => $provider->credit_limit,
                'new_limit' => $newLimit,
            ]);
        }

        return $updated;
    }

    public function updateProviderCommissionRate(int $providerId, float $newRate): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $updated = $this->updateCommissionRate($provider, $newRate);

        if ($updated) {
            Event::dispatch('provider.commission_rate_updated', $provider);

            Log::info('Provider commission rate updated', [
                'provider_id' => $provider->id,
                'old_rate' => $provider->commission_rate,
                'new_rate' => $newRate,
            ]);
        }

        return $updated;
    }

    public function updateProviderDiscountRate(int $providerId, float $newRate): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $updated = $this->updateDiscountRate($provider, $newRate);

        if ($updated) {
            Event::dispatch('provider.discount_rate_updated', $provider);

            Log::info('Provider discount rate updated', [
                'provider_id' => $provider->id,
                'old_rate' => $provider->discount_rate,
                'new_rate' => $newRate,
            ]);
        }

        return $updated;
    }

    /**
     * Provider contract management
     */
    public function extendProviderContract(int $providerId, string $newEndDate): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $extended = $this->extendContract($provider, $newEndDate);

        if ($extended) {
            Event::dispatch('provider.contract_extended', $provider);

            Log::info('Provider contract extended', [
                'provider_id' => $provider->id,
                'new_end_date' => $newEndDate,
            ]);
        }

        return $extended;
    }

    public function terminateProviderContract(int $providerId, ?string $reason = null): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $terminated = $this->terminateContract($provider, $reason);

        if ($terminated) {
            Event::dispatch('provider.contract_terminated', $provider);

            Log::info('Provider contract terminated', [
                'provider_id' => $provider->id,
                'reason' => $reason,
            ]);
        }

        return $terminated;
    }

    /**
     * Provider analytics and reporting
     */
    public function getProviderAnalytics(int $providerId): array
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        return [
            'basic_info' => [
                'id' => $provider->id,
                'company_name' => $provider->company_name,
                'provider_number' => $provider->provider_number,
                'status' => $provider->status,
                'provider_type' => $provider->provider_type,
                'created_at' => $provider->created_at,
            ],
            'performance_metrics' => $this->getProviderPerformanceMetrics($providerId),
            'financial_summary' => [
                'total_spent' => $provider->total_spent,
                'credit_limit' => $provider->credit_limit,
                'current_balance' => $provider->current_balance,
                'credit_utilization' => $provider->credit_utilization,
                'commission_rate' => $provider->commission_rate,
                'discount_rate' => $provider->discount_rate,
            ],
            'contract_info' => [
                'contract_start_date' => $provider->contract_start_date,
                'contract_end_date' => $provider->contract_end_date,
                'contract_status' => $provider->has_expired_contract ? 'expired' : 'active',
                'contract_expires_in_days' => $provider->contract_expires_in_days,
            ],
            'ratings' => [
                'overall_rating' => $provider->rating,
                'quality_rating' => $provider->quality_rating,
                'delivery_rating' => $provider->delivery_rating,
                'communication_rating' => $provider->communication_rating,
                'overall_score' => $provider->overall_score,
            ],
            'statistics' => [
                'total_orders' => $provider->total_orders,
                'average_order_value' => $provider->average_order_value,
                'on_time_delivery_rate' => $provider->on_time_delivery_rate,
                'return_rate' => $provider->return_rate,
                'defect_rate' => $provider->defect_rate,
            ],
        ];
    }

    /**
     * Provider performance evaluation
     */
    public function evaluateProviderPerformance(int $providerId): array
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $score = $this->calculateProviderScore($providerId);
        $metrics = $this->getProviderPerformanceMetrics($providerId);

        // Determine performance level
        $performanceLevel = match (true) {
            $score >= 90 => 'excellent',
            $score >= 80 => 'good',
            $score >= 70 => 'satisfactory',
            $score >= 60 => 'needs_improvement',
            default => 'poor'
        };

        // Generate recommendations
        $recommendations = $this->generatePerformanceRecommendations($provider, $score);

        return [
            'provider_id' => $providerId,
            'overall_score' => $score,
            'performance_level' => $performanceLevel,
            'metrics' => $metrics,
            'recommendations' => $recommendations,
            'evaluation_date' => now()->toISOString(),
        ];
    }

    /**
     * Provider communication management
     */
    public function addProviderNote(int $providerId, string $note, string $type = 'general'): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $added = $this->addProviderNote($provider, $note, $type);

        if ($added) {
            Log::info('Provider note added', [
                'provider_id' => $providerId,
                'note_type' => $type,
                'note' => $note,
            ]);
        }

        return $added;
    }

    public function getProviderNotes(int $providerId): Collection
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        return $this->getProviderNotes($provider);
    }

    /**
     * Provider data import/export
     */
    public function exportProviderData(int $providerId): array
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        return [
            'provider' => ProviderDTO::fromModel($provider),
            'orders' => $this->getProviderOrderHistory($providerId),
            'products' => $this->getProviderProducts($providerId),
            'invoices' => $this->getProviderInvoices($providerId),
            'payments' => $this->getProviderPayments($providerId),
            'notes' => $this->getProviderNotes($providerId),
            'export_date' => now()->toISOString(),
        ];
    }

    /**
     * Provider qualification and certification management
     */
    public function updateProviderSpecializations(int $providerId, array $specializations): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $updated = $this->updateProviderSpecializations($provider, $specializations);

        if ($updated) {
            Event::dispatch('provider.specializations_updated', $provider);

            Log::info('Provider specializations updated', [
                'provider_id' => $providerId,
                'specializations' => $specializations,
            ]);
        }

        return $updated;
    }

    public function updateProviderCertifications(int $providerId, array $certifications): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $updated = $this->updateProviderCertifications($provider, $certifications);

        if ($updated) {
            Event::dispatch('provider.certifications_updated', $provider);

            Log::info('Provider certifications updated', [
                'provider_id' => $providerId,
                'certifications' => $certifications,
            ]);
        }

        return $updated;
    }

    /**
     * Provider insurance management
     */
    public function updateProviderInsurance(int $providerId, array $insurance): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $updated = $this->updateProviderInsurance($provider, $insurance);

        if ($updated) {
            Event::dispatch('provider.insurance_updated', $provider);

            Log::info('Provider insurance updated', [
                'provider_id' => $providerId,
                'insurance' => $insurance,
            ]);
        }

        return $updated;
    }

    /**
     * Provider location management
     */
    public function updateProviderLocation(int $providerId, array $locationData): bool
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        $locationFields = ['address', 'city', 'state', 'postal_code', 'country'];
        $updateData = array_intersect_key($locationData, array_flip($locationFields));

        if (empty($updateData)) {
            return false;
        }

        $updated = $this->updateProvider($provider, $updateData);

        if ($updated) {
            Event::dispatch('provider.location_updated', $provider);

            Log::info('Provider location updated', [
                'provider_id' => $providerId,
                'location_data' => $updateData,
            ]);
        }

        return $updated;
    }

    /**
     * Provider order management
     */
    public function getProviderOrders(int $providerId, int $perPage = 15): LengthAwarePaginator
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        return $provider->orders()->paginate($perPage);
    }

    /**
     * Provider payment management
     */
    public function getProviderPayments(int $providerId, int $perPage = 15): LengthAwarePaginator
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        return $provider->payments()->paginate($perPage);
    }

    /**
     * Provider invoice management
     */
    public function getProviderInvoices(int $providerId, int $perPage = 15): LengthAwarePaginator
    {
        $provider = $this->getProvider($providerId);

        if (! $provider) {
            throw new \InvalidArgumentException('Provider not found');
        }

        return $provider->invoices()->paginate($perPage);
    }

    /**
     * Provider performance metrics calculation
     */
    public function calculateProviderScore(int $providerId): float
    {
        return $this->providerRepository->calculateProviderScore($providerId);
    }

    public function getProviderPerformanceMetrics(int $providerId): array
    {
        return $this->providerRepository->getProviderPerformanceMetrics($providerId);
    }

    /**
     * Generate performance recommendations
     */
    private function generatePerformanceRecommendations(Provider $provider, float $score): array
    {
        $recommendations = [];

        if ($score < 70) {
            if ($provider->quality_rating && $provider->quality_rating < 3.0) {
                $recommendations[] = 'Focus on improving product quality and reducing defects';
            }

            if ($provider->delivery_rating && $provider->delivery_rating < 3.0) {
                $recommendations[] = 'Improve delivery reliability and on-time delivery rates';
            }

            if ($provider->communication_rating && $provider->communication_rating < 3.0) {
                $recommendations[] = 'Enhance communication and response times';
            }

            if ($provider->return_rate && $provider->return_rate > 10) {
                $recommendations[] = 'Address high return rates by improving product quality and descriptions';
            }
        }

        if ($provider->has_expired_contract) {
            $recommendations[] = 'Contract has expired - consider renewal or renegotiation';
        }

        if ($provider->is_over_credit_limit) {
            $recommendations[] = 'Credit limit exceeded - review payment terms and credit management';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Continue maintaining current performance standards';
        }

        return $recommendations;
    }
}
