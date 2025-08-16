<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait HasPricingOperations
{
    /**
     * Set the base price for a model
     */
    public function setPrice(Model $model, float $price): bool
    {
        try {
            if ($price < 0) {
                throw new \InvalidArgumentException('Price cannot be negative');
            }

            $model->price = $price;
            $model->save();

            // Trigger price update event
            if (method_exists($model, 'fireModelEvent')) {
                $model->fireModelEvent('priceUpdated');
            }

            Log::info("Price updated for {$model->getTable()} ID: {$model->id}", [
                'old_price' => $model->getOriginal('price'),
                'new_price' => $price,
                'model_type' => get_class($model)
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to set price for {$model->getTable()} ID: {$model->id}", [
                'error' => $e->getMessage(),
                'price' => $price
            ]);
            return false;
        }
    }

    /**
     * Set the sale price for a model
     */
    public function setSalePrice(Model $model, float $salePrice): bool
    {
        try {
            if ($salePrice < 0) {
                throw new \InvalidArgumentException('Sale price cannot be negative');
            }

            if ($salePrice > $model->price) {
                throw new \InvalidArgumentException('Sale price cannot be higher than base price');
            }

            $model->sale_price = $salePrice;
            $model->save();

            // Trigger sale price update event
            if (method_exists($model, 'fireModelEvent')) {
                $model->fireModelEvent('salePriceUpdated');
            }

            Log::info("Sale price updated for {$model->getTable()} ID: {$model->id}", [
                'old_sale_price' => $model->getOriginal('sale_price'),
                'new_sale_price' => $salePrice,
                'base_price' => $model->price
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to set sale price for {$model->getTable()} ID: {$model->id}", [
                'error' => $e->getMessage(),
                'sale_price' => $salePrice
            ]);
            return false;
        }
    }

    /**
     * Set the compare price for a model
     */
    public function setComparePrice(Model $model, float $comparePrice): bool
    {
        try {
            if ($comparePrice < 0) {
                throw new \InvalidArgumentException('Compare price cannot be negative');
            }

            $model->compare_price = $comparePrice;
            $model->save();

            // Trigger compare price update event
            if (method_exists($model, 'fireModelEvent')) {
                $model->fireModelEvent('comparePriceUpdated');
            }

            Log::info("Compare price updated for {$model->getTable()} ID: {$model->id}", [
                'old_compare_price' => $model->getOriginal('compare_price'),
                'new_compare_price' => $comparePrice
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to set compare price for {$model->getTable()} ID: {$model->id}", [
                'error' => $e->getMessage(),
                'compare_price' => $comparePrice
            ]);
            return false;
        }
    }

    /**
     * Calculate the effective price (sale price if available, otherwise base price)
     */
    public function getEffectivePrice(Model $model): float
    {
        $salePrice = $model->sale_price ?? 0;
        $basePrice = $model->price ?? 0;

        if ($salePrice > 0 && $salePrice < $basePrice) {
            return $salePrice;
        }

        return $basePrice;
    }

    /**
     * Calculate the discount percentage
     */
    public function getDiscountPercentage(Model $model): float
    {
        $basePrice = $model->price ?? 0;
        $salePrice = $model->sale_price ?? 0;

        if ($basePrice <= 0 || $salePrice <= 0 || $salePrice >= $basePrice) {
            return 0;
        }

        return round((($basePrice - $salePrice) / $basePrice) * 100, 2);
    }

    /**
     * Calculate the savings amount
     */
    public function getSavingsAmount(Model $model): float
    {
        $basePrice = $model->price ?? 0;
        $salePrice = $model->sale_price ?? 0;

        if ($basePrice <= 0 || $salePrice <= 0 || $salePrice >= $basePrice) {
            return 0;
        }

        return $basePrice - $salePrice;
    }

    /**
     * Check if the model is on sale
     */
    public function isOnSale(Model $model): bool
    {
        $salePrice = $model->sale_price ?? 0;
        $basePrice = $model->price ?? 0;

        return $salePrice > 0 && $salePrice < $basePrice;
    }

    /**
     * Apply a percentage discount to the base price
     */
    public function applyPercentageDiscount(Model $model, float $percentage): bool
    {
        try {
            if ($percentage < 0 || $percentage > 100) {
                throw new \InvalidArgumentException('Discount percentage must be between 0 and 100');
            }

            $basePrice = $model->price ?? 0;
            if ($basePrice <= 0) {
                throw new \InvalidArgumentException('Base price must be greater than 0');
            }

            $discountAmount = $basePrice * ($percentage / 100);
            $salePrice = $basePrice - $discountAmount;

            return $this->setSalePrice($model, $salePrice);
        } catch (\Exception $e) {
            Log::error("Failed to apply percentage discount for {$model->getTable()} ID: {$model->id}", [
                'error' => $e->getMessage(),
                'percentage' => $percentage
            ]);
            return false;
        }
    }

    /**
     * Apply a fixed amount discount to the base price
     */
    public function applyFixedDiscount(Model $model, float $discountAmount): bool
    {
        try {
            if ($discountAmount < 0) {
                throw new \InvalidArgumentException('Discount amount cannot be negative');
            }

            $basePrice = $model->price ?? 0;
            if ($basePrice <= 0) {
                throw new \InvalidArgumentException('Base price must be greater than 0');
            }

            if ($discountAmount >= $basePrice) {
                throw new \InvalidArgumentException('Discount amount cannot be greater than or equal to base price');
            }

            $salePrice = $basePrice - $discountAmount;

            return $this->setSalePrice($model, $salePrice);
        } catch (\Exception $e) {
            Log::error("Failed to apply fixed discount for {$model->getTable()} ID: {$model->id}", [
                'error' => $e->getMessage(),
                'discount_amount' => $discountAmount
            ]);
            return false;
        }
    }

    /**
     * Remove sale price (set to null)
     */
    public function removeSalePrice(Model $model): bool
    {
        try {
            $model->sale_price = null;
            $model->save();

            // Trigger sale price removal event
            if (method_exists($model, 'fireModelEvent')) {
                $model->fireModelEvent('salePriceRemoved');
            }

            Log::info("Sale price removed for {$model->getTable()} ID: {$model->id}");

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to remove sale price for {$model->getTable()} ID: {$model->id}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get pricing summary for a model
     */
    public function getPricingSummary(Model $model): array
    {
        $basePrice = $model->price ?? 0;
        $salePrice = $model->sale_price ?? 0;
        $comparePrice = $model->compare_price ?? 0;
        $effectivePrice = $this->getEffectivePrice($model);
        $discountPercentage = $this->getDiscountPercentage($model);
        $savingsAmount = $this->getSavingsAmount($model);
        $isOnSale = $this->isOnSale($model);

        return [
            'base_price' => $basePrice,
            'sale_price' => $salePrice,
            'compare_price' => $comparePrice,
            'effective_price' => $effectivePrice,
            'discount_percentage' => $discountPercentage,
            'savings_amount' => $savingsAmount,
            'is_on_sale' => $isOnSale,
            'currency' => config('shopping.currency', 'USD')
        ];
    }
}
