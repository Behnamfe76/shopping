<?php

namespace Fereydooni\Shopping\app\Repositories\Interfaces;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Fereydooni\Shopping\app\Models\EmployeeEmergencyContact;
use Fereydooni\Shopping\app\DTOs\EmployeeEmergencyContactDTO;

interface EmployeeEmergencyContactRepositoryInterface
{
    // Basic CRUD operations
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function simplePaginate(int $perPage = 15): Paginator;
    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator;
    public function find(int $id): ?EmployeeEmergencyContact;
    public function findDTO(int $id): ?EmployeeEmergencyContactDTO;
    public function create(array $data): EmployeeEmergencyContact;
    public function createAndReturnDTO(array $data): EmployeeEmergencyContactDTO;
    public function update(EmployeeEmergencyContact $contact, array $data): bool;
    public function updateAndReturnDTO(EmployeeEmergencyContact $contact, array $data): ?EmployeeEmergencyContactDTO;
    public function delete(EmployeeEmergencyContact $contact): bool;

    // Find by specific criteria
    public function findByEmployeeId(int $employeeId): Collection;
    public function findByEmployeeIdDTO(int $employeeId): Collection;
    public function findByRelationship(string $relationship): Collection;
    public function findByRelationshipDTO(string $relationship): Collection;
    public function findByContactName(string $contactName): Collection;
    public function findByContactNameDTO(string $contactName): Collection;
    public function findByPhone(string $phone): Collection;
    public function findByPhoneDTO(string $phone): Collection;
    public function findByEmail(string $email): Collection;
    public function findByEmailDTO(string $email): Collection;
    public function findByEmployeeAndRelationship(int $employeeId, string $relationship): Collection;
    public function findByEmployeeAndRelationshipDTO(int $employeeId, string $relationship): Collection;

    // Status-based queries
    public function findPrimary(): Collection;
    public function findPrimaryDTO(): Collection;
    public function findActive(): Collection;
    public function findActiveDTO(): Collection;
    public function findInactive(): Collection;
    public function findInactiveDTO(): Collection;

    // Location-based queries
    public function findByCity(string $city): Collection;
    public function findByCityDTO(string $city): Collection;
    public function findByState(string $state): Collection;
    public function findByStateDTO(string $state): Collection;
    public function findByCountry(string $country): Collection;
    public function findByCountryDTO(string $country): Collection;

    // Status management
    public function activate(EmployeeEmergencyContact $contact): bool;
    public function deactivate(EmployeeEmergencyContact $contact): bool;
    public function setAsPrimary(EmployeeEmergencyContact $contact): bool;
    public function removePrimary(EmployeeEmergencyContact $contact): bool;

    // Count and statistics
    public function getEmployeeContactCount(int $employeeId): int;
    public function getEmployeeContactCountByRelationship(int $employeeId, string $relationship): int;
    public function getEmployeePrimaryContact(int $employeeId): ?EmployeeEmergencyContact;
    public function getEmployeePrimaryContactDTO(int $employeeId): ?EmployeeEmergencyContactDTO;
    public function getEmployeeActiveContacts(int $employeeId): Collection;
    public function getEmployeeActiveContactsDTO(int $employeeId): Collection;
    public function getTotalContactCount(): int;
    public function getTotalContactCountByRelationship(string $relationship): int;
    public function getTotalPrimaryContacts(): int;
    public function getTotalActiveContacts(): int;

    // Search functionality
    public function searchContacts(string $query): Collection;
    public function searchContactsDTO(string $query): Collection;
    public function searchContactsByEmployee(int $employeeId, string $query): Collection;
    public function searchContactsByEmployeeDTO(int $employeeId, string $query): Collection;

    // Import/Export
    public function exportContactData(array $filters = []): string;
    public function importContactData(string $data): bool;

    // Statistics and analytics
    public function getContactStatistics(): array;
    public function getEmployeeContactStatistics(int $employeeId): array;
    public function getContactDistribution(): array;
    public function validateContactInformation(int $contactId): array;
}
