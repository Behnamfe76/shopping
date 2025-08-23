<?php

namespace Fereydooni\Shopping\app\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeEmergencyContactRepositoryInterface;
use Fereydooni\Shopping\app\Models\EmployeeEmergencyContact;
use Fereydooni\Shopping\app\DTOs\EmployeeEmergencyContactDTO;

class EmployeeEmergencyContactRepository implements EmployeeEmergencyContactRepositoryInterface
{
    protected $model;
    protected $cachePrefix = 'employee_emergency_contact';

    public function __construct(EmployeeEmergencyContact $model)
    {
        $this->model = $model;
    }

    // Basic CRUD operations
    public function all(): Collection
    {
        return Cache::remember("{$this->cachePrefix}:all", 3600, function () {
            return $this->model->with('employee')->get();
        });
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('employee')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return $this->model->with('employee')
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return $this->model->with('employee')
            ->orderBy('created_at', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function find(int $id): ?EmployeeEmergencyContact
    {
        return Cache::remember("{$this->cachePrefix}:{$id}", 3600, function () use ($id) {
            return $this->model->with('employee')->find($id);
        });
    }

    public function findDTO(int $id): ?EmployeeEmergencyContactDTO
    {
        $contact = $this->find($id);
        return $contact ? EmployeeEmergencyContactDTO::fromModel($contact) : null;
    }

    public function create(array $data): EmployeeEmergencyContact
    {
        try {
            DB::beginTransaction();

            $contact = $this->model->create($data);

            // If this is set as primary, remove primary status from other contacts
            if ($contact->is_primary) {
                $this->model->where('employee_id', $contact->employee_id)
                    ->where('id', '!=', $contact->id)
                    ->update(['is_primary' => false]);
            }

            DB::commit();
            $this->clearCache();

            return $contact->load('employee');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create emergency contact: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createAndReturnDTO(array $data): EmployeeEmergencyContactDTO
    {
        $contact = $this->create($data);
        return EmployeeEmergencyContactDTO::fromModel($contact);
    }

    public function update(EmployeeEmergencyContact $contact, array $data): bool
    {
        try {
            DB::beginTransaction();

            $result = $contact->update($data);

            // If this is set as primary, remove primary status from other contacts
            if (isset($data['is_primary']) && $data['is_primary']) {
                $this->model->where('employee_id', $contact->employee_id)
                    ->where('id', '!=', $contact->id)
                    ->update(['is_primary' => false]);
            }

            DB::commit();
            $this->clearCache();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update emergency contact: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateAndReturnDTO(EmployeeEmergencyContact $contact, array $data): ?EmployeeEmergencyContactDTO
    {
        $result = $this->update($contact, $data);
        return $result ? EmployeeEmergencyContactDTO::fromModel($contact->fresh()) : null;
    }

    public function delete(EmployeeEmergencyContact $contact): bool
    {
        try {
            $result = $contact->delete();
            $this->clearCache();
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to delete emergency contact: ' . $e->getMessage());
            throw $e;
        }
    }

    // Find by specific criteria
    public function findByEmployeeId(int $employeeId): Collection
    {
        return Cache::remember("{$this->cachePrefix}:employee:{$employeeId}", 3600, function () use ($employeeId) {
            return $this->model->with('employee')
                ->where('employee_id', $employeeId)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function findByEmployeeIdDTO(int $employeeId): Collection
    {
        return $this->findByEmployeeId($employeeId)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    public function findByRelationship(string $relationship): Collection
    {
        return $this->model->with('employee')
            ->where('relationship', $relationship)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByRelationshipDTO(string $relationship): Collection
    {
        return $this->findByRelationship($relationship)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    public function findByContactName(string $contactName): Collection
    {
        return $this->model->with('employee')
            ->where('contact_name', 'like', "%{$contactName}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByContactNameDTO(string $contactName): Collection
    {
        return $this->findByContactName($contactName)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    public function findByPhone(string $phone): Collection
    {
        return $this->model->with('employee')
            ->where(function ($query) use ($phone) {
                $query->where('phone_primary', 'like', "%{$phone}%")
                      ->orWhere('phone_secondary', 'like', "%{$phone}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByPhoneDTO(string $phone): Collection
    {
        return $this->findByPhone($phone)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    public function findByEmail(string $email): Collection
    {
        return $this->model->with('employee')
            ->where('email', 'like', "%{$email}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByEmailDTO(string $email): Collection
    {
        return $this->findByEmail($email)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    public function findByEmployeeAndRelationship(int $employeeId, string $relationship): Collection
    {
        return $this->model->with('employee')
            ->where('employee_id', $employeeId)
            ->where('relationship', $relationship)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByEmployeeAndRelationshipDTO(int $employeeId, string $relationship): Collection
    {
        return $this->findByEmployeeAndRelationship($employeeId, $relationship)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    // Status-based queries
    public function findPrimary(): Collection
    {
        return $this->model->with('employee')
            ->where('is_primary', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findPrimaryDTO(): Collection
    {
        return $this->findPrimary()->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    public function findActive(): Collection
    {
        return $this->model->with('employee')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findActiveDTO(): Collection
    {
        return $this->findActive()->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    public function findInactive(): Collection
    {
        return $this->model->with('employee')
            ->where('is_active', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findInactiveDTO(): Collection
    {
        return $this->findInactive()->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    // Location-based queries
    public function findByCity(string $city): Collection
    {
        return $this->model->with('employee')
            ->where('city', 'like', "%{$city}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByCityDTO(string $city): Collection
    {
        return $this->findByCity($city)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    public function findByState(string $state): Collection
    {
        return $this->model->with('employee')
            ->where('state', 'like', "%{$state}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByStateDTO(string $state): Collection
    {
        return $this->findByState($state)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    public function findByCountry(string $country): Collection
    {
        return $this->model->with('employee')
            ->where('country', 'like', "%{$country}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByCountryDTO(string $country): Collection
    {
        return $this->findByCountry($country)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    // Status management
    public function activate(EmployeeEmergencyContact $contact): bool
    {
        $result = $contact->activate();
        $this->clearCache();
        return $result;
    }

    public function deactivate(EmployeeEmergencyContact $contact): bool
    {
        $result = $contact->deactivate();
        $this->clearCache();
        return $result;
    }

    public function setAsPrimary(EmployeeEmergencyContact $contact): bool
    {
        $result = $contact->setAsPrimary();
        $this->clearCache();
        return $result;
    }

    public function removePrimary(EmployeeEmergencyContact $contact): bool
    {
        $result = $contact->removePrimary();
        $this->clearCache();
        return $result;
    }

    // Count and statistics
    public function getEmployeeContactCount(int $employeeId): int
    {
        return Cache::remember("{$this->cachePrefix}:count:employee:{$employeeId}", 3600, function () use ($employeeId) {
            return $this->model->where('employee_id', $employeeId)->count();
        });
    }

    public function getEmployeeContactCountByRelationship(int $employeeId, string $relationship): int
    {
        return $this->model->where('employee_id', $employeeId)
            ->where('relationship', $relationship)
            ->count();
    }

    public function getEmployeePrimaryContact(int $employeeId): ?EmployeeEmergencyContact
    {
        return Cache::remember("{$this->cachePrefix}:primary:employee:{$employeeId}", 3600, function () use ($employeeId) {
            return $this->model->with('employee')
                ->where('employee_id', $employeeId)
                ->where('is_primary', true)
                ->first();
        });
    }

    public function getEmployeePrimaryContactDTO(int $employeeId): ?EmployeeEmergencyContactDTO
    {
        $contact = $this->getEmployeePrimaryContact($employeeId);
        return $contact ? EmployeeEmergencyContactDTO::fromModel($contact) : null;
    }

    public function getEmployeeActiveContacts(int $employeeId): Collection
    {
        return Cache::remember("{$this->cachePrefix}:active:employee:{$employeeId}", 3600, function () use ($employeeId) {
            return $this->model->with('employee')
                ->where('employee_id', $employeeId)
                ->where('is_active', true)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public function getEmployeeActiveContactsDTO(int $employeeId): Collection
    {
        return $this->getEmployeeActiveContacts($employeeId)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    public function getTotalContactCount(): int
    {
        return Cache::remember("{$this->cachePrefix}:count:total", 3600, function () {
            return $this->model->count();
        });
    }

    public function getTotalContactCountByRelationship(string $relationship): int
    {
        return $this->model->where('relationship', $relationship)->count();
    }

    public function getTotalPrimaryContacts(): int
    {
        return Cache::remember("{$this->cachePrefix}:count:primary", 3600, function () {
            return $this->model->where('is_primary', true)->count();
        });
    }

    public function getTotalActiveContacts(): int
    {
        return Cache::remember("{$this->cachePrefix}:count:active", 3600, function () {
            return $this->model->where('is_active', true)->count();
        });
    }

    // Search functionality
    public function searchContacts(string $query): Collection
    {
        return $this->model->with('employee')
            ->where(function ($q) use ($query) {
                $q->where('contact_name', 'like', "%{$query}%")
                  ->orWhere('phone_primary', 'like', "%{$query}%")
                  ->orWhere('phone_secondary', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%")
                  ->orWhere('city', 'like', "%{$query}%")
                  ->orWhere('state', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchContactsDTO(string $query): Collection
    {
        return $this->searchContacts($query)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    public function searchContactsByEmployee(int $employeeId, string $query): Collection
    {
        return $this->model->with('employee')
            ->where('employee_id', $employeeId)
            ->where(function ($q) use ($query) {
                $q->where('contact_name', 'like', "%{$query}%")
                  ->orWhere('phone_primary', 'like', "%{$query}%")
                  ->orWhere('phone_secondary', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%")
                  ->orWhere('city', 'like', "%{$query}%")
                  ->orWhere('state', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function searchContactsByEmployeeDTO(int $employeeId, string $query): Collection
    {
        return $this->searchContactsByEmployee($employeeId, $query)->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    // Import/Export
    public function exportContactData(array $filters = []): string
    {
        $query = $this->model->with('employee');

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['relationship'])) {
            $query->where('relationship', $filters['relationship']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $contacts = $query->get();

        $csvData = [];
        $csvData[] = ['ID', 'Employee ID', 'Contact Name', 'Relationship', 'Phone Primary', 'Phone Secondary', 'Email', 'Address', 'City', 'State', 'Postal Code', 'Country', 'Is Primary', 'Is Active', 'Notes', 'Created At'];

        foreach ($contacts as $contact) {
            $csvData[] = [
                $contact->id,
                $contact->employee_id,
                $contact->contact_name,
                $contact->relationship->value,
                $contact->phone_primary,
                $contact->phone_secondary,
                $contact->email,
                $contact->address,
                $contact->city,
                $contact->state,
                $contact->postal_code,
                $contact->country,
                $contact->is_primary ? 'Yes' : 'No',
                $contact->is_active ? 'Yes' : 'No',
                $contact->notes,
                $contact->created_at
            ];
        }

        $output = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    public function importContactData(string $data): bool
    {
        try {
            DB::beginTransaction();

            $lines = explode("\n", trim($data));
            $headers = str_getcsv(array_shift($lines));

            foreach ($lines as $line) {
                if (empty(trim($line))) continue;

                $row = array_combine($headers, str_getcsv($line));

                if (isset($row['Employee ID']) && isset($row['Contact Name'])) {
                    $this->model->create([
                        'employee_id' => $row['Employee ID'],
                        'contact_name' => $row['Contact Name'],
                        'relationship' => $row['Relationship'] ?? 'other',
                        'phone_primary' => $row['Phone Primary'] ?? null,
                        'phone_secondary' => $row['Phone Secondary'] ?? null,
                        'email' => $row['Email'] ?? null,
                        'address' => $row['Address'] ?? null,
                        'city' => $row['City'] ?? null,
                        'state' => $row['State'] ?? null,
                        'postal_code' => $row['Postal Code'] ?? null,
                        'country' => $row['Country'] ?? null,
                        'is_primary' => strtolower($row['Is Primary'] ?? 'no') === 'yes',
                        'is_active' => strtolower($row['Is Active'] ?? 'yes') === 'yes',
                        'notes' => $row['Notes'] ?? null,
                    ]);
                }
            }

            DB::commit();
            $this->clearCache();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to import contact data: ' . $e->getMessage());
            return false;
        }
    }

    // Statistics and analytics
    public function getContactStatistics(): array
    {
        return Cache::remember("{$this->cachePrefix}:statistics", 3600, function () {
            return [
                'total_contacts' => $this->getTotalContactCount(),
                'active_contacts' => $this->getTotalActiveContacts(),
                'primary_contacts' => $this->getTotalPrimaryContacts(),
                'contacts_by_relationship' => $this->getContactDistribution(),
                'contacts_by_country' => $this->model->selectRaw('country, COUNT(*) as count')
                    ->groupBy('country')
                    ->pluck('count', 'country')
                    ->toArray(),
                'contacts_by_state' => $this->model->selectRaw('state, COUNT(*) as count')
                    ->groupBy('state')
                    ->pluck('count', 'state')
                    ->toArray(),
            ];
        });
    }

    public function getEmployeeContactStatistics(int $employeeId): array
    {
        return Cache::remember("{$this->cachePrefix}:statistics:employee:{$employeeId}", 3600, function () use ($employeeId) {
            $contacts = $this->findByEmployeeId($employeeId);

            return [
                'total_contacts' => $contacts->count(),
                'active_contacts' => $contacts->where('is_active', true)->count(),
                'primary_contacts' => $contacts->where('is_primary', true)->count(),
                'contacts_by_relationship' => $contacts->groupBy('relationship')
                    ->map(function ($group) {
                        return $group->count();
                    })->toArray(),
                'has_primary' => $contacts->where('is_primary', true)->isNotEmpty(),
                'has_active' => $contacts->where('is_active', true)->isNotEmpty(),
            ];
        });
    }

    public function getContactDistribution(): array
    {
        return Cache::remember("{$this->cachePrefix}:distribution", 3600, function () {
            return $this->model->selectRaw('relationship, COUNT(*) as count')
                ->groupBy('relationship')
                ->pluck('count', 'relationship')
                ->toArray();
        });
    }

    public function validateContactInformation(int $contactId): array
    {
        $contact = $this->find($contactId);
        if (!$contact) {
            return ['valid' => false, 'errors' => ['Contact not found']];
        }

        $errors = [];

        if (!$contact->hasValidPhone()) {
            $errors[] = 'No valid phone number provided';
        }

        if (!$contact->hasValidEmail() && !$contact->hasValidAddress()) {
            $errors[] = 'Either email or address must be provided';
        }

        if ($contact->is_primary && !$contact->hasValidPhone()) {
            $errors[] = 'Primary contact must have a valid phone number';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'contact_method' => $contact->getContactMethod(),
            'has_phone' => $contact->hasValidPhone(),
            'has_email' => $contact->hasValidEmail(),
            'has_address' => $contact->hasValidAddress(),
        ];
    }

    // Helper methods
    protected function clearCache(): void
    {
        Cache::forget("{$this->cachePrefix}:all");
        Cache::forget("{$this->cachePrefix}:count:total");
        Cache::forget("{$this->cachePrefix}:count:primary");
        Cache::forget("{$this->cachePrefix}:count:active");
        Cache::forget("{$this->cachePrefix}:statistics");
        Cache::forget("{$this->cachePrefix}:distribution");

        // Clear employee-specific caches
        $employeeIds = $this->model->distinct('employee_id')->pluck('employee_id');
        foreach ($employeeIds as $employeeId) {
            Cache::forget("{$this->cachePrefix}:employee:{$employeeId}");
            Cache::forget("{$this->cachePrefix}:count:employee:{$employeeId}");
            Cache::forget("{$this->cachePrefix}:primary:employee:{$employeeId}");
            Cache::forget("{$this->cachePrefix}:active:employee:{$employeeId}");
            Cache::forget("{$this->cachePrefix}:statistics:employee:{$employeeId}");
        }
    }
}
