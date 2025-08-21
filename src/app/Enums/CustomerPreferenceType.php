<?php

namespace Fereydooni\Shopping\app\Enums;

enum CustomerPreferenceType: string
{
    case STRING = 'string';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case BOOLEAN = 'boolean';
    case JSON = 'json';
    case ARRAY = 'array';
    case OBJECT = 'object';

    /**
     * Get all preference types
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get preference type label
     */
    public function label(): string
    {
        return match($this) {
            self::STRING => 'String',
            self::INTEGER => 'Integer',
            self::FLOAT => 'Float',
            self::BOOLEAN => 'Boolean',
            self::JSON => 'JSON',
            self::ARRAY => 'Array',
            self::OBJECT => 'Object',
        };
    }

    /**
     * Get preference type description
     */
    public function description(): string
    {
        return match($this) {
            self::STRING => 'Text-based preference value',
            self::INTEGER => 'Whole number preference value',
            self::FLOAT => 'Decimal number preference value',
            self::BOOLEAN => 'True/false preference value',
            self::JSON => 'JSON formatted preference value',
            self::ARRAY => 'Array preference value',
            self::OBJECT => 'Object preference value',
        };
    }

    /**
     * Check if type is numeric
     */
    public function isNumeric(): bool
    {
        return in_array($this, [self::INTEGER, self::FLOAT]);
    }

    /**
     * Check if type is primitive
     */
    public function isPrimitive(): bool
    {
        return in_array($this, [self::STRING, self::INTEGER, self::FLOAT, self::BOOLEAN]);
    }

    /**
     * Check if type is complex
     */
    public function isComplex(): bool
    {
        return in_array($this, [self::JSON, self::ARRAY, self::OBJECT]);
    }

    /**
     * Get validation rules for the type
     */
    public function validationRules(): array
    {
        return match($this) {
            self::STRING => ['string', 'max:65535'],
            self::INTEGER => ['integer'],
            self::FLOAT => ['numeric'],
            self::BOOLEAN => ['boolean'],
            self::JSON => ['json'],
            self::ARRAY => ['array'],
            self::OBJECT => ['array'],
        };
    }

    /**
     * Cast value to the appropriate type
     */
    public function castValue(mixed $value): mixed
    {
        return match($this) {
            self::STRING => (string) $value,
            self::INTEGER => (int) $value,
            self::FLOAT => (float) $value,
            self::BOOLEAN => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            self::JSON => is_string($value) ? json_decode($value, true) : $value,
            self::ARRAY => is_array($value) ? $value : [$value],
            self::OBJECT => is_array($value) ? (object) $value : $value,
        };
    }
}
