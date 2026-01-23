<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\DTOs\DepartmentDTO;
use Fereydooni\Shopping\app\Enums\DepartmentStatus;
use Fereydooni\Shopping\app\Models\Department as DepartmentModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static CursorPaginator cursorAll()
 * @method static DepartmentModel|null find(int $id)
 * @method static DepartmentDTO|null findDTO(int $id)
 * @method static DepartmentModel|null findByCode(string $code)
 * @method static DepartmentDTO|null findByCodeDTO(string $code)
 * @method static DepartmentModel create(array $data)
 * @method static DepartmentDTO createDTO(array $data)
 * @method static bool update(DepartmentModel $department, array $data)
 * @method static DepartmentDTO|null updateDTO(DepartmentModel $department, array $data)
 * @method static bool delete(DepartmentModel $department)
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static Collection findByStatus(DepartmentStatus $status)
 * @method static Collection findByStatusDTO(DepartmentStatus $status)
 * @method static Collection findByLocation(string $location)
 * @method static Collection getRootDepartments()
 * @method static Collection getRootDepartmentsDTO()
 * @method static Collection getChildren(int $parentId)
 * @method static Collection getChildrenDTO(int $parentId)
 * @method static Collection getAncestors(int $departmentId)
 * @method static Collection getAncestorsDTO(int $departmentId)
 * @method static Collection getDescendants(int $departmentId)
 * @method static Collection getDescendantsDTO(int $departmentId)
 * @method static Collection getTree()
 * @method static Collection getTreeDTO()
 * @method static bool moveDepartment(DepartmentModel $department, ?int $newParentId)
 * @method static array getDepartmentStats()
 * @method static array getDepartmentStatsByStatus()
 * @method static Collection getDepartmentPath(int $departmentId)
 * @method static Collection getDepartmentPathDTO(int $departmentId)
 * @method static int getDepth(DepartmentModel $department)
 * @method static Collection getByDepth(int $depth)
 * @method static int getDepartmentCount()
 * @method static int getDepartmentCountByStatus(DepartmentStatus $status)
 * @method static int getDepartmentCountByParent(int $parentId)
 * @method static bool validateDepartment(array $data)
 * @method static bool deleteSome(array $ids)
 * @method static bool deleteAll()
 */
class Department extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shopping.department';
    }
}
