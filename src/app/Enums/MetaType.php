<?php

namespace Fereydooni\Shopping\app\Enums;

enum MetaType: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case BOOLEAN = 'boolean';
    case JSON = 'json';
    case URL = 'url';
    case EMAIL = 'email';
    case PHONE = 'phone';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case TIME = 'time';
    case COLOR = 'color';
    case FILE = 'file';
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case DOCUMENT = 'document';
    case LOCATION = 'location';
    case PRICE = 'price';
    case CURRENCY = 'currency';
    case PERCENTAGE = 'percentage';
    case DIMENSION = 'dimension';
    case WEIGHT = 'weight';
    case VOLUME = 'volume';
    case TEMPERATURE = 'temperature';
    case SPEED = 'speed';
    case DURATION = 'duration';
    case RATING = 'rating';
    case SCORE = 'score';
    case RANK = 'rank';
    case PRIORITY = 'priority';
    case STATUS = 'status';
    case CATEGORY = 'category';
    case TAG = 'tag';
    case BRAND = 'brand';
    case MODEL = 'model';
    case SERIAL = 'serial';
    case SKU = 'sku';
    case UPC = 'upc';
    case EAN = 'ean';
    case ISBN = 'isbn';
    case CUSTOM = 'custom';

    public function getLabel(): string
    {
        return match($this) {
            self::TEXT => 'Text',
            self::NUMBER => 'Number',
            self::BOOLEAN => 'Boolean',
            self::JSON => 'JSON',
            self::URL => 'URL',
            self::EMAIL => 'Email',
            self::PHONE => 'Phone',
            self::DATE => 'Date',
            self::DATETIME => 'DateTime',
            self::TIME => 'Time',
            self::COLOR => 'Color',
            self::FILE => 'File',
            self::IMAGE => 'Image',
            self::VIDEO => 'Video',
            self::AUDIO => 'Audio',
            self::DOCUMENT => 'Document',
            self::LOCATION => 'Location',
            self::PRICE => 'Price',
            self::CURRENCY => 'Currency',
            self::PERCENTAGE => 'Percentage',
            self::DIMENSION => 'Dimension',
            self::WEIGHT => 'Weight',
            self::VOLUME => 'Volume',
            self::TEMPERATURE => 'Temperature',
            self::SPEED => 'Speed',
            self::DURATION => 'Duration',
            self::RATING => 'Rating',
            self::SCORE => 'Score',
            self::RANK => 'Rank',
            self::PRIORITY => 'Priority',
            self::STATUS => 'Status',
            self::CATEGORY => 'Category',
            self::TAG => 'Tag',
            self::BRAND => 'Brand',
            self::MODEL => 'Model',
            self::SERIAL => 'Serial',
            self::SKU => 'SKU',
            self::UPC => 'UPC',
            self::EAN => 'EAN',
            self::ISBN => 'ISBN',
            self::CUSTOM => 'Custom',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::TEXT => 'Plain text value',
            self::NUMBER => 'Numeric value',
            self::BOOLEAN => 'True or false value',
            self::JSON => 'JSON formatted data',
            self::URL => 'Web URL or link',
            self::EMAIL => 'Email address',
            self::PHONE => 'Phone number',
            self::DATE => 'Date value',
            self::DATETIME => 'Date and time value',
            self::TIME => 'Time value',
            self::COLOR => 'Color value (hex, rgb, etc.)',
            self::FILE => 'File path or reference',
            self::IMAGE => 'Image file or URL',
            self::VIDEO => 'Video file or URL',
            self::AUDIO => 'Audio file or URL',
            self::DOCUMENT => 'Document file or URL',
            self::LOCATION => 'Geographic location',
            self::PRICE => 'Price value',
            self::CURRENCY => 'Currency code',
            self::PERCENTAGE => 'Percentage value',
            self::DIMENSION => 'Physical dimensions',
            self::WEIGHT => 'Weight value',
            self::VOLUME => 'Volume value',
            self::TEMPERATURE => 'Temperature value',
            self::SPEED => 'Speed value',
            self::DURATION => 'Duration or time period',
            self::RATING => 'Rating value (1-5, etc.)',
            self::SCORE => 'Score value',
            self::RANK => 'Ranking value',
            self::PRIORITY => 'Priority level',
            self::STATUS => 'Status value',
            self::CATEGORY => 'Category classification',
            self::TAG => 'Tag or label',
            self::BRAND => 'Brand name',
            self::MODEL => 'Model number or name',
            self::SERIAL => 'Serial number',
            self::SKU => 'Stock Keeping Unit',
            self::UPC => 'Universal Product Code',
            self::EAN => 'European Article Number',
            self::ISBN => 'International Standard Book Number',
            self::CUSTOM => 'Custom or user-defined type',
        };
    }

    public function getValidationRules(): array
    {
        return match($this) {
            self::TEXT => ['string', 'max:65535'],
            self::NUMBER => ['numeric'],
            self::BOOLEAN => ['boolean'],
            self::JSON => ['json'],
            self::URL => ['url'],
            self::EMAIL => ['email'],
            self::PHONE => ['string', 'regex:/^[\+]?[1-9][\d]{0,15}$/'],
            self::DATE => ['date'],
            self::DATETIME => ['date'],
            self::TIME => ['date_format:H:i:s'],
            self::COLOR => ['string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            self::FILE => ['string', 'max:255'],
            self::IMAGE => ['string', 'max:255'],
            self::VIDEO => ['string', 'max:255'],
            self::AUDIO => ['string', 'max:255'],
            self::DOCUMENT => ['string', 'max:255'],
            self::LOCATION => ['string', 'max:255'],
            self::PRICE => ['numeric', 'min:0'],
            self::CURRENCY => ['string', 'size:3'],
            self::PERCENTAGE => ['numeric', 'min:0', 'max:100'],
            self::DIMENSION => ['string', 'max:255'],
            self::WEIGHT => ['numeric', 'min:0'],
            self::VOLUME => ['numeric', 'min:0'],
            self::TEMPERATURE => ['numeric'],
            self::SPEED => ['numeric', 'min:0'],
            self::DURATION => ['string', 'max:255'],
            self::RATING => ['numeric', 'min:0', 'max:10'],
            self::SCORE => ['numeric'],
            self::RANK => ['integer', 'min:1'],
            self::PRIORITY => ['integer', 'min:1', 'max:10'],
            self::STATUS => ['string', 'max:50'],
            self::CATEGORY => ['string', 'max:255'],
            self::TAG => ['string', 'max:255'],
            self::BRAND => ['string', 'max:255'],
            self::MODEL => ['string', 'max:255'],
            self::SERIAL => ['string', 'max:255'],
            self::SKU => ['string', 'max:255'],
            self::UPC => ['string', 'size:12'],
            self::EAN => ['string', 'size:13'],
            self::ISBN => ['string', 'regex:/^(?:[0-9]{10}|[0-9]{13})$/'],
            self::CUSTOM => ['string', 'max:65535'],
        };
    }

}
