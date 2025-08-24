<?php

namespace App\Services;

use App\DTOs\ProviderInsuranceDTO;
use App\Repositories\Interfaces\ProviderInsuranceRepositoryInterface;
use App\Events\ProviderInsurance\ProviderInsuranceCreated;
use App\Events\ProviderInsurance\ProviderInsuranceUpdated;
use App\Events\ProviderInsurance\ProviderInsuranceDeleted;
use App\Events\ProviderInsurance\ProviderInsuranceVerified;
use App\Events\ProviderInsurance\ProviderInsuranceExpired;
use App\Events\ProviderInsurance\ProviderInsuranceRenewed;
use App\Events\ProviderInsurance\ProviderInsuranceDocumentUploaded;
use App\Models\ProviderInsurance;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class ProviderInsuranceService
{
    protected ProviderInsuranceRepositoryInterface $repository;

    public function __construct(ProviderInsuranceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    // Repository method wrappers
    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->repository->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->repository->cursorPaginate($perPage, $cursor);
    }

    public function find(int $id): ?ProviderInsurance
    {
        return $this->repository->find($id);
    }

    public function findDTO(int $id): ?ProviderInsuranceDTO
    {
        return $this->repository->findDTO($id);
    }

    public function findByProviderId(int $providerId): Collection
    {
        return $this->repository->findByProviderId($providerId);
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        return $this->repository->findByProviderIdDTO($providerId);
    }

    public function findByInsuranceType(string $insuranceType): Collection
    {
        return $this->repository->findByInsuranceType($insuranceType);
    }

    public function findByInsuranceTypeDTO(string $insuranceType): Collection
    {
        return $this->repository->findByInsuranceTypeDTO($insuranceType);
    }

    public function findByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function findByStatusDTO(string $status): Collection
    {
        return $this->repository->findByStatusDTO($status);
    }

    public function findByVerificationStatus(string $verificationStatus): Collection
    {
        return $this->repository->findByVerificationStatus($verificationStatus);
    }

    public function findByVerificationStatusDTO(string $verificationStatus): Collection
    {
        return $this->repository->findByVerificationStatusDTO($verificationStatus);
    }

    public function findByPolicyNumber(string $policyNumber): ?ProviderInsurance
    {
        return $this->repository->findByPolicyNumber($policyNumber);
    }

    public function findByPolicyNumberDTO(string $policyNumber): ?ProviderInsuranceDTO
    {
        return $this->repository->findByPolicyNumberDTO($policyNumber);
    }

    public function findByProviderName(string $providerName): Collection
    {
        return $this->repository->findByProviderName($providerName);
    }

    public function findByProviderNameDTO(string $providerName): Collection
    {
        return $this->repository->findByProviderNameDTO($providerName);
    }

    public function findActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function findActiveDTO(): Collection
    {
        return $this->repository->findActiveDTO();
    }

    public function findExpired(): Collection
    {
        return $this->repository->findExpired();
    }

    public function findExpiredDTO(): Collection
    {
        return $this->repository->findExpiredDTO();
    }

    public function findExpiringSoon(int $days = 30): Collection
    {
        return $this->repository->findExpiringSoon($days);
    }

    public function findExpiringSoonDTO(int $days = 30): Collection
    {
        return $this->repository->findExpiringSoonDTO($days);
    }

    public function findVerified(): Collection
    {
        return $this->repository->findVerified();
    }

    public function findVerifiedDTO(): Collection
    {
        return $this->repository->findVerifiedDTO();
    }

    public function findPendingVerification(): Collection
    {
        return $this->repository->findPendingVerification();
    }

    public function findPendingVerificationDTO(): Collection
    {
        return $this->repository->findPendingVerificationDTO();
    }

    public function findByProviderAndType(int $providerId, string $insuranceType): ?ProviderInsurance
    {
        return $this->repository->findByProviderAndType($providerId, $insuranceType);
    }

    public function findByProviderAndTypeDTO(int $providerId, string $insuranceType): ?ProviderInsuranceDTO
    {
        return $this->repository->findByProviderAndTypeDTO($providerId, $insuranceType);
    }

    public function findByProviderAndStatus(int $providerId, string $status): Collection
    {
        return $this->repository->findByProviderAndStatus($providerId, $status);
    }

    public function findByProviderAndStatusDTO(int $providerId, string $status): Collection
    {
        return $this->repository->findByProviderAndStatusDTO($providerId, $status);
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRangeDTO($startDate, $endDate);
    }

    public function findByCoverageAmountRange(float $minAmount, float $maxAmount): Collection
    {
        return $this->repository->findByCoverageAmountRange($minAmount, $maxAmount);
    }

    public function findByCoverageAmountRangeDTO(float $minAmount, float $maxAmount): Collection
    {
        return $this->repository->findByCoverageAmountRangeDTO($minAmount, $maxAmount);
    }

    // Business logic methods
    public function create(array $data): ProviderInsurance
    {
        try {
            // Validate business rules
            $this->validateInsuranceData($data);

            // Generate policy number if not provided
            if (empty($data['policy_number'])) {
                $data['policy_number'] = $this->generatePolicyNumber($data['provider_id'], $data['insurance_type']);
            }

            // Set default status
            if (empty($data['status'])) {
                $data['status'] = 'pending';
            }

            // Set default verification status
            if (empty($data['verification_status'])) {
                $data['verification_status'] = 'pending';
            }

            $providerInsurance = $this->repository->create($data);

            // Dispatch event
            event(new ProviderInsuranceCreated($providerInsurance));

            return $providerInsurance;
        } catch (Exception $e) {
            Log::error('Failed to create provider insurance', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): ProviderInsuranceDTO
    {
        $providerInsurance = $this->create($data);
        return $this->repository->findDTO($providerInsurance->id);
    }

    public function update(ProviderInsurance $providerInsurance, array $data): bool
    {
        try {
            // Validate business rules
            $this->validateInsuranceUpdate($providerInsurance, $data);

            $result = $this->repository->update($providerInsurance, $data);

            if ($result) {
                // Dispatch event
                event(new ProviderInsuranceUpdated($providerInsurance, $data));
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to update provider insurance', [
                'id' => $providerInsurance->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(ProviderInsurance $providerInsurance, array $data): ?ProviderInsuranceDTO
    {
        $result = $this->update($providerInsurance, $data);
        return $result ? $this->repository->findDTO($providerInsurance->id) : null;
    }

    public function delete(ProviderInsurance $providerInsurance): bool
    {
        try {
            $result = $this->repository->delete($providerInsurance);

            if ($result) {
                // Dispatch event
                event(new ProviderInsuranceDeleted($providerInsurance));
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to delete provider insurance', [
                'id' => $providerInsurance->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function activate(ProviderInsurance $providerInsurance): bool
    {
        return $this->update($providerInsurance, ['status' => 'active']);
    }

    public function deactivate(ProviderInsurance $providerInsurance): bool
    {
        return $this->update($providerInsurance, ['status' => 'inactive']);
    }

    public function expire(ProviderInsurance $providerInsurance): bool
    {
        $result = $this->update($providerInsurance, ['status' => 'expired']);

        if ($result) {
            event(new ProviderInsuranceExpired($providerInsurance));
        }

        return $result;
    }

    public function cancel(ProviderInsurance $providerInsurance, string $reason = null): bool
    {
        $data = ['status' => 'cancelled'];
        if ($reason) {
            $data['notes'] = ($providerInsurance->notes ? $providerInsurance->notes . "\n" : '') . "Cancelled: " . $reason;
        }

        return $this->update($providerInsurance, $data);
    }

    public function suspend(ProviderInsurance $providerInsurance, string $reason = null): bool
    {
        $data = ['status' => 'suspended'];
        if ($reason) {
            $data['notes'] = ($providerInsurance->notes ? $providerInsurance->notes . "\n" : '') . "Suspended: " . $reason;
        }

        return $this->update($providerInsurance, $data);
    }

    public function verify(ProviderInsurance $providerInsurance, int $verifiedBy, string $notes = null): bool
    {
        $data = [
            'verification_status' => 'verified',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
        ];

        if ($notes) {
            $data['notes'] = ($providerInsurance->notes ? $providerInsurance->notes . "\n" : '') . "Verified: " . $notes;
        }

        $result = $this->update($providerInsurance, $data);

        if ($result) {
            event(new ProviderInsuranceVerified($providerInsurance, $verifiedBy, $notes));
        }

        return $result;
    }

    public function reject(ProviderInsurance $providerInsurance, int $rejectedBy, string $reason): bool
    {
        $data = [
            'verification_status' => 'rejected',
            'verified_by' => $rejectedBy,
            'verified_at' => now(),
            'notes' => ($providerInsurance->notes ? $providerInsurance->notes . "\n" : '') . "Rejected: " . $reason
        ];

        return $this->update($providerInsurance, $data);
    }

    public function renew(ProviderInsurance $providerInsurance, array $renewalData): bool
    {
        try {
            // Validate renewal data
            $this->validateRenewalData($renewalData);

            // Create new insurance record
            $newInsuranceData = [
                'provider_id' => $providerInsurance->provider_id,
                'insurance_type' => $providerInsurance->insurance_type,
                'provider_name' => $providerInsurance->provider_name,
                'coverage_amount' => $renewalData['coverage_amount'] ?? $providerInsurance->coverage_amount,
                'start_date' => $renewalData['start_date'],
                'end_date' => $renewalData['end_date'],
                'status' => 'pending',
                'verification_status' => 'pending',
                'notes' => 'Renewal of policy ' . $providerInsurance->policy_number,
                'documents' => $renewalData['documents'] ?? []
            ];

            $newInsurance = $this->create($newInsuranceData);

            // Update old insurance status
            $this->update($providerInsurance, ['status' => 'renewed']);

            // Dispatch renewal event
            event(new ProviderInsuranceRenewed($providerInsurance, $newInsurance, $renewalData));

            return true;
        } catch (Exception $e) {
            Log::error('Failed to renew provider insurance', [
                'id' => $providerInsurance->id,
                'renewal_data' => $renewalData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function addDocument(ProviderInsurance $providerInsurance, string $documentPath): bool
    {
        try {
            $documents = $providerInsurance->documents ?? [];
            $documents[] = $documentPath;

            $result = $this->update($providerInsurance, ['documents' => $documents]);

            if ($result) {
                event(new ProviderInsuranceDocumentUploaded($providerInsurance, $documentPath));
            }

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to add document to provider insurance', [
                'id' => $providerInsurance->id,
                'document_path' => $documentPath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function removeDocument(ProviderInsurance $providerInsurance, string $documentPath): bool
    {
        try {
            $documents = $providerInsurance->documents ?? [];
            $documents = array_filter($documents, fn($doc) => $doc !== $documentPath);

            return $this->update($providerInsurance, ['documents' => array_values($documents)]);
        } catch (Exception $e) {
            Log::error('Failed to remove document from provider insurance', [
                'id' => $providerInsurance->id,
                'document_path' => $documentPath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // Analytics and counting methods
    public function getInsuranceCount(int $providerId): int
    {
        return $this->repository->getInsuranceCount($providerId);
    }

    public function getInsuranceCountByType(int $providerId, string $insuranceType): int
    {
        return $this->repository->getInsuranceCountByType($providerId, $insuranceType);
    }

    public function getInsuranceCountByStatus(int $providerId, string $status): int
    {
        return $this->repository->getInsuranceCountByStatus($providerId, $status);
    }

    public function getInsuranceCountByVerificationStatus(int $providerId, string $verificationStatus): int
    {
        return $this->repository->getInsuranceCountByVerificationStatus($providerId, $verificationStatus);
    }

    public function getActiveInsuranceCount(int $providerId): int
    {
        return $this->repository->getActiveInsuranceCount($providerId);
    }

    public function getExpiredInsuranceCount(int $providerId): int
    {
        return $this->repository->getExpiredInsuranceCount($providerId);
    }

    public function getExpiringSoonCount(int $providerId, int $days = 30): int
    {
        return $this->repository->getExpiringSoonCount($providerId, $days);
    }

    public function getVerifiedInsuranceCount(int $providerId): int
    {
        return $this->repository->getVerifiedInsuranceCount($providerId);
    }

    public function getPendingVerificationCount(int $providerId): int
    {
        return $this->repository->getPendingVerificationCount($providerId);
    }

    public function getTotalInsuranceCount(): int
    {
        return $this->repository->getTotalInsuranceCount();
    }

    public function getTotalInsuranceCountByType(string $insuranceType): int
    {
        return $this->repository->getTotalInsuranceCountByType($insuranceType);
    }

    public function getTotalInsuranceCountByStatus(string $status): int
    {
        return $this->repository->getTotalInsuranceCountByStatus($status);
    }

    public function getTotalInsuranceCountByVerificationStatus(string $verificationStatus): int
    {
        return $this->repository->getTotalInsuranceCountByVerificationStatus($verificationStatus);
    }

    public function getTotalActiveInsuranceCount(): int
    {
        return $this->repository->getTotalActiveInsuranceCount();
    }

    public function getTotalExpiredInsuranceCount(): int
    {
        return $this->repository->getTotalExpiredInsuranceCount();
    }

    public function getTotalExpiringSoonCount(int $days = 30): int
    {
        return $this->repository->getTotalExpiringSoonCount($days);
    }

    public function getTotalVerifiedInsuranceCount(): int
    {
        return $this->repository->getTotalVerifiedInsuranceCount();
    }

    public function getTotalPendingVerificationCount(): int
    {
        return $this->repository->getTotalPendingVerificationCount();
    }

    public function getTotalCoverageAmount(): float
    {
        return $this->repository->getTotalCoverageAmount();
    }

    public function getAverageCoverageAmount(): float
    {
        return $this->repository->getAverageCoverageAmount();
    }

    public function getTotalCoverageAmountByProvider(int $providerId): float
    {
        return $this->repository->getTotalCoverageAmountByProvider($providerId);
    }

    public function getAverageCoverageAmountByProvider(int $providerId): float
    {
        return $this->repository->getAverageCoverageAmountByProvider($providerId);
    }

    public function getTotalCoverageAmountByType(string $insuranceType): float
    {
        return $this->repository->getTotalCoverageAmountByType($insuranceType);
    }

    public function getAverageCoverageAmountByType(string $insuranceType): float
    {
        return $this->repository->getAverageCoverageAmountByType($insuranceType);
    }

    public function getExpiringInsurance(int $limit = 10): Collection
    {
        return $this->repository->getExpiringInsurance($limit);
    }

    public function getExpiringInsuranceDTO(int $limit = 10): Collection
    {
        return $this->repository->getExpiringInsuranceDTO($limit);
    }

    public function getExpiringInsuranceByProvider(int $providerId, int $limit = 10): Collection
    {
        return $this->repository->getExpiringInsuranceByProvider($providerId, $limit);
    }

    public function getExpiringInsuranceByProviderDTO(int $providerId, int $limit = 10): Collection
    {
        return $this->repository->getExpiringInsuranceByProviderDTO($providerId, $limit);
    }

    public function getPendingVerification(int $limit = 10): Collection
    {
        return $this->repository->getPendingVerification($limit);
    }

    public function getPendingVerificationDTO(int $limit = 10): Collection
    {
        return $this->repository->getPendingVerificationDTO($limit);
    }

    public function getPendingVerificationByProvider(int $providerId, int $limit = 10): Collection
    {
        return $this->repository->getPendingVerificationByProvider($providerId, $limit);
    }

    public function getPendingVerificationByProviderDTO(int $providerId, int $limit = 10): Collection
    {
        return $this->repository->getPendingVerificationByProviderDTO($providerId, $limit);
    }

    public function searchInsurance(string $query): Collection
    {
        return $this->repository->searchInsurance($query);
    }

    public function searchInsuranceDTO(string $query): Collection
    {
        return $this->repository->searchInsuranceDTO($query);
    }

    public function searchInsuranceByProvider(int $providerId, string $query): Collection
    {
        return $this->repository->searchInsuranceByProvider($providerId, $query);
    }

    public function searchInsuranceByProviderDTO(int $providerId, string $query): Collection
    {
        return $this->repository->searchInsuranceByProviderDTO($providerId, $query);
    }

    public function getInsuranceAnalytics(int $providerId): array
    {
        return $this->repository->getInsuranceAnalytics($providerId);
    }

    public function getInsuranceAnalyticsByType(int $providerId, string $insuranceType): array
    {
        return $this->repository->getInsuranceAnalyticsByType($providerId, $insuranceType);
    }

    public function getInsuranceAnalyticsByStatus(int $providerId, string $status): array
    {
        return $this->repository->getInsuranceAnalyticsByStatus($providerId, $status);
    }

    public function getInsuranceAnalyticsByVerificationStatus(int $providerId, string $verificationStatus): array
    {
        return $this->repository->getInsuranceAnalyticsByVerificationStatus($providerId, $verificationStatus);
    }

    public function getGlobalInsuranceAnalytics(): array
    {
        return $this->repository->getGlobalInsuranceAnalytics();
    }

    public function getGlobalInsuranceAnalyticsByType(string $insuranceType): array
    {
        return $this->repository->getGlobalInsuranceAnalyticsByType($insuranceType);
    }

    public function getGlobalInsuranceAnalyticsByStatus(string $status): array
    {
        return $this->repository->getGlobalInsuranceAnalyticsByStatus($status);
    }

    public function getGlobalInsuranceAnalyticsByVerificationStatus(string $verificationStatus): array
    {
        return $this->repository->getGlobalInsuranceAnalyticsByVerificationStatus($verificationStatus);
    }

    // Private helper methods
    private function validateInsuranceData(array $data): void
    {
        // Validate required fields
        $requiredFields = ['provider_id', 'insurance_type', 'provider_name', 'coverage_amount', 'start_date', 'end_date'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '{$field}' is required");
            }
        }

        // Validate dates
        if (strtotime($data['start_date']) >= strtotime($data['end_date'])) {
            throw new Exception('End date must be after start date');
        }

        // Validate coverage amount
        if ($data['coverage_amount'] <= 0) {
            throw new Exception('Coverage amount must be greater than zero');
        }

        // Validate insurance type
        $validTypes = ['general_liability', 'professional_liability', 'product_liability', 'workers_compensation', 'auto_insurance', 'property_insurance', 'cyber_insurance', 'other'];
        if (!in_array($data['insurance_type'], $validTypes)) {
            throw new Exception('Invalid insurance type');
        }
    }

    private function validateInsuranceUpdate(ProviderInsurance $providerInsurance, array $data): void
    {
        // Validate dates if provided
        if (isset($data['start_date']) && isset($data['end_date'])) {
            if (strtotime($data['start_date']) >= strtotime($data['end_date'])) {
                throw new Exception('End date must be after start date');
            }
        }

        // Validate coverage amount if provided
        if (isset($data['coverage_amount']) && $data['coverage_amount'] <= 0) {
            throw new Exception('Coverage amount must be greater than zero');
        }

        // Validate insurance type if provided
        if (isset($data['insurance_type'])) {
            $validTypes = ['general_liability', 'professional_liability', 'product_liability', 'workers_compensation', 'auto_insurance', 'property_insurance', 'cyber_insurance', 'other'];
            if (!in_array($data['insurance_type'], $validTypes)) {
                throw new Exception('Invalid insurance type');
            }
        }
    }

    private function validateRenewalData(array $data): void
    {
        // Validate required fields
        $requiredFields = ['start_date', 'end_date'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '{$field}' is required for renewal");
            }
        }

        // Validate dates
        if (strtotime($data['start_date']) >= strtotime($data['end_date'])) {
            throw new Exception('End date must be after start date');
        }

        // Validate coverage amount if provided
        if (isset($data['coverage_amount']) && $data['coverage_amount'] <= 0) {
            throw new Exception('Coverage amount must be greater than zero');
        }
    }

    private function generatePolicyNumber(int $providerId, string $insuranceType): string
    {
        $prefix = strtoupper(substr($insuranceType, 0, 3));
        $timestamp = time();
        $random = strtoupper(substr(md5($providerId . $timestamp), 0, 6));

        return "{$prefix}-{$providerId}-{$timestamp}-{$random}";
    }
}
