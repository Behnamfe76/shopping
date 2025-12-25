<?php

namespace App\Traits;

use App\Models\Employee;
use App\Models\EmployeeEmergencyContact;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasEmployeeEmergencyContactImportExport
{
    /**
     * Export emergency contact data to CSV.
     */
    public function exportEmergencyContactsToCSV(array $filters = []): string
    {
        try {
            $contacts = $this->getContactsForExport($filters);

            $filename = 'emergency_contacts_'.date('Y-m-d_H-i-s').'.csv';
            $filepath = storage_path('app/exports/'.$filename);

            // Ensure directory exists
            if (! file_exists(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }

            $file = fopen($filepath, 'w');

            // Write headers
            $headers = [
                'ID',
                'Employee ID',
                'Employee Name',
                'Contact Name',
                'Relationship',
                'Primary Phone',
                'Secondary Phone',
                'Email',
                'Address',
                'City',
                'State',
                'Postal Code',
                'Country',
                'Is Primary',
                'Is Active',
                'Notes',
                'Created At',
                'Updated At',
            ];

            fputcsv($file, $headers);

            // Write data
            foreach ($contacts as $contact) {
                $row = [
                    $contact->id,
                    $contact->employee_id,
                    $contact->employee->full_name ?? 'N/A',
                    $contact->contact_name,
                    $contact->relationship,
                    $contact->phone_primary,
                    $contact->phone_secondary ?? '',
                    $contact->email ?? '',
                    $contact->address ?? '',
                    $contact->city ?? '',
                    $contact->state ?? '',
                    $contact->postal_code ?? '',
                    $contact->country ?? '',
                    $contact->is_primary ? 'Yes' : 'No',
                    $contact->is_active ? 'Yes' : 'No',
                    $contact->notes ?? '',
                    $contact->created_at,
                    $contact->updated_at,
                ];

                fputcsv($file, $row);
            }

            fclose($file);

            Log::info('Emergency contacts exported to CSV', [
                'filename' => $filename,
                'contact_count' => $contacts->count(),
                'filters' => $filters,
            ]);

            return $filepath;

        } catch (\Exception $e) {
            Log::error('Failed to export emergency contacts to CSV', [
                'error' => $e->getMessage(),
                'filters' => $filters,
            ]);
            throw $e;
        }
    }

    /**
     * Export emergency contact data to JSON.
     */
    public function exportEmergencyContactsToJSON(array $filters = []): string
    {
        try {
            $contacts = $this->getContactsForExport($filters);

            $filename = 'emergency_contacts_'.date('Y-m-d_H-i-s').'.json';
            $filepath = storage_path('app/exports/'.$filename);

            // Ensure directory exists
            if (! file_exists(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }

            $data = [
                'export_date' => now()->toISOString(),
                'total_contacts' => $contacts->count(),
                'filters_applied' => $filters,
                'contacts' => $contacts->map(function ($contact) {
                    return [
                        'id' => $contact->id,
                        'employee_id' => $contact->employee_id,
                        'employee_name' => $contact->employee->full_name ?? 'N/A',
                        'contact_name' => $contact->contact_name,
                        'relationship' => $contact->relationship,
                        'phone_primary' => $contact->phone_primary,
                        'phone_secondary' => $contact->phone_secondary,
                        'email' => $contact->email,
                        'address' => $contact->address,
                        'city' => $contact->city,
                        'state' => $contact->state,
                        'postal_code' => $contact->postal_code,
                        'country' => $contact->country,
                        'is_primary' => $contact->is_primary,
                        'is_active' => $contact->is_active,
                        'notes' => $contact->notes,
                        'created_at' => $contact->created_at,
                        'updated_at' => $contact->updated_at,
                    ];
                })->toArray(),
            ];

            file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));

            Log::info('Emergency contacts exported to JSON', [
                'filename' => $filename,
                'contact_count' => $contacts->count(),
                'filters' => $filters,
            ]);

            return $filepath;

        } catch (\Exception $e) {
            Log::error('Failed to export emergency contacts to JSON', [
                'error' => $e->getMessage(),
                'filters' => $filters,
            ]);
            throw $e;
        }
    }

    /**
     * Import emergency contacts from CSV.
     */
    public function importEmergencyContactsFromCSV(string $filepath, array $options = []): array
    {
        try {
            $results = [
                'total_rows' => 0,
                'imported' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [],
            ];

            if (! file_exists($filepath)) {
                throw new \Exception("Import file not found: {$filepath}");
            }

            $file = fopen($filepath, 'r');
            $headers = fgetcsv($file);
            $results['total_rows'] = -1; // Exclude header row

            // Skip header row
            fgetcsv($file);

            DB::beginTransaction();

            while (($row = fgetcsv($file)) !== false) {
                $results['total_rows']++;

                try {
                    $data = $this->parseCSVRow($headers, $row);
                    $result = $this->processImportRow($data, $options);

                    if ($result['action'] === 'imported') {
                        $results['imported']++;
                    } elseif ($result['action'] === 'updated') {
                        $results['updated']++;
                    } else {
                        $results['skipped']++;
                    }

                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'row' => $results['total_rows'] + 1,
                        'error' => $e->getMessage(),
                        'data' => $row,
                    ];
                }
            }

            fclose($file);

            if (empty($results['errors'])) {
                DB::commit();
                Log::info('Emergency contacts imported successfully from CSV', $results);
            } else {
                DB::rollBack();
                Log::warning('Emergency contacts import completed with errors', $results);
            }

            return $results;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import emergency contacts from CSV', [
                'filepath' => $filepath,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Parse CSV row data.
     */
    private function parseCSVRow(array $headers, array $row): array
    {
        $data = [];

        foreach ($headers as $index => $header) {
            if (isset($row[$index])) {
                $key = strtolower(str_replace(' ', '_', $header));
                $value = trim($row[$index]);

                // Convert boolean values
                if (in_array($key, ['is_primary', 'is_active'])) {
                    $value = in_array(strtolower($value), ['yes', 'true', '1', 'y']);
                }

                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * Process import row data.
     */
    private function processImportRow(array $data, array $options): array
    {
        // Validate required fields
        if (empty($data['contact_name']) || empty($data['phone_primary'])) {
            throw new \Exception('Contact name and primary phone are required');
        }

        // Find employee
        $employee = Employee::find($data['employee_id']);
        if (! $employee) {
            throw new \Exception("Employee not found with ID: {$data['employee_id']}");
        }

        // Check if contact already exists
        $existingContact = EmployeeEmergencyContact::where('employee_id', $data['employee_id'])
            ->where('contact_name', $data['contact_name'])
            ->first();

        if ($existingContact) {
            if (! empty($options['update_existing'])) {
                $existingContact->update($data);

                return ['action' => 'updated', 'contact_id' => $existingContact->id];
            } else {
                return ['action' => 'skipped', 'reason' => 'Contact already exists'];
            }
        } else {
            $contact = EmployeeEmergencyContact::create($data);

            return ['action' => 'imported', 'contact_id' => $contact->id];
        }
    }

    /**
     * Get contacts for export with filters.
     */
    private function getContactsForExport(array $filters = []): Collection
    {
        $query = EmployeeEmergencyContact::with('employee');

        // Apply filters
        if (! empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (! empty($filters['relationship'])) {
            $query->where('relationship', $filters['relationship']);
        }

        if (! empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (! empty($filters['is_primary'])) {
            $query->where('is_primary', $filters['is_primary']);
        }

        if (! empty($filters['city'])) {
            $query->where('city', 'like', "%{$filters['city']}%");
        }

        if (! empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        if (! empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        return $query->orderBy('employee_id')->orderBy('is_primary', 'desc')->get();
    }

    /**
     * Generate emergency contact import template.
     */
    public function generateImportTemplate(): string
    {
        $filename = 'emergency_contact_import_template.csv';
        $filepath = storage_path('app/templates/'.$filename);

        // Ensure directory exists
        if (! file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // Write headers with example data
        $headers = [
            'Employee ID',
            'Contact Name',
            'Relationship',
            'Primary Phone',
            'Secondary Phone',
            'Email',
            'Address',
            'City',
            'State',
            'Postal Code',
            'Country',
            'Is Primary',
            'Is Active',
            'Notes',
        ];

        fputcsv($file, $headers);

        // Write example row
        $example = [
            '1',
            'John Doe',
            'spouse',
            '+1-555-123-4567',
            '+1-555-987-6543',
            'john.doe@example.com',
            '123 Main St',
            'New York',
            'NY',
            '10001',
            'United States',
            'Yes',
            'Yes',
            'Emergency contact for Jane Doe',
        ];

        fputcsv($file, $example);
        fclose($file);

        return $filepath;
    }

    /**
     * Get import/export statistics.
     */
    public function getImportExportStatistics(): array
    {
        return [
            'total_contacts' => EmployeeEmergencyContact::count(),
            'active_contacts' => EmployeeEmergencyContact::where('is_active', true)->count(),
            'primary_contacts' => EmployeeEmergencyContact::where('is_primary', true)->count(),
            'contacts_by_relationship' => EmployeeEmergencyContact::selectRaw('relationship, COUNT(*) as count')
                ->groupBy('relationship')
                ->pluck('count', 'relationship')
                ->toArray(),
            'contacts_by_country' => EmployeeEmergencyContact::selectRaw('country, COUNT(*) as count')
                ->whereNotNull('country')
                ->groupBy('country')
                ->pluck('count', 'country')
                ->toArray(),
            'recent_imports' => $this->getRecentImportHistory(),
        ];
    }

    /**
     * Get recent import history.
     */
    private function getRecentImportHistory(): array
    {
        // This could be implemented with a separate import_logs table
        // For now, return empty array
        return [];
    }
}
