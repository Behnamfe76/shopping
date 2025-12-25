<?php

namespace Fereydooni\Shopping\App\Repositories;

use Fereydooni\Shopping\App\DTOs\ProviderDTO;
use Fereydooni\Shopping\App\Enums\ProviderStatus;
use Fereydooni\Shopping\App\Enums\ProviderType;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProviderRepository implements ProviderRepositoryInterface
{
    public function __construct(protected Provider $model) {}

    // Basic CRUD operations
    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    // Find operations
    public function find(int $id): ?Provider
    {
        return $this->model->find($id);
    }

    public function findDTO(int $id): ?ProviderDTO
    {
        $provider = $this->find($id);

        return $provider ? ProviderDTO::fromModel($provider) : null;
    }

    public function findByUserId(int $userId): ?Provider
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function findByUserIdDTO(int $userId): ?ProviderDTO
    {
        $provider = $this->findByUserId($userId);

        return $provider ? ProviderDTO::fromModel($provider) : null;
    }

    public function findByEmail(string $email): ?Provider
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByEmailDTO(string $email): ?ProviderDTO
    {
        $provider = $this->findByEmail($email);

        return $provider ? ProviderDTO::fromModel($provider) : null;
    }

    public function findByPhone(string $phone): ?Provider
    {
        return $this->model->where('phone', $phone)->first();
    }

    public function findByPhoneDTO(string $phone): ?ProviderDTO
    {
        $provider = $this->findByPhone($phone);

        return $provider ? ProviderDTO::fromModel($provider) : null;
    }

    public function findByProviderNumber(string $providerNumber): ?Provider
    {
        return $this->model->where('provider_number', $providerNumber)->first();
    }

    public function findByProviderNumberDTO(string $providerNumber): ?ProviderDTO
    {
        $provider = $this->findByProviderNumber($providerNumber);

        return $provider ? ProviderDTO::fromModel($provider) : null;
    }

    public function findByCompanyName(string $companyName): ?Provider
    {
        return $this->model->where('company_name', 'LIKE', "%{$companyName}%")->first();
    }

    public function findByCompanyNameDTO(string $companyName): ?ProviderDTO
    {
        $provider = $this->findByCompanyName($companyName);

        return $provider ? ProviderDTO::fromModel($provider) : null;
    }

    public function findByTaxId(string $taxId): ?Provider
    {
        return $this->model->where('tax_id', $taxId)->first();
    }

    public function findByTaxIdDTO(string $taxId): ?ProviderDTO
    {
        $provider = $this->findByTaxId($taxId);

        return $provider ? ProviderDTO::fromModel($provider) : null;
    }

    // Status-based queries
    public function findByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        $providers = $this->findByStatus($status);

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    public function findByType(string $type): Collection
    {
        return $this->model->where('provider_type', $type)->get();
    }

    public function findByTypeDTO(string $type): Collection
    {
        $providers = $this->findByType($type);

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    public function findActive(): Collection
    {
        return $this->model->active()->get();
    }

    public function findActiveDTO(): Collection
    {
        $providers = $this->findActive();

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    public function findInactive(): Collection
    {
        return $this->model->inactive()->get();
    }

    public function findInactiveDTO(): Collection
    {
        $providers = $this->findInactive();

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    public function findSuspended(): Collection
    {
        return $this->model->suspended()->get();
    }

    public function findSuspendedDTO(): Collection
    {
        $providers = $this->findSuspended();

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    // Create and update operations
    public function create(array $data): Provider
    {
        if (! isset($data['provider_number'])) {
            $data['provider_number'] = $this->generateProviderNumber();
        }

        return $this->model->create($data);
    }

    public function createAndReturnDTO(array $data): ProviderDTO
    {
        $provider = $this->create($data);

        return ProviderDTO::fromModel($provider);
    }

    public function update(Provider $provider, array $data): bool
    {
        return $provider->update($data);
    }

    public function updateAndReturnDTO(Provider $provider, array $data): ?ProviderDTO
    {
        $updated = $this->update($provider, $data);

        return $updated ? ProviderDTO::fromModel($provider->fresh()) : null;
    }

    public function delete(Provider $provider): bool
    {
        return $provider->delete();
    }

    // Status management
    public function activate(Provider $provider): bool
    {
        return $provider->activate();
    }

    public function deactivate(Provider $provider): bool
    {
        return $provider->deactivate();
    }

    public function suspend(Provider $provider, ?string $reason = null): bool
    {
        return $provider->suspend($reason);
    }

    public function unsuspend(Provider $provider): bool
    {
        return $provider->unsuspend();
    }

    // Rating management
    public function updateRating(Provider $provider, float $rating): bool
    {
        return $provider->updateRating($rating);
    }

    public function updateQualityRating(Provider $provider, float $rating): bool
    {
        return $provider->updateQualityRating($rating);
    }

    public function updateDeliveryRating(Provider $provider, float $rating): bool
    {
        return $provider->updateDeliveryRating($rating);
    }

    public function updateCommunicationRating(Provider $provider, float $rating): bool
    {
        return $provider->updateCommunicationRating($rating);
    }

    // Financial management
    public function updateCreditLimit(Provider $provider, float $newLimit): bool
    {
        return $provider->updateCreditLimit($newLimit);
    }

    public function updateCommissionRate(Provider $provider, float $newRate): bool
    {
        return $provider->updateCommissionRate($newRate);
    }

    public function updateDiscountRate(Provider $provider, float $newRate): bool
    {
        return $provider->updateDiscountRate($newRate);
    }

    // Contract management
    public function extendContract(Provider $provider, string $newEndDate): bool
    {
        return $provider->extendContract($newEndDate);
    }

    public function terminateContract(Provider $provider, ?string $reason = null): bool
    {
        return $provider->terminateContract($reason);
    }

    // Statistics and counts
    public function getProviderCount(): int
    {
        return $this->model->count();
    }

    public function getProviderCountByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    public function getProviderCountByType(string $type): int
    {
        return $this->model->where('provider_type', $type)->count();
    }

    public function getActiveProviderCount(): int
    {
        return $this->model->active()->count();
    }

    public function getInactiveProviderCount(): int
    {
        return $this->model->inactive()->count();
    }

    public function getSuspendedProviderCount(): int
    {
        return $this->model->suspended()->count();
    }

    public function getTotalProviderSpending(): float
    {
        return $this->model->sum('total_spent');
    }

    public function getAverageProviderSpending(): float
    {
        return $this->model->avg('total_spent') ?? 0.0;
    }

    public function getAverageProviderRating(): float
    {
        return $this->model->whereNotNull('rating')->avg('rating') ?? 0.0;
    }

    public function getTotalCreditLimit(): float
    {
        return $this->model->sum('credit_limit');
    }

    public function getAverageCreditLimit(): float
    {
        return $this->model->avg('credit_limit') ?? 0.0;
    }

    public function getTotalCurrentBalance(): float
    {
        return $this->model->sum('current_balance');
    }

    public function getAverageCurrentBalance(): float
    {
        return $this->model->avg('current_balance') ?? 0.0;
    }

    // Search operations
    public function search(string $query): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('company_name', 'LIKE', "%{$query}%")
                ->orWhere('contact_person', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('phone', 'LIKE', "%{$query}%")
                ->orWhere('provider_number', 'LIKE', "%{$query}%");
        })->get();
    }

    public function searchDTO(string $query): Collection
    {
        $providers = $this->search($query);

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    public function searchByCompany(string $companyName): Collection
    {
        return $this->model->where('company_name', 'LIKE', "%{$companyName}%")->get();
    }

    public function searchByCompanyDTO(string $companyName): Collection
    {
        $providers = $this->searchByCompany($companyName);

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    public function searchBySpecialization(string $specialization): Collection
    {
        return $this->model->whereJsonContains('specializations', $specialization)->get();
    }

    public function searchBySpecializationDTO(string $specialization): Collection
    {
        $providers = $this->searchBySpecialization($specialization);

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    // Top performers
    public function getTopRated(int $limit = 10): Collection
    {
        return $this->model->topRated($limit)->get();
    }

    public function getTopRatedDTO(int $limit = 10): Collection
    {
        $providers = $this->getTopRated($limit);

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    public function getTopSpenders(int $limit = 10): Collection
    {
        return $this->model->topSpenders($limit)->get();
    }

    public function getTopSpendersDTO(int $limit = 10): Collection
    {
        $providers = $this->getTopSpenders($limit);

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    public function getMostReliable(int $limit = 10): Collection
    {
        return $this->model->mostReliable($limit)->get();
    }

    public function getMostReliableDTO(int $limit = 10): Collection
    {
        $providers = $this->getMostReliable($limit);

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    public function getNewestProviders(int $limit = 10): Collection
    {
        return $this->model->newest($limit)->get();
    }

    public function getNewestProvidersDTO(int $limit = 10): Collection
    {
        $providers = $this->getNewestProviders($limit);

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    public function getLongestServing(int $limit = 10): Collection
    {
        return $this->model->longestServing($limit)->get();
    }

    public function getLongestServingDTO(int $limit = 10): Collection
    {
        $providers = $this->getLongestServing($limit);

        return $providers->map(fn ($provider) => ProviderDTO::fromModel($provider));
    }

    // Validation and utilities
    public function validateProvider(array $data): bool
    {
        $validator = validator($data, ProviderDTO::rules(), ProviderDTO::messages());

        return ! $validator->fails();
    }

    public function generateProviderNumber(): string
    {
        do {
            $number = 'PROV-'.strtoupper(Str::random(8));
        } while (! $this->isProviderNumberUnique($number));

        return $number;
    }

    public function isProviderNumberUnique(string $providerNumber): bool
    {
        return ! $this->model->where('provider_number', $providerNumber)->exists();
    }

    // Analytics and reporting
    public function getProviderStats(): array
    {
        return [
            'total' => $this->getProviderCount(),
            'active' => $this->getActiveProviderCount(),
            'inactive' => $this->getInactiveProviderCount(),
            'suspended' => $this->getSuspendedProviderCount(),
            'total_spending' => $this->getTotalProviderSpending(),
            'average_rating' => $this->getAverageProviderRating(),
            'total_credit_limit' => $this->getTotalCreditLimit(),
            'total_current_balance' => $this->getTotalCurrentBalance(),
        ];
    }

    public function getProviderStatsByStatus(): array
    {
        $stats = [];
        foreach (ProviderStatus::values() as $status) {
            $stats[$status] = $this->getProviderCountByStatus($status);
        }

        return $stats;
    }

    public function getProviderStatsByType(): array
    {
        $stats = [];
        foreach (ProviderType::values() as $type) {
            $stats[$type] = $this->getProviderCountByType($type);
        }

        return $stats;
    }

    public function getProviderGrowthStats(string $period = 'monthly'): array
    {
        $query = $this->model->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date');

        if ($period === 'monthly') {
            $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as date, COUNT(*) as count')
                ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'));
        }

        return $query->get()->pluck('count', 'date')->toArray();
    }

    public function getProviderPerformanceStats(): array
    {
        return [
            'average_rating' => $this->getAverageProviderRating(),
            'average_quality_rating' => $this->model->whereNotNull('quality_rating')->avg('quality_rating') ?? 0.0,
            'average_delivery_rating' => $this->model->whereNotNull('delivery_rating')->avg('delivery_rating') ?? 0.0,
            'average_communication_rating' => $this->model->whereNotNull('communication_rating')->avg('communication_rating') ?? 0.0,
            'average_on_time_delivery' => $this->model->whereNotNull('on_time_delivery_rate')->avg('on_time_delivery_rate') ?? 0.0,
            'average_response_time' => $this->model->whereNotNull('response_time')->avg('response_time') ?? 0.0,
        ];
    }

    public function getProviderQualityStats(): array
    {
        return [
            'average_quality_rating' => $this->model->whereNotNull('quality_rating')->avg('quality_rating') ?? 0.0,
            'average_defect_rate' => $this->model->whereNotNull('defect_rate')->avg('defect_rate') ?? 0.0,
            'average_return_rate' => $this->model->whereNotNull('return_rate')->avg('return_rate') ?? 0.0,
            'providers_with_quality_issues' => $this->model->where('quality_rating', '<', 3.0)->count(),
        ];
    }

    public function getProviderFinancialStats(): array
    {
        return [
            'total_credit_limit' => $this->getTotalCreditLimit(),
            'average_credit_limit' => $this->getAverageCreditLimit(),
            'total_current_balance' => $this->getTotalCurrentBalance(),
            'average_current_balance' => $this->getAverageCurrentBalance(),
            'providers_over_limit' => $this->model->whereRaw('current_balance > credit_limit')->count(),
            'total_commission_potential' => $this->model->sum(DB::raw('total_spent * commission_rate / 100')),
        ];
    }

    public function getProviderContractStats(): array
    {
        $now = now();

        return [
            'total_contracts' => $this->model->whereNotNull('contract_start_date')->count(),
            'active_contracts' => $this->model->where('contract_end_date', '>', $now)->count(),
            'expired_contracts' => $this->model->where('contract_end_date', '<', $now)->count(),
            'contracts_expiring_soon' => $this->model->whereBetween('contract_end_date', [$now, $now->addDays(30)])->count(),
        ];
    }

    // Provider-specific data
    public function getProviderLifetimeValue(int $providerId): float
    {
        $provider = $this->find($providerId);

        return $provider ? $provider->total_spent : 0.0;
    }

    public function getProviderOrderHistory(int $providerId): Collection
    {
        $provider = $this->find($providerId);

        return $provider ? $provider->orders : collect();
    }

    public function getProviderProducts(int $providerId): Collection
    {
        $provider = $this->find($providerId);

        return $provider ? $provider->products : collect();
    }

    public function getProviderInvoices(int $providerId): Collection
    {
        $provider = $this->find($providerId);

        return $provider ? $provider->invoices : collect();
    }

    public function getProviderPayments(int $providerId): Collection
    {
        $provider = $this->find($providerId);

        return $provider ? $provider->payments : collect();
    }

    // Notes and additional data
    public function addProviderNote(Provider $provider, string $note, string $type = 'general'): bool
    {
        return (bool) $provider->addNote($note, $type);
    }

    public function getProviderNotes(Provider $provider): Collection
    {
        return $provider->notes;
    }

    // Specializations and certifications
    public function updateProviderSpecializations(Provider $provider, array $specializations): bool
    {
        return $provider->updateSpecializations($specializations);
    }

    public function getProviderSpecializations(int $providerId): array
    {
        $provider = $this->find($providerId);

        return $provider ? ($provider->specializations ?? []) : [];
    }

    public function updateProviderCertifications(Provider $provider, array $certifications): bool
    {
        return $provider->updateCertifications($certifications);
    }

    public function getProviderCertifications(int $providerId): array
    {
        $provider = $this->find($providerId);

        return $provider ? ($provider->certifications ?? []) : [];
    }

    // Insurance
    public function updateProviderInsurance(Provider $provider, array $insurance): bool
    {
        return $provider->updateInsurance($insurance);
    }

    public function getProviderInsurance(int $providerId): array
    {
        $provider = $this->find($providerId);

        return $provider ? ($provider->insurance_info ?? []) : [];
    }

    // Performance metrics
    public function calculateProviderScore(int $providerId): float
    {
        $provider = $this->find($providerId);

        return $provider ? $provider->calculateScore() : 0.0;
    }

    public function getProviderPerformanceMetrics(int $providerId): array
    {
        $provider = $this->find($providerId);

        return $provider ? $provider->getPerformanceMetrics() : [];
    }
}
