<?php

namespace Fereydooni\Shopping\app\Enums;

enum ProductAttributeType: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case DECIMAL = 'decimal';
    case BOOLEAN = 'boolean';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case TIME = 'time';
    case EMAIL = 'email';
    case URL = 'url';
    case PHONE = 'phone';
    case COLOR = 'color';
    case SIZE = 'size';
    case WEIGHT = 'weight';
    case DIMENSION = 'dimension';
    case MATERIAL = 'material';
    case BRAND = 'brand';
    case CATEGORY = 'category';
    case TAG = 'tag';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Text',
            self::NUMBER => 'Number',
            self::DECIMAL => 'Decimal',
            self::BOOLEAN => 'Boolean',
            self::DATE => 'Date',
            self::DATETIME => 'Date & Time',
            self::TIME => 'Time',
            self::EMAIL => 'Email',
            self::URL => 'URL',
            self::PHONE => 'Phone',
            self::COLOR => 'Color',
            self::SIZE => 'Size',
            self::WEIGHT => 'Weight',
            self::DIMENSION => 'Dimension',
            self::MATERIAL => 'Material',
            self::BRAND => 'Brand',
            self::CATEGORY => 'Category',
            self::TAG => 'Tag',
            self::CUSTOM => 'Custom',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TEXT => 'Simple text input',
            self::NUMBER => 'Whole number input',
            self::DECIMAL => 'Decimal number input',
            self::BOOLEAN => 'Yes/No or True/False input',
            self::DATE => 'Date picker input',
            self::DATETIME => 'Date and time picker input',
            self::TIME => 'Time picker input',
            self::EMAIL => 'Email address input',
            self::URL => 'URL/Website input',
            self::PHONE => 'Phone number input',
            self::COLOR => 'Color picker input',
            self::SIZE => 'Size selection input',
            self::WEIGHT => 'Weight input with units',
            self::DIMENSION => 'Dimension input (length, width, height)',
            self::MATERIAL => 'Material selection input',
            self::BRAND => 'Brand selection input',
            self::CATEGORY => 'Category selection input',
            self::TAG => 'Tag selection input',
            self::CUSTOM => 'Custom attribute type',
        };
    }

    public function isNumeric(): bool
    {
        return in_array($this, [self::NUMBER, self::DECIMAL, self::WEIGHT, self::DIMENSION]);
    }

    public function isDate(): bool
    {
        return in_array($this, [self::DATE, self::DATETIME, self::TIME]);
    }

    public function isText(): bool
    {
        return in_array($this, [self::TEXT, self::EMAIL, self::URL, self::PHONE]);
    }

    public function isSelection(): bool
    {
        return in_array($this, [self::COLOR, self::SIZE, self::MATERIAL, self::BRAND, self::CATEGORY, self::TAG]);
    }

    public function isBoolean(): bool
    {
        return $this === self::BOOLEAN;
    }

    public function isCustom(): bool
    {
        return $this === self::CUSTOM;
    }
}
