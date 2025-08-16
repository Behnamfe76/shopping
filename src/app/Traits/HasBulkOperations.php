<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait HasBulkOperations
{
    /**
     * Bulk create records
     */
    public function bulkCreate(array $data): Collection
    {
        $created = collect();

        foreach ($data as $item) {
            if ($this->validateBulkData($item)) {
                $created->push($this->create($item));
            }
        }

        return $created;
    }

    /**
     * Bulk update records
     */
    public function bulkUpdate(array $data): bool
    {
        $success = true;

        foreach ($data as $item) {
            if (isset($item['id'])) {
                $model = $this->find($item['id']);
                if ($model && $this->validateBulkData($item)) {
                    if (!$this->update($model, $item)) {
                        $success = false;
                    }
                }
            }
        }

        return $success;
    }

    /**
     * Bulk delete records
     */
    public function bulkDelete(array $ids): bool
    {
        return $this->whereIn('id', $ids)->delete() > 0;
    }

    /**
     * Bulk validate data
     */
    protected function validateBulkData(array $data): bool
    {
        if (method_exists($this, 'getValidationRules')) {
            $rules = $this->getValidationRules();
            $validator = Validator::make($data, $rules);
            return !$validator->fails();
        }

        return true;
    }

    /**
     * Bulk operation with transaction
     */
    public function bulkOperationWithTransaction(callable $operation): bool
    {
        DB::beginTransaction();

        try {
            $result = $operation();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
