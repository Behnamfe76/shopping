<?php

namespace App\Facades;

use App\Actions\ProviderContract\CalculateContractMetricsAction;
use App\Actions\ProviderContract\CreateProviderContractAction;
use App\Actions\ProviderContract\RenewProviderContractAction;
use App\Actions\ProviderContract\SignProviderContractAction;
use App\Actions\ProviderContract\TerminateProviderContractAction;
use App\Actions\ProviderContract\UpdateProviderContractAction;
use App\DTOs\ProviderContractDTO;
use App\Models\ProviderContract;
use App\Repositories\ProviderContractRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ProviderContract
{
    protected static $repository;

    protected static $createAction;

    protected static $updateAction;

    protected static $signAction;

    protected static $renewAction;

    protected static $terminateAction;

    protected static $metricsAction;

    protected static function getRepository(): ProviderContractRepository
    {
        if (! static::$repository) {
            static::$repository = app(ProviderContractRepository::class);
        }

        return static::$repository;
    }

    protected static function getCreateAction(): CreateProviderContractAction
    {
        if (! static::$createAction) {
            static::$createAction = app(CreateProviderContractAction::class);
        }

        return static::$createAction;
    }

    protected static function getUpdateAction(): UpdateProviderContractAction
    {
        if (! static::$updateAction) {
            static::$updateAction = app(UpdateProviderContractAction::class);
        }

        return static::$updateAction;
    }

    protected static function getSignAction(): SignProviderContractAction
    {
        if (! static::$signAction) {
            static::$signAction = app(SignProviderContractAction::class);
        }

        return static::$signAction;
    }

    protected static function getRenewAction(): RenewProviderContractAction
    {
        if (! static::$renewAction) {
            static::$renewAction = app(RenewProviderContractAction::class);
        }

        return static::$renewAction;
    }

    protected static function getTerminateAction(): TerminateProviderContractAction
    {
        if (! static::$terminateAction) {
            static::$terminateAction = app(TerminateProviderContractAction::class);
        }

        return static::$terminateAction;
    }

    protected static function getMetricsAction(): CalculateContractMetricsAction
    {
        if (! static::$metricsAction) {
            static::$metricsAction = app(CalculateContractMetricsAction::class);
        }

        return static::$metricsAction;
    }

    // Basic CRUD Operations
    public static function all(): Collection
    {
        try {
            return static::getRepository()->all();
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in all(): '.$e->getMessage());

            return collect();
        }
    }

    public static function paginate(int $perPage = 15): LengthAwarePaginator
    {
        try {
            return static::getRepository()->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in paginate(): '.$e->getMessage());

            return new LengthAwarePaginator([], 0, $perPage);
        }
    }

    public static function find(int $id): ?ProviderContract
    {
        try {
            return static::getRepository()->find($id);
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in find(): '.$e->getMessage());

            return null;
        }
    }

    public static function findDTO(int $id): ?ProviderContractDTO
    {
        try {
            return static::getRepository()->findDTO($id);
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in findDTO(): '.$e->getMessage());

            return null;
        }
    }

    public static function create(array $data): ?ProviderContractDTO
    {
        try {
            $dto = static::getCreateAction()->execute($data);

            if ($dto) {
                Event::dispatch('provider-contract.created', $dto);
                static::clearCache();
            }

            return $dto;
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in create(): '.$e->getMessage());

            return null;
        }
    }

    public static function update(int $id, array $data): ?ProviderContractDTO
    {
        try {
            $contract = static::find($id);
            if (! $contract) {
                return null;
            }

            $dto = static::getUpdateAction()->execute($contract, $data);

            if ($dto) {
                Event::dispatch('provider-contract.updated', $dto);
                static::clearCache();
            }

            return $dto;
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in update(): '.$e->getMessage());

            return null;
        }
    }

    public static function delete(int $id): bool
    {
        try {
            $contract = static::find($id);
            if (! $contract) {
                return false;
            }

            $result = static::getRepository()->delete($contract);

            if ($result) {
                Event::dispatch('provider-contract.deleted', $contract);
                static::clearCache();
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in delete(): '.$e->getMessage());

            return false;
        }
    }

    // Contract Lifecycle Operations
    public static function sign(int $id, int $signedBy): ?ProviderContractDTO
    {
        try {
            $contract = static::find($id);
            if (! $contract) {
                return null;
            }

            $dto = static::getSignAction()->execute($contract, $signedBy);

            if ($dto) {
                Event::dispatch('provider-contract.signed', $dto);
                static::clearCache();
            }

            return $dto;
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in sign(): '.$e->getMessage());

            return null;
        }
    }

    public static function renew(int $id, ?string $newEndDate = null): ?ProviderContractDTO
    {
        try {
            $contract = static::find($id);
            if (! $contract) {
                return null;
            }

            $dto = static::getRenewAction()->execute($contract, $newEndDate);

            if ($dto) {
                Event::dispatch('provider-contract.renewed', $dto);
                static::clearCache();
            }

            return $dto;
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in renew(): '.$e->getMessage());

            return null;
        }
    }

    public static function terminate(int $id, ?string $reason = null): ?ProviderContractDTO
    {
        try {
            $contract = static::find($id);
            if (! $contract) {
                return null;
            }

            $dto = static::getTerminateAction()->execute($contract, $reason);

            if ($dto) {
                Event::dispatch('provider-contract.terminated', $dto);
                static::clearCache();
            }

            return $dto;
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in terminate(): '.$e->getMessage());

            return null;
        }
    }

    public static function activate(int $id): bool
    {
        try {
            $contract = static::find($id);
            if (! $contract) {
                return false;
            }

            $result = static::getRepository()->activate($contract);

            if ($result) {
                Event::dispatch('provider-contract.activated', $contract);
                static::clearCache();
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in activate(): '.$e->getMessage());

            return false;
        }
    }

    public static function expire(int $id): bool
    {
        try {
            $contract = static::find($id);
            if (! $contract) {
                return false;
            }

            $result = static::getRepository()->expire($contract);

            if ($result) {
                Event::dispatch('provider-contract.expired', $contract);
                static::clearCache();
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in expire(): '.$e->getMessage());

            return false;
        }
    }

    // Search and Filter Operations
    public static function findByProvider(int $providerId): Collection
    {
        try {
            return static::getRepository()->findByProviderId($providerId);
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in findByProvider(): '.$e->getMessage());

            return collect();
        }
    }

    public static function findByType(string $contractType): Collection
    {
        try {
            return static::getRepository()->findByContractType($contractType);
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in findByType(): '.$e->getMessage());

            return collect();
        }
    }

    public static function findByStatus(string $status): Collection
    {
        try {
            return static::getRepository()->findByStatus($status);
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in findByStatus(): '.$e->getMessage());

            return collect();
        }
    }

    public static function findActive(): Collection
    {
        try {
            return static::getRepository()->findActive();
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in findActive(): '.$e->getMessage());

            return collect();
        }
    }

    public static function findExpired(): Collection
    {
        try {
            return static::getRepository()->findExpired();
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in findExpired(): '.$e->getMessage());

            return collect();
        }
    }

    public static function findExpiringSoon(int $days = 30): Collection
    {
        try {
            return static::getRepository()->findExpiringSoon($days);
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in findExpiringSoon(): '.$e->getMessage());

            return collect();
        }
    }

    public static function search(string $query): Collection
    {
        try {
            return static::getRepository()->searchContracts($query);
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in search(): '.$e->getMessage());

            return collect();
        }
    }

    // Statistics and Metrics
    public static function getStatistics(): array
    {
        try {
            return static::getRepository()->getContractStatistics();
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in getStatistics(): '.$e->getMessage());

            return [];
        }
    }

    public static function getMetrics(int $contractId): array
    {
        try {
            $contract = static::find($contractId);
            if (! $contract) {
                return [];
            }

            return static::getMetricsAction()->execute($contract);
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in getMetrics(): '.$e->getMessage());

            return [];
        }
    }

    public static function getTotalCount(): int
    {
        try {
            return static::getRepository()->getTotalContractCount();
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in getTotalCount(): '.$e->getMessage());

            return 0;
        }
    }

    public static function getActiveCount(): int
    {
        try {
            return static::getRepository()->getActiveContractCount();
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in getActiveCount(): '.$e->getMessage());

            return 0;
        }
    }

    public static function getExpiredCount(): int
    {
        try {
            return static::getRepository()->getExpiredContractCount();
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in getExpiredCount(): '.$e->getMessage());

            return 0;
        }
    }

    // Utility Methods
    public static function generateContractNumber(): string
    {
        try {
            return static::getRepository()->generateContractNumber();
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in generateContractNumber(): '.$e->getMessage());

            return 'CONTRACT-'.time();
        }
    }

    public static function isContractNumberUnique(string $contractNumber): bool
    {
        try {
            return static::getRepository()->isContractNumberUnique($contractNumber);
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in isContractNumberUnique(): '.$e->getMessage());

            return false;
        }
    }

    public static function export(array $filters = []): string
    {
        try {
            return static::getRepository()->exportContractData($filters);
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in export(): '.$e->getMessage());

            return '';
        }
    }

    public static function import(string $data): bool
    {
        try {
            $result = static::getRepository()->importContractData($data);

            if ($result) {
                static::clearCache();
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('ProviderContract facade error in import(): '.$e->getMessage());

            return false;
        }
    }

    // Cache Management
    protected static function clearCache(): void
    {
        try {
            Cache::tags(['provider-contracts'])->flush();
        } catch (\Exception $e) {
            Log::warning('ProviderContract facade error clearing cache: '.$e->getMessage());
        }
    }

    // Method Chaining Support
    public static function forProvider(int $providerId): self
    {
        return new class($providerId) extends ProviderContract
        {
            private $providerId;

            public function __construct(int $providerId)
            {
                $this->providerId = $providerId;
            }

            public function getContracts(): Collection
            {
                return self::findByProvider($this->providerId);
            }

            public function getActiveContracts(): Collection
            {
                return self::getRepository()->getProviderActiveContracts($this->providerId);
            }

            public function getExpiredContracts(): Collection
            {
                return self::getRepository()->getProviderExpiredContracts($this->providerId);
            }

            public function getCount(): int
            {
                return self::getRepository()->getProviderContractCount($this->providerId);
            }
        };
    }

    public static function ofType(string $contractType): self
    {
        return new class($contractType) extends ProviderContract
        {
            private $contractType;

            public function __construct(string $contractType)
            {
                $this->contractType = $contractType;
            }

            public function getContracts(): Collection
            {
                return self::findByType($this->contractType);
            }

            public function getCount(): int
            {
                return self::getRepository()->getTotalContractCountByType($this->contractType);
            }
        };
    }

    public static function withStatus(string $status): self
    {
        return new class($status) extends ProviderContract
        {
            private $status;

            public function __construct(string $status)
            {
                $this->status = $status;
            }

            public function getContracts(): Collection
            {
                return self::findByStatus($this->status);
            }

            public function getCount(): int
            {
                return self::getRepository()->getTotalContractCountByStatus($this->status);
            }
        };
    }
}
