<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Collection all()
 * @method static \Illuminate\Database\Eloquent\Collection allDTO()
 * @method static \Illuminate\Pagination\LengthAwarePaginator paginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\Paginator simplePaginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static \Fereydooni\Shopping\app\Models\Brand|null find(int $id)
 * @method static \Fereydooni\Shopping\app\DTOs\BrandDTO|null findDTO(int $id)
 * @method static \Fereydooni\Shopping\app\Models\Brand|null findBySlug(string $slug)
 * @method static \Fereydooni\Shopping\app\DTOs\BrandDTO|null findBySlugDTO(string $slug)
 * @method static \Fereydooni\Shopping\app\Models\Brand create(array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\BrandDTO createDTO(array $data)
 * @method static bool update(\Fereydooni\Shopping\app\Models\Brand $brand, array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\BrandDTO|null updateDTO(\Fereydooni\Shopping\app\Models\Brand $brand, array $data)
 * @method static bool delete(\Fereydooni\Shopping\app\Models\Brand $brand)
 * @method static bool toggleActive(\Fereydooni\Shopping\app\Models\Brand $brand)
 * @method static bool toggleFeatured(\Fereydooni\Shopping\app\Models\Brand $brand)
 * @method static \Illuminate\Database\Eloquent\Collection getActive()
 * @method static \Illuminate\Database\Eloquent\Collection getActiveDTO()
 * @method static \Illuminate\Database\Eloquent\Collection getFeatured()
 * @method static \Illuminate\Database\Eloquent\Collection getFeaturedDTO()
 * @method static \Illuminate\Database\Eloquent\Collection getPopular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getPopularDTO(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getWithProducts()
 * @method static \Illuminate\Database\Eloquent\Collection getWithProductsDTO()
 * @method static \Illuminate\Database\Eloquent\Collection search(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchDTO(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection getByFirstLetter(string $letter)
 * @method static \Illuminate\Database\Eloquent\Collection getByFirstLetterDTO(string $letter)
 * @method static int getCount()
 * @method static int getActiveCount()
 * @method static int getFeaturedCount()
 * @method static bool validate(array $data)
 * @method static string generateSlug(string $name, ?int $excludeId = null)
 * @method static bool isSlugUnique(string $slug, ?int $excludeId = null)
 * @method static \Spatie\MediaLibrary\MediaCollections\Models\Media uploadLogo(\Fereydooni\Shopping\app\Models\Brand $brand, \Illuminate\Http\UploadedFile $file)
 * @method static \Spatie\MediaLibrary\MediaCollections\Models\Media uploadBanner(\Fereydooni\Shopping\app\Models\Brand $brand, \Illuminate\Http\UploadedFile $file)
 * @method static string|null getLogoUrl(\Fereydooni\Shopping\app\Models\Brand $brand)
 * @method static string|null getBannerUrl(\Fereydooni\Shopping\app\Models\Brand $brand)
 * @method static bool deleteLogo(\Fereydooni\Shopping\app\Models\Brand $brand)
 * @method static bool deleteBanner(\Fereydooni\Shopping\app\Models\Brand $brand)
 */
class Brand extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shopping.brand';
    }
}
