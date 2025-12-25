<?php

namespace Fereydooni\Shopping\App\Enums;

enum LocationType: string
{
    case HEADQUARTERS = 'headquarters';
    case WAREHOUSE = 'warehouse';
    case STORE = 'store';
    case OFFICE = 'office';
    case FACTORY = 'factory';
    case DISTRIBUTION_CENTER = 'distribution_center';
    case RETAIL_OUTLET = 'retail_outlet';
    case SERVICE_CENTER = 'service_center';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::HEADQUARTERS => 'Headquarters',
            self::WAREHOUSE => 'Warehouse',
            self::STORE => 'Store',
            self::OFFICE => 'Office',
            self::FACTORY => 'Factory',
            self::DISTRIBUTION_CENTER => 'Distribution Center',
            self::RETAIL_OUTLET => 'Retail Outlet',
            self::SERVICE_CENTER => 'Service Center',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::HEADQUARTERS => 'Main administrative office and company headquarters',
            self::WAREHOUSE => 'Storage facility for inventory and goods',
            self::STORE => 'Retail location for customer sales',
            self::OFFICE => 'Administrative or business office',
            self::FACTORY => 'Manufacturing or production facility',
            self::DISTRIBUTION_CENTER => 'Centralized distribution hub',
            self::RETAIL_OUTLET => 'Customer-facing retail location',
            self::SERVICE_CENTER => 'Service and support facility',
            self::OTHER => 'Other type of location',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }
}
