<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Models\ProductTagModel;
use Fereydooni\Shopping\app\DTOs\ProductTagDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method static Collection all()
 * @method static lens()
 * @method static ProductTagModel|null find(int $id)
 * @method static ProductTagDTO|null findDTO(int $id)
 * @method static ProductTagModel|null findBySlug(string $slug)
 * @method static ProductTagDTO|null findBySlugDTO(string $slug)
 * @method static ProductTagModel|null findByName(string $name)
 * @method static ProductTagDTO|null findByNameDTO(string $name)
 * @method static Collection findActive()
 * @method static Collection findActiveDTO()
 * @method static Collection findFeatured()
 * @method static Collection findFeaturedDTO()
 * @method static Collection findByUsageCount(int $minCount)
 * @method static Collection findByUsageCountDTO(int $minCount)
 * @method static Collection findPopular(int $limit = 10)
 * @method static Collection findPopularDTO(int $limit = 10)
 * @method static Collection findRecent(int $limit = 10)
 * @method static Collection findRecentDTO(int $limit = 10)
 * @method static Collection findByColor(string $color)
 * @method static Collection findByColorDTO(string $color)
 * @method static Collection findByIcon(string $icon)
 * @method static Collection findByIconDTO(string $icon)
 * @method static ProductTagModel create(array $data)
 * @method static ProductTagDTO createAndReturnDTO(array $data)
 * @method static bool update(ProductTagModel $tag, array $data)
 * @method static bool delete(ProductTagModel $tag)
 * @method static bool toggleActive(ProductTagModel $tag)
 * @method static bool toggleFeatured(ProductTagModel $tag)
 * @method static bool incrementUsage(ProductTagModel $tag)
 * @method static bool decrementUsage(ProductTagModel $tag)
 * @method static int getTagCount()
 * @method static int getActiveTagCount()
 * @method static int getFeaturedTagCount()
 * @method static int getPopularTagCount()
 * @method static Collection search(string $query)
 * @method static Collection searchDTO(string $query)
 * @method static Collection getTagNames()
 * @method static Collection getTagSlugs()
 * @method static Collection getTagColors()
 * @method static Collection getTagIcons()
 * @method static bool validateTag(array $data)
 * @method static bool isNameUnique(string $name, ?int $excludeId = null)
 * @method static bool isSlugUnique(string $slug, ?int $excludeId = null)
 * @method static string generateSlug(string $name)
 * @method static int getTagUsage(int $tagId)
 * @method static int getTagUsageByProduct(int $tagId, int $productId)
 * @method static array getTagAnalytics(int $tagId)
 * @method static array getTagAnalyticsByProduct(int $tagId)
 * @method static array getTagTrends(int $tagId, string $period = 'month')
 * @method static array getTagComparison(int $tagId1, int $tagId2)
 * @method static array getTagRecommendations(int $productId)
 * @method static array getTagForecast(int $tagId, string $period = 'month')
 * @method static array getTagPerformance(int $tagId)
 * @method static float getTagROI(int $tagId)
 * @method static float getTagConversionRate(int $tagId)
 * @method static float getTagAverageOrderValue(int $tagId)
 * @method static float getTagCustomerRetention(int $tagId)
 * @method static Collection bulkCreate(array $tagData)
 * @method static bool bulkUpdate(array $tagData)
 * @method static bool bulkDelete(array $tagIds)
 * @method static bool importTags(array $tagData)
 * @method static array exportTags()
 * @method static bool syncTags(int $productId, array $tagIds)
 * @method static bool mergeTags(int $tagId1, int $tagId2)
 * @method static bool splitTags(int $tagId, array $newTagNames)
 * @method static Collection getTagSuggestions(string $query)
 * @method static Collection getTagAutocomplete(string $query)
 * @method static Collection getTagRelated(int $tagId)
 * @method static Collection getTagSynonyms(int $tagId)
 * @method static array getTagHierarchy(int $tagId)
 * @method static array getTagTree()
 * @method static array getTagCloud()
 * @method static array getTagStats()
 * @method static void optimizeTagQueries()
 * @method static void cacheTagData()
 * @method static void indexTagData()
 */
class ProductTag extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'shopping.product-tag';
    }
}
