<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Models\Address as AddressModel;
use Fereydooni\Shopping\app\DTOs\AddressDTO;
use Fereydooni\Shopping\app\Enums\AddressType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;

/**
 * @method static Collection all()
 * @method static AddressModel|null find(int $id)
 * @method static AddressDTO|null findDTO(int $id)
 * @method static Collection findByUser(int $userId)
 * @method static Collection findByUserDTO(int $userId)
 * @method static AddressModel create(array $data)
 * @method static AddressDTO createDTO(array $data)
 * @method static bool update(AddressModel $address, array $data)
 * @method static AddressDTO|null updateDTO(AddressModel $address, array $data)
 * @method static bool delete(AddressModel $address)
 *
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static LengthAwarePaginator paginateByUser(int $userId, int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static Paginator simplePaginateByUser(int $userId, int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static CursorPaginator cursorPaginateByUser(int $userId, int $perPage = 15, string $cursor = null)
 *
 * @method static int countByUser(int $userId)
 * @method static bool deleteByUser(int $userId)
 * @method static bool updateByUser(int $userId, array $data)
 *
 * @method static Collection findByType(AddressType $type)
 * @method static Collection findByUserAndType(int $userId, AddressType $type)
 * @method static Collection findByUserAndTypeDTO(int $userId, AddressType $type)
 * @method static int countByType(AddressType $type)
 * @method static int countByUserAndType(int $userId, AddressType $type)
 * @method static bool deleteByType(AddressType $type)
 *
 * @method static bool setDefault(AddressModel $address)
 * @method static AddressDTO|null setDefaultDTO(AddressModel $address)
 * @method static bool unsetDefault(AddressModel $address)
 * @method static AddressModel|null getDefaultByUser(int $userId, AddressType $type)
 * @method static AddressDTO|null getDefaultDTOByUser(int $userId, AddressType $type)
 * @method static Collection getDefaultItems(int $userId)
 * @method static Collection getDefaultItemsDTO(int $userId)
 * @method static bool hasDefaultItem(int $userId, AddressType $type)
 * @method static bool canSetDefault(AddressModel $address)
 * @method static bool isDefault(AddressModel $address)
 *
 * @method static Collection search(string $query, ?int $userId = null, ?AddressType $type = null)
 * @method static mixed searchWithPagination(string $query, int $perPage = 15, ?int $userId = null, ?AddressType $type = null, string $paginationType = 'regular')
 * @method static array getSearchSuggestions(string $query, ?int $userId = null)
 * @method static Collection searchByField(string $field, string $value, ?int $userId = null)
 * @method static Collection advancedSearch(array $criteria, ?int $userId = null)
 *
 * @method static array getAddressStats(int $userId)
 * @method static array getAddressStatsByType(int $userId)
 * @method static Collection getAddressPath(AddressModel $address)
 * @method static Collection getAddressPathDTO(AddressModel $address)
 */
class Address extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shopping.address';
    }
}
