<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait HasImportExport
{
    /**
     * Import data from array
     */
    public function importData(array $data): bool
    {
        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                if ($this->validateImportData($item)) {
                    $this->create($item);
                }
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * Export data to array
     */
    public function exportData(): array
    {
        return $this->all()->map(function ($item) {
            return $this->formatForExport($item);
        })->toArray();
    }

    /**
     * Import from file
     */
    public function importFromFile(string $filePath): bool
    {
        if (! Storage::exists($filePath)) {
            return false;
        }

        $content = Storage::get($filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        return $this->importData($data);
    }

    /**
     * Export to file
     */
    public function exportToFile(string $filePath): bool
    {
        $data = $this->exportData();
        $content = json_encode($data, JSON_PRETTY_PRINT);

        return Storage::put($filePath, $content);
    }

    /**
     * Validate import data
     */
    protected function validateImportData(array $data): bool
    {
        if (method_exists($this, 'getValidationRules')) {
            $rules = $this->getValidationRules();
            $validator = validator($data, $rules);

            return ! $validator->fails();
        }

        return true;
    }

    /**
     * Format data for export
     */
    protected function formatForExport($item): array
    {
        return $item->toArray();
    }

    /**
     * Sync data (delete existing and create new)
     */
    public function syncData(array $data): bool
    {
        DB::beginTransaction();

        try {
            // Delete existing data
            $this->truncate();

            // Import new data
            $this->importData($data);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }
}
