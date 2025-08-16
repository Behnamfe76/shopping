<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasAnalyticsOperations
{
    public function incrementViewCount(Model $model): bool
    {
        $model->increment('view_count');
        return true;
    }

    public function incrementWishlistCount(Model $model): bool
    {
        $model->increment('wishlist_count');
        return true;
    }

    public function updateAverageRating(Model $model): bool
    {
        $averageRating = $model->reviews()->avg('rating') ?? 0;
        $model->average_rating = $averageRating;
        $model->reviews_count = $model->reviews()->count();
        return $model->save();
    }

    public function getAnalytics(Model $model): array
    {
        return [
            'total_sales' => $model->total_sales ?? 0,
            'view_count' => $model->view_count ?? 0,
            'wishlist_count' => $model->wishlist_count ?? 0,
            'average_rating' => $model->average_rating ?? 0,
            'reviews_count' => $model->reviews_count ?? 0,
        ];
    }
}

