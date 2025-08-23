<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;
use Fereydooni\Shopping\app\Services\EmployeeNoteService;
use Fereydooni\Shopping\app\DTOs\EmployeeNoteDTO;
use Illuminate\Support\Collection;

/**
 * @method static EmployeeNoteDTO create(array $data)
 * @method static EmployeeNoteDTO update(int $id, array $data)
 * @method static bool delete(int $id)
 * @method static EmployeeNoteDTO|null find(int $id)
 * @method static Collection findByEmployee(int $employeeId)
 * @method static Collection search(string $query)
 * @method static bool archive(int $id)
 * @method static bool unarchive(int $id)
 * @method static bool makePrivate(int $id)
 * @method static bool makePublic(int $id)
 * @method static bool addTags(int $id, array $tags)
 * @method static bool removeTags(int $id, array $tags)
 * @method static bool addAttachment(int $id, string $attachmentPath)
 * @method static bool removeAttachment(int $id, string $attachmentPath)
 * @method static array getStatistics(int $employeeId = null)
 * @method static Collection getRecent(int $employeeId, int $limit = 10)
 * @method static string export(int $employeeId, string $format = 'json')
 * @method static bool import(int $employeeId, string $data, string $format = 'json')
 */
class EmployeeNote extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EmployeeNoteService::class;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        $instance = static::getFacadeRoot();

        if (!$instance) {
            throw new \RuntimeException('A facade root has not been set.');
        }

        return $instance->$name(...$arguments);
    }
}
