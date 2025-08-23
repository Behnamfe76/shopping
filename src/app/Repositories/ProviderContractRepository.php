<?php

namespace Fereydooni\Shopping\App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderContractRepositoryInterface;
use Fereydooni\Shopping\App\Models\ProviderContract;
use Fereydooni\Shopping\App\DTOs\ProviderContractDTO;
use Fereydooni\Shopping\App\Enums\ContractStatus;
use Fereydooni\Shopping\App\Enums\ContractType;
use Carbon\Carbon;

class ProviderContractRepository implements ProviderContractRepositoryInterface
{
    public function __construct(protected ProviderContract $model)
    {
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return Cache::remember('provider_contracts_all', 300, function () {
            return $this->model->with(['provider', 'signedBy'])->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['provider', 'signedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with(['provider', 'signedBy'])
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with(['provider', 'signedBy'])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    // Find operations
    public function find(int $id): ?ProviderContract
    {
        return Cache::remember("provider_contract_{$id}", 300, function () use ($id) {
            return $this->model->with(['provider', 'signedBy'])->find($id);
        });
    }

    public function findDTO(int $id): ?ProviderContractDTO
    {
        $contract = $this->find($id);
        return $contract ? ProviderContractDTO::fromModel($contract) : null;
    }

    public function findByProviderId(int $providerId): Collection
    {
        return Cache::remember("provider_contracts_provider_{$providerId}", 300, function () use ($providerId) {
            return $this->model->where('provider_id', $providerId)
                ->with(['provider', 'signedBy'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByProviderIdDTO(int $providerId): Collection
    {
        $contracts = $this->findByProviderId($providerId);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function findByContractNumber(string $contractNumber): ?ProviderContract
    {
        return $this->model->where('contract_number', $contractNumber)
            ->with(['provider', 'signedBy'])
            ->first();
    }

    public function findByContractNumberDTO(string $contractNumber): ?ProviderContractDTO
    {
        $contract = $this->findByContractNumber($contractNumber);
        return $contract ? ProviderContractDTO::fromModel($contract) : null;
    }

    public function findByContractType(string $contractType): Collection
    {
        return $this->model->where('contract_type', $contractType)
            ->with(['provider', 'signedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByContractTypeDTO(string $contractType): Collection
    {
        $contracts = $this->findByContractType($contractType);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function findByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)
            ->with(['provider', 'signedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        $contracts = $this->findByStatus($status);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->with(['provider', 'signedBy'])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        $contracts = $this->findByDateRange($startDate, $endDate);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function findByProviderAndType(int $providerId, string $contractType): Collection
    {
        return $this->model->where('provider_id', $providerId)
            ->where('contract_type', $contractType)
            ->with(['provider', 'signedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByProviderAndTypeDTO(int $providerId, string $contractType): Collection
    {
        $contracts = $this->findByProviderAndType($providerId, $contractType);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    // Status-based queries
    public function findActive(): Collection
    {
        return $this->model->where('status', ContractStatus::ACTIVE)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with(['provider', 'signedBy'])
            ->orderBy('end_date', 'asc')
            ->get();
    }

    public function findActiveDTO(): Collection
    {
        $contracts = $this->findActive();
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function findExpired(): Collection
    {
        return $this->model->where('end_date', '<', now())
            ->with(['provider', 'signedBy'])
            ->orderBy('end_date', 'desc')
            ->get();
    }

    public function findExpiredDTO(): Collection
    {
        $contracts = $this->findExpired();
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function findTerminated(): Collection
    {
        return $this->model->where('status', ContractStatus::TERMINATED)
            ->with(['provider', 'signedBy'])
            ->orderBy('termination_date', 'desc')
            ->get();
    }

    public function findTerminatedDTO(): Collection
    {
        $contracts = $this->findTerminated();
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function findPendingRenewal(): Collection
    {
        return $this->model->where('status', ContractStatus::PENDING_RENEWAL)
            ->with(['provider', 'signedBy'])
            ->orderBy('end_date', 'asc')
            ->get();
    }

    public function findPendingRenewalDTO(): Collection
    {
        $contracts = $this->findPendingRenewal();
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function findExpiringSoon(int $days = 30): Collection
    {
        $expiryDate = now()->addDays($days);
        return $this->model->where('status', ContractStatus::ACTIVE)
            ->where('end_date', '<=', $expiryDate)
            ->where('end_date', '>=', now())
            ->with(['provider', 'signedBy'])
            ->orderBy('end_date', 'asc')
            ->get();
    }

    public function findExpiringSoonDTO(int $days = 30): Collection
    {
        $contracts = $this->findExpiringSoon($days);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    // User-based queries
    public function findBySignedBy(int $signedBy): Collection
    {
        return $this->model->where('signed_by', $signedBy)
            ->with(['provider', 'signedBy'])
            ->orderBy('signed_at', 'desc')
            ->get();
    }

    public function findBySignedByDTO(int $signedBy): Collection
    {
        $contracts = $this->findBySignedBy($signedBy);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function findByRenewalDate(string $renewalDate): Collection
    {
        return $this->model->where('renewal_date', $renewalDate)
            ->with(['provider', 'signedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByRenewalDateDTO(string $renewalDate): Collection
    {
        $contracts = $this->findByRenewalDate($renewalDate);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    // Financial queries
    public function findByCommissionRateRange(float $minRate, float $maxRate): Collection
    {
        return $this->model->whereBetween('commission_rate', [$minRate, $maxRate])
            ->with(['provider', 'signedBy'])
            ->orderBy('commission_rate', 'asc')
            ->get();
    }

    public function findByCommissionRateRangeDTO(float $minRate, float $maxRate): Collection
    {
        $contracts = $this->findByCommissionRateRange($minRate, $maxRate);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function findByContractValueRange(float $minValue, float $maxValue): Collection
    {
        return $this->model->whereBetween('contract_value', [$minValue, $maxValue])
            ->with(['provider', 'signedBy'])
            ->orderBy('contract_value', 'desc')
            ->get();
    }

    public function findByContractValueRangeDTO(float $minValue, float $maxValue): Collection
    {
        $contracts = $this->findByContractValueRange($minValue, $maxValue);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    // Create and update operations
    public function create(array $data): ProviderContract
    {
        try {
            DB::beginTransaction();

            $contract = $this->model->create($data);

            // Clear relevant caches
            $this->clearContractCaches();

            DB::commit();

            Log::info('Provider contract created', ['contract_id' => $contract->id, 'provider_id' => $contract->provider_id]);

            return $contract->load(['provider', 'signedBy']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create provider contract', ['error' => $e->getMessage(), 'data' => $data]);
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): ProviderContractDTO
    {
        $contract = $this->create($data);
        return ProviderContractDTO::fromModel($contract);
    }

    public function update(ProviderContract $contract, array $data): bool
    {
        try {
            DB::beginTransaction();

            $updated = $contract->update($data);

            if ($updated) {
                // Clear relevant caches
                $this->clearContractCaches();

                Log::info('Provider contract updated', ['contract_id' => $contract->id]);
            }

            DB::commit();

            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update provider contract', ['error' => $e->getMessage(), 'contract_id' => $contract->id]);
            throw $e;
        }
    }

    public function updateAndReturnDTO(ProviderContract $contract, array $data): ?ProviderContractDTO
    {
        $updated = $this->update($contract, $data);
        return $updated ? ProviderContractDTO::fromModel($contract->fresh()) : null;
    }

    public function delete(ProviderContract $contract): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $contract->delete();

            if ($deleted) {
                // Clear relevant caches
                $this->clearContractCaches();

                Log::info('Provider contract deleted', ['contract_id' => $contract->id]);
            }

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete provider contract', ['error' => $e->getMessage(), 'contract_id' => $contract->id]);
            throw $e;
        }
    }

    // Contract lifecycle management
    public function activate(ProviderContract $contract): bool
    {
        if ($contract->status !== ContractStatus::DRAFT) {
            return false;
        }

        return $this->update($contract, [
            'status' => ContractStatus::ACTIVE,
            'start_date' => now()
        ]);
    }

    public function expire(ProviderContract $contract): bool
    {
        if ($contract->status !== ContractStatus::ACTIVE) {
            return false;
        }

        return $this->update($contract, ['status' => ContractStatus::EXPIRED]);
    }

    public function terminate(ProviderContract $contract, string $reason = null): bool
    {
        if (!$contract->canBeTerminated()) {
            return false;
        }

        return $this->update($contract, [
            'status' => ContractStatus::TERMINATED,
            'termination_date' => now(),
            'termination_reason' => $reason
        ]);
    }

    public function suspend(ProviderContract $contract, string $reason = null): bool
    {
        if (!$contract->canBeModified()) {
            return false;
        }

        return $this->update($contract, [
            'status' => ContractStatus::SUSPENDED,
            'notes' => $contract->notes . "\nSuspended: " . $reason
        ]);
    }

    public function renew(ProviderContract $contract, string $newEndDate = null): bool
    {
        if (!$contract->canBeRenewed()) {
            return false;
        }

        $newEndDate = $newEndDate ?: now()->addYear();

        return $this->update($contract, [
            'end_date' => $newEndDate,
            'status' => ContractStatus::ACTIVE,
            'renewal_date' => now()
        ]);
    }

    public function sign(ProviderContract $contract, int $signedBy): bool
    {
        if ($contract->isSigned()) {
            return false;
        }

        return $this->update($contract, [
            'signed_by' => $signedBy,
            'signed_at' => now(),
            'status' => ContractStatus::ACTIVE
        ]);
    }

    // Contract modifications
    public function updateCommissionRate(ProviderContract $contract, float $newRate): bool
    {
        if (!$contract->canBeModified()) {
            return false;
        }

        return $this->update($contract, ['commission_rate' => $newRate]);
    }

    public function updatePaymentTerms(ProviderContract $contract, array $newTerms): bool
    {
        if (!$contract->canBeModified()) {
            return false;
        }

        return $this->update($contract, ['payment_terms' => $newTerms]);
    }

    public function extendContract(ProviderContract $contract, string $newEndDate): bool
    {
        if (!$contract->canBeModified()) {
            return false;
        }

        return $this->update($contract, ['end_date' => $newEndDate]);
    }

    // Provider-specific statistics
    public function getProviderContractCount(int $providerId): int
    {
        return Cache::remember("provider_contract_count_{$providerId}", 300, function () use ($providerId) {
            return $this->model->where('provider_id', $providerId)->count();
        });
    }

    public function getProviderContractCountByType(int $providerId, string $contractType): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('contract_type', $contractType)
            ->count();
    }

    public function getProviderContractCountByStatus(int $providerId, string $status): int
    {
        return $this->model->where('provider_id', $providerId)
            ->where('status', $status)
            ->count();
    }

    public function getProviderActiveContracts(int $providerId): Collection
    {
        return $this->model->where('provider_id', $providerId)
            ->where('status', ContractStatus::ACTIVE)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with(['provider', 'signedBy'])
            ->orderBy('end_date', 'asc')
            ->get();
    }

    public function getProviderActiveContractsDTO(int $providerId): Collection
    {
        $contracts = $this->getProviderActiveContracts($providerId);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function getProviderExpiredContracts(int $providerId): Collection
    {
        return $this->model->where('provider_id', $providerId)
            ->where('end_date', '<', now())
            ->with(['provider', 'signedBy'])
            ->orderBy('end_date', 'desc')
            ->get();
    }

    public function getProviderExpiredContractsDTO(int $providerId): Collection
    {
        $contracts = $this->getProviderExpiredContracts($providerId);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    // Global statistics
    public function getTotalContractCount(): int
    {
        return Cache::remember('total_contract_count', 300, function () {
            return $this->model->count();
        });
    }

    public function getTotalContractCountByType(string $contractType): int
    {
        return $this->model->where('contract_type', $contractType)->count();
    }

    public function getTotalContractCountByStatus(string $status): int
    {
        return $this->model->where('status', $status)->count();
    }

    public function getActiveContractCount(): int
    {
        return Cache::remember('active_contract_count', 300, function () {
            return $this->model->where('status', ContractStatus::ACTIVE)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count();
        });
    }

    public function getExpiredContractCount(): int
    {
        return Cache::remember('expired_contract_count', 300, function () {
            return $this->model->where('end_date', '<', now())->count();
        });
    }

    public function getTerminatedContractCount(): int
    {
        return Cache::remember('terminated_contract_count', 300, function () {
            return $this->model->where('status', ContractStatus::TERMINATED)->count();
        });
    }

    public function getExpiringContractCount(int $days = 30): int
    {
        $expiryDate = now()->addDays($days);
        return $this->model->where('status', ContractStatus::ACTIVE)
            ->where('end_date', '<=', $expiryDate)
            ->where('end_date', '>=', now())
            ->count();
    }

    // Financial statistics
    public function getTotalContractValue(): float
    {
        return Cache::remember('total_contract_value', 300, function () {
            return $this->model->sum('contract_value');
        });
    }

    public function getAverageContractValue(): float
    {
        return Cache::remember('average_contract_value', 300, function () {
            return $this->model->avg('contract_value') ?? 0;
        });
    }

    public function getTotalContractValueByType(string $contractType): float
    {
        return $this->model->where('contract_type', $contractType)->sum('contract_value');
    }

    // Search operations
    public function searchContracts(string $query): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%")
              ->orWhere('contract_number', 'like', "%{$query}%")
              ->orWhereHas('provider', function ($providerQuery) use ($query) {
                  $providerQuery->where('company_name', 'like', "%{$query}%");
              });
        })
        ->with(['provider', 'signedBy'])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function searchContractsDTO(string $query): Collection
    {
        $contracts = $this->searchContracts($query);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    public function searchContractsByProvider(int $providerId, string $query): Collection
    {
        return $this->model->where('provider_id', $providerId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('contract_number', 'like', "%{$query}%");
            })
            ->with(['provider', 'signedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchContractsByProviderDTO(int $providerId, string $query): Collection
    {
        $contracts = $this->searchContractsByProvider($providerId, $query);
        return $contracts->map(fn($contract) => ProviderContractDTO::fromModel($contract));
    }

    // Data operations
    public function exportContractData(array $filters = []): string
    {
        $query = $this->model->with(['provider', 'signedBy']);

        if (isset($filters['provider_id'])) {
            $query->where('provider_id', $filters['provider_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['contract_type'])) {
            $query->where('contract_type', $filters['contract_type']);
        }

        $contracts = $query->get();

        // Convert to CSV format (simplified)
        $csv = "ID,Provider,Contract Number,Type,Title,Status,Start Date,End Date,Value,Currency\n";

        foreach ($contracts as $contract) {
            $csv .= "{$contract->id},{$contract->provider->company_name},{$contract->contract_number},";
            $csv .= "{$contract->contract_type},{$contract->title},{$contract->status},";
            $csv .= "{$contract->start_date->format('Y-m-d')},{$contract->end_date->format('Y-m-d')},";
            $csv .= "{$contract->contract_value},{$contract->currency}\n";
        }

        return $csv;
    }

    public function importContractData(string $data): bool
    {
        try {
            DB::beginTransaction();

            $lines = explode("\n", trim($data));
            $headers = str_getcsv(array_shift($lines));

            foreach ($lines as $line) {
                if (empty(trim($line))) continue;

                $row = array_combine($headers, str_getcsv($line));

                // Basic validation
                if (empty($row['provider_id']) || empty($row['title'])) {
                    continue;
                }

                $this->create([
                    'provider_id' => $row['provider_id'],
                    'title' => $row['title'],
                    'contract_type' => $row['contract_type'] ?? ContractType::SERVICE,
                    'status' => $row['status'] ?? ContractStatus::DRAFT,
                    'start_date' => $row['start_date'] ?? now(),
                    'end_date' => $row['end_date'] ?? now()->addYear(),
                    'contract_value' => $row['contract_value'] ?? 0,
                    'currency' => $row['currency'] ?? 'USD',
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import contract data', ['error' => $e->getMessage()]);
            return false;
        }
    }

    // Analytics and reporting
    public function getContractStatistics(): array
    {
        return Cache::remember('contract_statistics', 300, function () {
            return [
                'total_contracts' => $this->getTotalContractCount(),
                'active_contracts' => $this->getActiveContractCount(),
                'expired_contracts' => $this->getExpiredContractCount(),
                'terminated_contracts' => $this->getTerminatedContractCount(),
                'expiring_soon' => $this->getExpiringContractCount(30),
                'total_value' => $this->getTotalContractValue(),
                'average_value' => $this->getAverageContractValue(),
                'by_type' => $this->getContractTypeDistribution(),
                'by_status' => $this->getContractStatusDistribution(),
            ];
        });
    }

    public function getProviderContractStatistics(int $providerId): array
    {
        return [
            'total_contracts' => $this->getProviderContractCount($providerId),
            'active_contracts' => $this->getProviderActiveContracts($providerId)->count(),
            'expired_contracts' => $this->getProviderExpiredContracts($providerId)->count(),
            'total_value' => $this->model->where('provider_id', $providerId)->sum('contract_value'),
            'average_value' => $this->model->where('provider_id', $providerId)->avg('contract_value') ?? 0,
        ];
    }

    public function getContractTrends(string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?: now()->subYear()->format('Y-m-d');
        $endDate = $endDate ?: now()->format('Y-m-d');

        $contracts = $this->model->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $contracts->pluck('count', 'date')->toArray();
    }

    // Utility methods
    public function generateContractNumber(): string
    {
        return ProviderContract::generateContractNumber();
    }

    public function isContractNumberUnique(string $contractNumber): bool
    {
        return !$this->model->where('contract_number', $contractNumber)->exists();
    }

    // Private helper methods
    private function clearContractCaches(): void
    {
        Cache::forget('provider_contracts_all');
        Cache::forget('total_contract_count');
        Cache::forget('active_contract_count');
        Cache::forget('expired_contract_count');
        Cache::forget('terminated_contract_count');
        Cache::forget('total_contract_value');
        Cache::forget('average_contract_value');
        Cache::forget('contract_statistics');
    }

    private function getContractTypeDistribution(): array
    {
        return $this->model->selectRaw('contract_type, COUNT(*) as count')
            ->groupBy('contract_type')
            ->pluck('count', 'contract_type')
            ->toArray();
    }

    private function getContractStatusDistribution(): array
    {
        return $this->model->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
}
