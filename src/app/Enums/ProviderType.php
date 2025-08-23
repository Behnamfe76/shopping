<?php

namespace Fereydooni\Shopping\App\Enums;

enum ProviderType: string
{
    case MANUFACTURER = 'manufacturer';
    case DISTRIBUTOR = 'distributor';
    case WHOLESALER = 'wholesaler';
    case RETAILER = 'retailer';
    case SERVICE_PROVIDER = 'service_provider';
    case LOGISTICS = 'logistics';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::MANUFACTURER->value => 'Manufacturer',
            self::DISTRIBUTOR->value => 'Distributor',
            self::WHOLESALER->value => 'Wholesaler',
            self::RETAILER->value => 'Retailer',
            self::SERVICE_PROVIDER->value => 'Service Provider',
            self::LOGISTICS->value => 'Logistics',
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }

    public function isManufacturer(): bool
    {
        return $this === self::MANUFACTURER;
    }

    public function isDistributor(): bool
    {
        return $this === self::DISTRIBUTOR;
    }

    public function isWholesaler(): bool
    {
        return $this === self::WHOLESALER;
    }

    public function isRetailer(): bool
    {
        return $this === self::RETAILER;
    }

    public function isServiceProvider(): bool
    {
        return $this === self::SERVICE_PROVIDER;
    }

    public function isLogistics(): bool
    {
        return $this === self::LOGISTICS;
    }

    public function canManufacture(): bool
    {
        return $this === self::MANUFACTURER;
    }

    public function canDistribute(): bool
    {
        return in_array($this, [self::DISTRIBUTOR, self::WHOLESALER]);
    }

    public function canRetail(): bool
    {
        return $this === self::RETAILER;
    }

    public function canProvideServices(): bool
    {
        return in_array($this, [self::SERVICE_PROVIDER, self::LOGISTICS]);
    }

    public function getDefaultCommissionRate(): float
    {
        return match($this) {
            self::MANUFACTURER => 5.0,
            self::DISTRIBUTOR => 8.0,
            self::WHOLESALER => 10.0,
            self::RETAILER => 15.0,
            self::SERVICE_PROVIDER => 12.0,
            self::LOGISTICS => 7.0,
        };
    }

    public function getDefaultCreditLimit(): float
    {
        return match($this) {
            self::MANUFACTURER => 50000.0,
            self::DISTRIBUTOR => 25000.0,
            self::WHOLESALER => 15000.0,
            self::RETAILER => 10000.0,
            self::SERVICE_PROVIDER => 20000.0,
            self::LOGISTICS => 30000.0,
        };
    }
}
