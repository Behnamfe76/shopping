<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\DTOs\TeamDTO;
use Fereydooni\Shopping\app\Enums\TeamStatus;
use Fereydooni\Shopping\app\Models\Team as TeamModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static CursorPaginator cursorAll()
 * @method static TeamModel|null find(int $id)
 * @method static TeamDTO|null findDTO(int $id)
 * @method static TeamModel|null findByCode(string $code)
 * @method static TeamDTO|null findByCodeDTO(string $code)
 * @method static TeamModel create(array $data)
 * @method static TeamDTO createDTO(array $data)
 * @method static bool update(TeamModel $team, array $data)
 * @method static TeamDTO|null updateDTO(TeamModel $team, array $data)
 * @method static bool delete(TeamModel $team)
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Paginator simplePaginate(int $perPage = 15)
 * @method static CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static Collection findByStatus(TeamStatus $status)
 * @method static Collection findByStatusDTO(TeamStatus $status)
 * @method static Collection findByLocation(string $location)
 * @method static Collection getByDepartment(int $departmentId)
 * @method static Collection getByDepartmentDTO(int $departmentId)
 * @method static array getTeamStats()
 * @method static array getTeamStatsByStatus()
 * @method static int getTeamCount()
 * @method static int getTeamCountByStatus(TeamStatus $status)
 * @method static int getTeamCountByDepartment(int $departmentId)
 * @method static bool addMember(TeamModel $team, int $employeeId, bool $isManager = false)
 * @method static bool removeMember(TeamModel $team, int $employeeId)
 * @method static bool promoteToManager(TeamModel $team, int $employeeId)
 * @method static bool demoteFromManager(TeamModel $team, int $employeeId)
 * @method static bool changeManager(TeamModel $team, int $oldManagerId, int $newManagerId)
 * @method static Collection getMembers(TeamModel $team)
 * @method static Collection getManagers(TeamModel $team)
 * @method static bool validateTeam(array $data)
 * @method static bool deleteSome(array $ids)
 * @method static bool deleteAll()
 */
class Team extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shopping.team';
    }
}
