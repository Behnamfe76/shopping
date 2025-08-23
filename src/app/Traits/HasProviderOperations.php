<?php

namespace Fereydooni\Shopping\App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\DTOs\ProviderDTO;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderRepositoryInterface;

trait HasProviderOperations
{
    protected ProviderRepositoryInterface $providerRepository;

    /**
     * Get all providers
     */
    public function getAllProviders(): Collection
    {
        return $this->providerRepository->all();
    }

    /**
     * Get paginated providers
     */
    public function getPaginatedProviders(int $perPage = 15): LengthAwarePaginator
    {
        return $this->providerRepository->paginate($perPage);
    }

    /**
     * Get provider by ID
     */
    public function getProvider(int $id): ?Provider
    {
        return $this->providerRepository->find($id);
    }

    /**
     * Get provider DTO by ID
     */
    public function getProviderDTO(int $id): ?ProviderDTO
    {
        return $this->providerRepository->findDTO($id);
    }

    /**
     * Get provider by user ID
     */
    public function getProviderByUserId(int $userId): ?Provider
    {
        return $this->providerRepository->findByUserId($userId);
    }

    /**
     * Get provider DTO by user ID
     */
    public function getProviderByUserIdDTO(int $userId): ?ProviderDTO
    {
        return $this->providerRepository->findByUserIdDTO($userId);
    }

    /**
     * Get provider by email
     */
    public function getProviderByEmail(string $email): ?Provider
    {
        return $this->providerRepository->findByEmail($email);
    }

    /**
     * Get provider DTO by email
     */
    public function getProviderByEmailDTO(string $email): ?ProviderDTO
    {
        return $this->providerRepository->findByEmailDTO($email);
    }

    /**
     * Get provider by phone
     */
    public function getProviderByPhone(string $phone): ?Provider
    {
        return $this->providerRepository->findByPhone($phone);
    }

    /**
     * Get provider DTO by phone
     */
    public function getProviderByPhoneDTO(string $phone): ?ProviderDTO
    {
        return $this->providerRepository->findByPhoneDTO($phone);
    }

    /**
     * Get provider by provider number
     */
    public function getProviderByNumber(string $providerNumber): ?Provider
    {
        return $this->providerRepository->findByProviderNumber($providerNumber);
    }

    /**
     * Get provider DTO by provider number
     */
    public function getProviderByNumberDTO(string $providerNumber): ?ProviderDTO
    {
        return $this->providerRepository->findByProviderNumberDTO($providerNumber);
    }

    /**
     * Get provider by company name
     */
    public function getProviderByCompanyName(string $companyName): ?Provider
    {
        return $this->providerRepository->findByCompanyName($companyName);
    }

    /**
     * Get provider DTO by company name
     */
    public function getProviderByCompanyNameDTO(string $companyName): ?ProviderDTO
    {
        return $this->providerRepository->findByCompanyNameDTO($companyName);
    }

    /**
     * Get provider by tax ID
     */
    public function getProviderByTaxId(string $taxId): ?Provider
    {
        return $this->providerRepository->findByTaxId($taxId);
    }

    /**
     * Get provider DTO by tax ID
     */
    public function getProviderByTaxIdDTO(string $taxId): ?ProviderDTO
    {
        return $this->providerRepository->findByTaxIdDTO($taxId);
    }

    /**
     * Create a new provider
     */
    public function createProvider(array $data): Provider
    {
        return $this->providerRepository->create($data);
    }

    /**
     * Create a new provider and return DTO
     */
    public function createProviderAndReturnDTO(array $data): ProviderDTO
    {
        return $this->providerRepository->createAndReturnDTO($data);
    }

    /**
     * Update provider
     */
    public function updateProvider(Provider $provider, array $data): bool
    {
        return $this->providerRepository->update($provider, $data);
    }

    /**
     * Update provider and return DTO
     */
    public function updateProviderAndReturnDTO(Provider $provider, array $data): ?ProviderDTO
    {
        return $this->providerRepository->updateAndReturnDTO($provider, $data);
    }

    /**
     * Delete provider
     */
    public function deleteProvider(Provider $provider): bool
    {
        return $this->providerRepository->delete($provider);
    }

    /**
     * Search providers
     */
    public function searchProviders(string $query): Collection
    {
        return $this->providerRepository->search($query);
    }

    /**
     * Search providers and return DTOs
     */
    public function searchProvidersDTO(string $query): Collection
    {
        return $this->providerRepository->searchDTO($query);
    }

    /**
     * Search providers by company name
     */
    public function searchProvidersByCompany(string $companyName): Collection
    {
        return $this->providerRepository->searchByCompany($companyName);
    }

