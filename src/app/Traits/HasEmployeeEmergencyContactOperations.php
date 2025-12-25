<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\DTOs\EmployeeEmergencyContactDTO;
use Fereydooni\Shopping\app\Models\EmployeeEmergencyContact;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeEmergencyContactRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait HasEmployeeEmergencyContactOperations
{
    protected $emergencyContactRepository;

    /**
     * Get the emergency contact repository instance.
     */
    protected function getEmergencyContactRepository(): EmployeeEmergencyContactRepositoryInterface
    {
        if (! $this->emergencyContactRepository) {
            $this->emergencyContactRepository = app(EmployeeEmergencyContactRepositoryInterface::class);
        }

        return $this->emergencyContactRepository;
    }

    /**
     * Get all emergency contacts.
     */
    public function getAllEmergencyContacts(): Collection
    {
        return $this->getEmergencyContactRepository()->all();
    }

    /**
     * Get all emergency contacts as DTOs.
     */
    public function getAllEmergencyContactsDTO(): Collection
    {
        return $this->getAllEmergencyContacts()->map(function ($contact) {
            return EmployeeEmergencyContactDTO::fromModel($contact);
        });
    }

    /**
     * Paginate emergency contacts.
     */
    public function paginateEmergencyContacts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->getEmergencyContactRepository()->paginate($perPage);
    }

    /**
     * Find emergency contact by ID.
     */
    public function findEmergencyContact(int $id): ?EmployeeEmergencyContact
    {
        return $this->getEmergencyContactRepository()->find($id);
    }

    /**
     * Find emergency contact by ID and return as DTO.
     */
    public function findEmergencyContactDTO(int $id): ?EmployeeEmergencyContactDTO
    {
        return $this->getEmergencyContactRepository()->findDTO($id);
    }

    /**
     * Create a new emergency contact.
     */
    public function createEmergencyContact(array $data): EmployeeEmergencyContact
    {
        return $this->getEmergencyContactRepository()->create($data);
    }

    /**
     * Create a new emergency contact and return as DTO.
     */
    public function createEmergencyContactDTO(array $data): EmployeeEmergencyContactDTO
    {
        return $this->getEmergencyContactRepository()->createAndReturnDTO($data);
    }

    /**
     * Update an emergency contact.
     */
    public function updateEmergencyContact(EmployeeEmergencyContact $contact, array $data): bool
    {
        return $this->getEmergencyContactRepository()->update($contact, $data);
    }

    /**
     * Update an emergency contact and return as DTO.
     */
    public function updateEmergencyContactDTO(EmployeeEmergencyContact $contact, array $data): ?EmployeeEmergencyContactDTO
    {
        return $this->getEmergencyContactRepository()->updateAndReturnDTO($contact, $data);
    }

    /**
     * Delete an emergency contact.
     */
    public function deleteEmergencyContact(EmployeeEmergencyContact $contact): bool
    {
        return $this->getEmergencyContactRepository()->delete($contact);
    }

    /**
     * Find emergency contacts by employee ID.
     */
    public function findEmergencyContactsByEmployee(int $employeeId): Collection
    {
        return $this->getEmergencyContactRepository()->findByEmployeeId($employeeId);
    }

    /**
     * Find emergency contacts by employee ID and return as DTOs.
     */
    public function findEmergencyContactsByEmployeeDTO(int $employeeId): Collection
    {
        return $this->getEmergencyContactRepository()->findByEmployeeIdDTO($employeeId);
    }

    /**
     * Find emergency contacts by relationship.
     */
    public function findEmergencyContactsByRelationship(string $relationship): Collection
    {
        return $this->getEmergencyContactRepository()->findByRelationship($relationship);
    }

    /**
     * Find emergency contacts by relationship and return as DTOs.
     */
    public function findEmergencyContactsByRelationshipDTO(string $relationship): Collection
    {
        return $this->getEmergencyContactRepository()->findByRelationshipDTO($relationship);
    }

    /**
     * Search emergency contacts.
     */
    public function searchEmergencyContacts(string $query): Collection
    {
        return $this->getEmergencyContactRepository()->searchContacts($query);
    }

    /**
     * Search emergency contacts and return as DTOs.
     */
    public function searchEmergencyContactsDTO(string $query): Collection
    {
        return $this->getEmergencyContactRepository()->searchContactsDTO($query);
    }

    /**
     * Search emergency contacts by employee.
     */
    public function searchEmergencyContactsByEmployee(int $employeeId, string $query): Collection
    {
        return $this->getEmergencyContactRepository()->searchContactsByEmployee($employeeId, $query);
    }

    /**
     * Search emergency contacts by employee and return as DTOs.
     */
    public function searchEmergencyContactsByEmployeeDTO(int $employeeId, string $query): Collection
    {
        return $this->getEmergencyContactRepository()->searchContactsByEmployeeDTO($employeeId, $query);
    }

    /**
     * Get emergency contact count for an employee.
     */
    public function getEmergencyContactCount(int $employeeId): int
    {
        return $this->getEmergencyContactRepository()->getEmployeeContactCount($employeeId);
    }

    /**
     * Get emergency contact count by relationship for an employee.
     */
    public function getEmergencyContactCountByRelationship(int $employeeId, string $relationship): int
    {
        return $this->getEmergencyContactRepository()->getEmployeeContactCountByRelationship($employeeId, $relationship);
    }

    /**
     * Get total emergency contact count.
     */
    public function getTotalEmergencyContactCount(): int
    {
        return $this->getEmergencyContactRepository()->getTotalContactCount();
    }

    /**
     * Get emergency contact statistics.
     */
    public function getEmergencyContactStatistics(): array
    {
        return $this->getEmergencyContactRepository()->getContactStatistics();
    }

    /**
     * Get emergency contact statistics for an employee.
     */
    public function getEmployeeEmergencyContactStatistics(int $employeeId): array
    {
        return $this->getEmergencyContactRepository()->getEmployeeContactStatistics($employeeId);
    }

    /**
     * Export emergency contact data.
     */
    public function exportEmergencyContactData(array $filters = []): string
    {
        return $this->getEmergencyContactRepository()->exportContactData($filters);
    }

    /**
     * Import emergency contact data.
     */
    public function importEmergencyContactData(string $data): bool
    {
        return $this->getEmergencyContactRepository()->importContactData($data);
    }

    /**
     * Validate emergency contact information.
     */
    public function validateEmergencyContactInformation(int $contactId): array
    {
        return $this->getEmergencyContactRepository()->validateContactInformation($contactId);
    }

    /**
     * Check if employee has emergency contacts.
     */
    public function hasEmergencyContacts(int $employeeId): bool
    {
        return $this->getEmergencyContactCount($employeeId) > 0;
    }

    /**
     * Check if employee has active emergency contacts.
     */
    public function hasActiveEmergencyContacts(int $employeeId): bool
    {
        $contacts = $this->findEmergencyContactsByEmployee($employeeId);

        return $contacts->where('is_active', true)->isNotEmpty();
    }

    /**
     * Check if employee has primary emergency contact.
     */
    public function hasPrimaryEmergencyContact(int $employeeId): bool
    {
        $contacts = $this->findEmergencyContactsByEmployee($employeeId);

        return $contacts->where('is_primary', true)->isNotEmpty();
    }

    /**
     * Get emergency contacts by contact name.
     */
    public function findEmergencyContactsByName(string $contactName): Collection
    {
        return $this->getEmergencyContactRepository()->findByContactName($contactName);
    }

    /**
     * Get emergency contacts by contact name and return as DTOs.
     */
    public function findEmergencyContactsByNameDTO(string $contactName): Collection
    {
        return $this->getEmergencyContactRepository()->findByContactNameDTO($contactName);
    }

    /**
     * Get emergency contacts by phone number.
     */
    public function findEmergencyContactsByPhone(string $phone): Collection
    {
        return $this->getEmergencyContactRepository()->findByPhone($phone);
    }

    /**
     * Get emergency contacts by phone number and return as DTOs.
     */
    public function findEmergencyContactsByPhoneDTO(string $phone): Collection
    {
        return $this->getEmergencyContactRepository()->findByPhoneDTO($phone);
    }

    /**
     * Get emergency contacts by email.
     */
    public function findEmergencyContactsByEmail(string $email): Collection
    {
        return $this->getEmergencyContactRepository()->findByEmail($email);
    }

    /**
     * Get emergency contacts by email and return as DTOs.
     */
    public function findEmergencyContactsByEmailDTO(string $email): Collection
    {
        return $this->getEmergencyContactRepository()->findByEmailDTO($email);
    }

    /**
     * Get emergency contacts by city.
     */
    public function findEmergencyContactsByCity(string $city): Collection
    {
        return $this->getEmergencyContactRepository()->findByCity($city);
    }

    /**
     * Get emergency contacts by city and return as DTOs.
     */
    public function findEmergencyContactsByCityDTO(string $city): Collection
    {
        return $this->getEmergencyContactRepository()->findByCityDTO($city);
    }

    /**
     * Get emergency contacts by state.
     */
    public function findEmergencyContactsByState(string $state): Collection
    {
        return $this->getEmergencyContactRepository()->findByState($state);
    }

    /**
     * Get emergency contacts by state and return as DTOs.
     */
    public function findEmergencyContactsByStateDTO(string $state): Collection
    {
        return $this->getEmergencyContactRepository()->findByStateDTO($state);
    }

    /**
     * Get emergency contacts by country.
     */
    public function findEmergencyContactsByCountry(string $country): Collection
    {
        return $this->getEmergencyContactRepository()->findByCountry($country);
    }

    /**
     * Get emergency contacts by country and return as DTOs.
     */
    public function findEmergencyContactsByCountryDTO(string $country): Collection
    {
        return $this->getEmergencyContactRepository()->findByCountryDTO($country);
    }
}
