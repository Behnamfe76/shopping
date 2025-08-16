<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasInventoryManagement
{
    public function updateStock(Model $model, int $quantity, string $operation = 'decrease'): bool
    {
        if ($operation === 'decrease') {
            $model->stock_quantity = max(0, $model->stock_quantity - $quantity);
        } else {
            $model->stock_quantity += $quantity;
        }

        return $model->save();
    }

    public function reserveStock(Model $model, int $quantity): bool
    {
        if ($model->stock_quantity < $quantity) {
            return false;
        }

        return $this->updateStock($model, $quantity, 'decrease');
    }

    public function releaseStock(Model $model, int $quantity): bool
    {
        return $this->updateStock($model, $quantity, 'increase');
    }

    public function getInventoryLevel(Model $model): int
    {
        return $model->stock_quantity ?? 0;
    }

    public function isLowStock(Model $model, int $threshold = 10): bool
    {
        return $this->getInventoryLevel($model) <= $threshold;
    }

    public function isOutOfStock(Model $model): bool
    {
        return $this->getInventoryLevel($model) <= 0;
    }
}
