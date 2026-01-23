<?php

namespace Fereydooni\Shopping\app\Models;

use Laravel\Scout\Searchable;
use Modules\Core\Traits\HasEnhancedActivityLog;

class Permission extends \Spatie\Permission\Models\Permission
{
    use Searchable;
    use HasEnhancedActivityLog;

    // Activity log configuration flags
    protected bool $logUserInfo = true;
    protected bool $logRequestMetadata = true;
    protected bool $logBrowserInfo = true;
    protected bool $logModelSnapshot = true;
    protected ?string $activityLogName = 'permissions';
    protected ?string $activityDescription = 'Permission has been {event}';

    public static function toScoutModelSettings(): array
    {
        return [
            self::class => [
                'collection-schema' => self::getTypesenseCollectionSchema(),
                'search-parameters' => [
                    'query_by' => implode(',', self::searchableFields()),
                ],
            ],
        ];
    }

    public static function searchableFields(): array
    {
        return [
            'name',
            'guard_name'
        ];
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'id_numeric' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'meta' => $this->meta ? json_encode($this->meta) : '',
            'created_at' => $this->created_at?->timestamp,
            'updated_at' => $this->updated_at?->timestamp,
        ];
    }

    /**
     * Define the Typesense collection schema.
     */
    public static function getTypesenseCollectionSchema(): array
    {
        return [
            'fields' => [
                [
                    'name' => 'id',
                    'type' => 'string',
                    'facet' => false,
                ],
                [
                    'name' => 'id_numeric',
                    'type' => 'int64',
                    'facet' => false,
                ],
                [
                    'name' => 'name',
                    'type' => 'string',
                    'facet' => true,
                ],
                [
                    'name' => 'guard_name',
                    'type' => 'string',
                    'facet' => true,
                ],
                [
                    'name' => 'meta',
                    'type' => 'string',
                    'facet' => false,
                    'optional' => true,
                ],
                [
                    'name' => 'created_at',
                    'type' => 'int64',
                    'facet' => false,
                ],
                [
                    'name' => 'updated_at',
                    'type' => 'int64',
                    'facet' => false,
                ]
            ],
            'default_sorting_field' => 'created_at',
        ];
    }

    public function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
