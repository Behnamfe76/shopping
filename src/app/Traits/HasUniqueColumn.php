<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Support\Str;

trait HasUniqueColumn
{
    protected static function bootHasUniqueColumn(): void
    {
        static::creating(function ($model) {
            $model->{$model->getUniqueColumnField()} = $model->generateUniqueColumnValue();
        });
    }

    /**
     * Define the field name used for the unique column.
     */
    protected function getUniqueColumnField(): string
    {
        return property_exists($this, 'uniqueColumnField') ? $this->uniqueColumnField : 'unique_column';
    }


    /**
     * Define your signature configuration here or override in a model.
     */
    protected function getUniqueColumnSignature(): array
    {
        return property_exists($this, 'uniqueColumnSignature') ? $this->uniqueColumnSignature : [
            'length' => 8,
            'type'   => 'alphanumeric', // options: numeric, alpha, alphanumeric
            'prefix' => '',             // optional prefix
            'suffix' => '',             // optional suffix
        ];
    }

    /**
     * Generate a unique column value with a given pattern.
     */
    public function generateUniqueColumnValue(): string
    {
        $signature = $this->getUniqueColumnSignature();
        $field = $this->getUniqueColumnField();

        do {
            $fieldValue = $signature['prefix'] ?? '';
            $fieldValue .= match ($signature['type'] ?? 'alphanumeric') {
                'numeric' => str_pad(random_int(0, pow(10, $signature['length']) - 1), $signature['length'], '0', STR_PAD_LEFT),
                'alpha' => collect(range('A', 'Z'))->random($signature['length'])->implode(''),
                default => Str::upper(Str::random($signature['length'])),
            };
            $fieldValue .= $signature['suffix'] ?? '';
        } while (
            static::where($field, $fieldValue)->exists()
        );

        return $fieldValue;
    }
}
