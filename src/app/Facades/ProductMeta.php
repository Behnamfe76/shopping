<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\DTOs\ProductMetaDTO;
use Fereydooni\Shopping\app\Models\ProductMeta;
use Fereydooni\Shopping\app\Services\ProductMetaService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static ProductMeta|null find(int $id)
 * @method static ProductMetaDTO|null findDTO(int $id)
 * @method static ProductMeta create(array $data)
 * @method static ProductMetaDTO createAndReturnDTO(array $data)
 * @method static bool update(ProductMeta $meta, array $data)
 * @method static bool delete(ProductMeta $meta)
 * @method static Collection findByProductId(int $productId)
 * @method static bool deleteByProductId(int $productId)
 * @method static bool deleteByKey(int $productId, string $metaKey)
 * @method static Collection findByMetaKey(string $metaKey)
 * @method static ProductMeta|null findByProductIdAndKey(int $productId, string $metaKey)
 * @method static Collection findByMetaType(string $metaType)
 * @method static Collection findPublic()
 * @method static Collection findPrivate()
 * @method static Collection findSearchable()
 * @method static Collection findFilterable()
 * @method static Collection search(string $query)
 * @method static Collection findByValue(string $metaValue)
 * @method static Collection findByValueLike(string $metaValue)
 * @method static bool togglePublic(ProductMeta $meta)
 * @method static bool toggleSearchable(ProductMeta $meta)
 * @method static bool toggleFilterable(ProductMeta $meta)
 * @method static Collection getMetaKeys()
 * @method static Collection getMetaTypes()
 * @method static Collection getMetaValues(string $metaKey)
 * @method static array getMetaAnalytics(string $metaKey)
 * @method static bool validateMeta(array $data)
 * @method static bool isKeyUnique(int $productId, string $metaKey, ?int $excludeId = null)
 * @method static Collection bulkCreate(int $productId, array $metaData)
 * @method static bool bulkUpdate(int $productId, array $metaData)
 * @method static bool bulkDelete(int $productId, array $metaKeys)
 * @method static bool importMeta(int $productId, array $metaData)
 * @method static array exportMeta(int $productId)
 * @method static bool syncMeta(int $productId, array $metaData)
 * @method static array getProductMetaAsArray(int $productId)
 * @method static ProductMeta setProductMeta(int $productId, string $key, string $value, array $options = [])
 * @method static mixed getProductMeta(int $productId, string $key, mixed $default = null)
 * @method static bool hasProductMeta(int $productId, string $key)
 * @method static bool removeProductMeta(int $productId, string $key)
 *
 * @see ProductMetaService
 */
class ProductMetaFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'shopping.product-meta';
    }
}
