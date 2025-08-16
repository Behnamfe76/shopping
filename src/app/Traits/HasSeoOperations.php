<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasSeoOperations
{
    public function updateSeoFields(Model $model, array $seoData): bool
    {
        $model->meta_title = $seoData['meta_title'] ?? null;
        $model->meta_description = $seoData['meta_description'] ?? null;
        $model->meta_keywords = $seoData['meta_keywords'] ?? null;
        $model->seo_url = $seoData['seo_url'] ?? null;
        $model->canonical_url = $seoData['canonical_url'] ?? null;
        $model->og_image = $seoData['og_image'] ?? null;
        $model->twitter_image = $seoData['twitter_image'] ?? null;

        return $model->save();
    }

    public function generateMetaTitle(Model $model): string
    {
        return $model->meta_title ?? $model->title ?? '';
    }

    public function generateMetaDescription(Model $model): string
    {
        return $model->meta_description ?? substr($model->description ?? '', 0, 160);
    }

    public function generateCanonicalUrl(Model $model): string
    {
        return $model->canonical_url ?? url($model->seo_url ?? $model->slug);
    }
}
