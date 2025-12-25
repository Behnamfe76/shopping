<?php

namespace Fereydooni\Shopping\app\Enums;

enum NetworkType: string
{
    case PPO = 'ppo';
    case HMO = 'hmo';
    case EPO = 'epo';
    case POS = 'pos';
    case HDHP = 'hdhp';

    public function label(): string
    {
        return match ($this) {
            self::PPO => 'PPO (Preferred Provider Organization)',
            self::HMO => 'HMO (Health Maintenance Organization)',
            self::EPO => 'EPO (Exclusive Provider Organization)',
            self::POS => 'POS (Point of Service)',
            self::HDHP => 'HDHP (High Deductible Health Plan)',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PPO => 'Flexible network with out-of-network coverage',
            self::HMO => 'Restricted network requiring referrals',
            self::EPO => 'Exclusive network with no out-of-network coverage',
            self::POS => 'Combination of HMO and PPO features',
            self::HDHP => 'High deductible plan with health savings account',
        };
    }

    public function requiresReferral(): bool
    {
        return in_array($this, [self::HMO, self::POS]);
    }

    public function hasOutOfNetworkCoverage(): bool
    {
        return in_array($this, [self::PPO, self::POS]);
    }

    public function isHighDeductible(): bool
    {
        return $this === self::HDHP;
    }

    public function getNetworkFlexibility(): string
    {
        return match ($this) {
            self::PPO => 'High',
            self::HMO => 'Low',
            self::EPO => 'None',
            self::POS => 'Medium',
            self::HDHP => 'Varies',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->label();

            return $carry;
        }, []);
    }
}