    /**
     * Search providers by company name and return DTOs
     */
    public function searchProvidersByCompanyDTO(string $companyName): Collection
    {
        return $this->providerRepository->searchByCompanyDTO($companyName);
    }

    /**
     * Search providers by specialization
     */
    public function searchProvidersBySpecialization(string $specialization): Collection
    {
        return $this->providerRepository->searchBySpecialization($specialization);
    }

    /**
     * Search providers by specialization and return DTOs
     */
    public function searchProvidersBySpecializationDTO(string $specialization): Collection
    {
        return $this->providerRepository->searchBySpecializationDTO($specialization);
    }

    /**
     * Validate provider data
     */
    public function validateProviderData(array $data): bool
    {
        return $this->providerRepository->validateProvider($data);
    }

    /**
     * Generate unique provider number
     */
    public function generateProviderNumber(): string
    {
        return $this->providerRepository->generateProviderNumber();
    }

    /**
     * Check if provider number is unique
     */
    public function isProviderNumberUnique(string $providerNumber): bool
    {
        return $this->providerRepository->isProviderNumberUnique($providerNumber);
    }

    /**
     * Get provider count
     */
    public function getProviderCount(): int
    {
        return $this->providerRepository->getProviderCount();
    }

    /**
     * Get provider count by status
     */
    public function getProviderCountByStatus(string $status): int
    {
        return $this->providerRepository->getProviderCountByStatus($status);
    }

    /**
     * Get provider count by type
     */
    public function getProviderCountByType(string $type): int
    {
        return $this->providerRepository->getProviderCountByType($type);
    }

    /**
     * Get active provider count
     */
    public function getActiveProviderCount(): int
    {
        return $this->providerRepository->getActiveProviderCount();
    }

    /**
     * Get inactive provider count
     */
    public function getInactiveProviderCount(): int
    {
        return $this->providerRepository->getInactiveProviderCount();
    }

    /**
     * Get suspended provider count
     */
    public function getSuspendedProviderCount(): int
    {
        return $this->providerRepository->getSuspendedProviderCount();
    }

    /**
     * Get total provider spending
     */
    public function getTotalProviderSpending(): float
    {
        return $this->providerRepository->getTotalProviderSpending();
    }

    /**
     * Get average provider spending
     */
    public function getAverageProviderSpending(): float
    {
        return $this->providerRepository->getAverageProviderSpending();
    }

    /**
     * Get average provider rating
     */
    public function getAverageProviderRating(): float
    {
        return $this->providerRepository->getAverageProviderRating();
    }

    /**
     * Get total credit limit
     */
    public function getTotalCreditLimit(): float
    {
        return $this->providerRepository->getTotalCreditLimit();
    }

    /**
     * Get average credit limit
     */
    public function getAverageCreditLimit(): float
    {
        return $this->providerRepository->getAverageCreditLimit();
    }

    /**
     * Get total current balance
     */
    public function getTotalCurrentBalance(): float
    {
        return $this->providerRepository->getTotalCurrentBalance();
    }

    /**
     * Get average current balance
     */
    public function getAverageCurrentBalance(): float
    {
        return $this->providerRepository->getAverageCurrentBalance();
    }

    /**
     * Get provider statistics
     */
    public function getProviderStats(): array
    {
        return $this->providerRepository->getProviderStats();
    }

    /**
     * Get provider statistics by status
     */
    public function getProviderStatsByStatus(): array
    {
        return $this->providerRepository->getProviderStatsByStatus();
    }

    /**
     * Get provider statistics by type
     */
    public function getProviderStatsByType(): array
    {
        return $this->providerRepository->getProviderStatsByType();
    }

    /**
     * Get provider growth statistics
     */
    public function getProviderGrowthStats(string $period = 'monthly'): array
    {
        return $this->providerRepository->getProviderGrowthStats($period);
    }

    /**
     * Get provider performance statistics
     */
    public function getProviderPerformanceStats(): array
    {
        return $this->providerRepository->getProviderPerformanceStats();
    }

    /**
     * Get provider quality statistics
     */
    public function getProviderQualityStats(): array
    {
        return $this->providerRepository->getProviderQualityStats();
    }

    /**
     * Get provider financial statistics
     */
    public function getProviderFinancialStats(): array
    {
        return $this->providerRepository->getProviderFinancialStats();
    }

    /**
     * Get provider contract statistics
     */
    public function getProviderContractStats(): array
    {
        return $this->providerRepository->getProviderContractStats();
    }
}
