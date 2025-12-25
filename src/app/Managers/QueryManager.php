<?php

namespace Fereydooni\Shopping\app\Managers;

use Fereydooni\Shopping\app\Contracts\QueryDriverInterface;
use Fereydooni\Shopping\app\Drivers\DatabaseQueryDriver;
use Fereydooni\Shopping\app\Drivers\TypesenseQueryDriver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class QueryManager
{
    protected array $drivers = [];

    protected string $defaultDriver;

    public function __construct()
    {
        $this->defaultDriver = config('shopping.query_method', 'database');
        $this->registerDefaultDrivers();
    }

    protected function registerDefaultDrivers(): void
    {
        $this->registerDriver('database', new DatabaseQueryDriver);
        $this->registerDriver('typesense', new TypesenseQueryDriver);
    }

    public function registerDriver(string $name, QueryDriverInterface $driver): void
    {
        $this->drivers[$name] = $driver;
    }

    public function getDriver(?string $name = null): QueryDriverInterface
    {
        $driverName = $name ?? $this->defaultDriver;
        if (! isset($this->drivers[$driverName])) {
            throw new \InvalidArgumentException("Query driver '{$driverName}' not found.");
        }

        return $this->drivers[$driverName];
    }

    public function getAvailableDrivers(): array
    {
        return array_keys($this->drivers);
    }

    public function setDefaultDriver(string $driverName): void
    {
        if (! isset($this->drivers[$driverName])) {
            throw new \InvalidArgumentException("Query driver '{$driverName}' not found.");
        }

        $this->defaultDriver = $driverName;
    }

    public function getDefaultDriver(): string
    {
        return $this->defaultDriver;
    }

    // Proxy methods to the current driver
    public function paginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15, ?string $driver = null): LengthAwarePaginator
    {
        $queryDriver = $this->getDriver($driver);
        if (! $queryDriver->supports($model)) {
            // Fallback to database driver if the requested driver doesn't support the model
            $queryDriver = $this->getDriver('database');
        }

        return $queryDriver->paginate($model, $filters, $searchOptions, $perPage);
    }

    public function simplePaginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15, ?string $driver = null): Paginator
    {
        $queryDriver = $this->getDriver($driver);

        if (! $queryDriver->supports($model)) {
            $queryDriver = $this->getDriver('database');
        }

        return $queryDriver->simplePaginate($model, $filters, $searchOptions, $perPage);
    }

    public function cursorPaginate(string $model, array $filters = [], array $searchOptions = [], int $perPage = 15, ?string $cursor = null, ?string $driver = null): CursorPaginator
    {
        $queryDriver = $this->getDriver($driver);

        if (! $queryDriver->supports($model)) {
            $queryDriver = $this->getDriver('database');
        }

        return $queryDriver->cursorPaginate($model, $filters, $searchOptions, $perPage, $cursor);
    }

    public function search(string $model, string $query, array $fields = [], array $filters = [], ?string $driver = null): Collection
    {
        $queryDriver = $this->getDriver($driver);

        if (! $queryDriver->supports($model)) {
            $queryDriver = $this->getDriver('database');
        }

        return $queryDriver->search($model, $query, $fields, $filters);
    }

    public function all(string $model, array $filters = [], ?string $driver = null): Collection
    {
        $queryDriver = $this->getDriver($driver);

        if (! $queryDriver->supports($model)) {
            $queryDriver = $this->getDriver('database');
        }

        return $queryDriver->all($model, $filters);
    }

    /**
     * Get the best available driver for a given model
     */
    public function getBestDriverForModel(string $model): string
    {
        foreach ($this->drivers as $name => $driver) {
            if ($driver->supports($model)) {
                return $name;
            }
        }

        return 'database'; // Fallback to database
    }
}
