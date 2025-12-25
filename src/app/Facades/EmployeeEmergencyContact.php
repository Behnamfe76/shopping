<?php

namespace Fereydooni\Shopping\app\Facades;

use Fereydooni\Shopping\app\DTOs\EmployeeEmergencyContactDTO;
use Fereydooni\Shopping\app\Models\EmployeeEmergencyContact;
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeEmergencyContactRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection all()
 * @method static LengthAwarePaginator paginate(int $perPage = 15)
 * @method static Collection find(int $id)
 * @method static EmployeeEmergencyContactDTO findDTO(int $id)
 * @method static EmployeeEmergencyContact create(array $data)
 * @method static EmployeeEmergencyContactDTO createAndReturnDTO(array $data)
 * @method static bool update(EmployeeEmergencyContact $contact, array $data)
 * @method static EmployeeEmergencyContactDTO|null updateAndReturnDTO(EmployeeEmergencyContact $contact, array $data)
 * @method static bool delete(EmployeeEmergencyContact $contact)
 * @method static Collection findByEmployeeId(int $employeeId)
 * @method static Collection findByEmployeeIdDTO(int $employeeId)
 * @method static Collection findByRelationship(string $relationship)
 * @method static Collection findByRelationshipDTO(string $relationship)
 * @method static Collection searchContacts(string $query)
 * @method static Collection searchContactsDTO(string $query)
 * @method static int getEmployeeContactCount(int $employeeId)
 * @method static array getContactStatistics()
 * @method static array getEmployeeContactStatistics(int $employeeId)
 * @method static string exportContactData(array $filters = [])
 * @method static bool importContactData(string $data)
 * @method static array validateContactInformation(int $contactId)
 * @method static bool activate(EmployeeEmergencyContact $contact)
 * @method static bool deactivate(EmployeeEmergencyContact $contact)
 * @method static bool setAsPrimary(EmployeeEmergencyContact $contact)
 * @method static bool removePrimary(EmployeeEmergencyContact $contact)
 * @method static EmployeeEmergencyContact|null getEmployeePrimaryContact(int $employeeId)
 * @method static EmployeeEmergencyContactDTO|null getEmployeePrimaryContactDTO(int $employeeId)
 * @method static Collection getEmployeeActiveContacts(int $employeeId)
 * @method static Collection getEmployeeActiveContactsDTO(int $employeeId)
 * @method static int getTotalContactCount()
 * @method static int getTotalPrimaryContacts()
 * @method static int getTotalActiveContacts()
 * @method static Collection findPrimary()
 * @method static Collection findPrimaryDTO()
 * @method static Collection findActive()
 * @method static Collection findActiveDTO()
 * @method static Collection findInactive()
 * @method static Collection findInactiveDTO()
 * @method static Collection findByContactName(string $contactName)
 * @method static Collection findByContactNameDTO(string $contactName)
 * @method static Collection findByPhone(string $phone)
 * @method static Collection findByPhoneDTO(string $phone)
 * @method static Collection findByEmail(string $email)
 * @method static Collection findByEmailDTO(string $email)
 * @method static Collection findByCity(string $city)
 * @method static Collection findByCityDTO(string $city)
 * @method static Collection findByState(string $state)
 * @method static Collection findByStateDTO(string $state)
 * @method static Collection findByCountry(string $country)
 * @method static Collection findByCountryDTO(string $country)
 * @method static Collection findByEmployeeAndRelationship(int $employeeId, string $relationship)
 * @method static Collection findByEmployeeAndRelationshipDTO(int $employeeId, string $relationship)
 * @method static int getEmployeeContactCountByRelationship(int $employeeId, string $relationship)
 * @method static int getTotalContactCountByRelationship(string $relationship)
 * @method static Collection searchContactsByEmployee(int $employeeId, string $query)
 * @method static Collection searchContactsByEmployeeDTO(int $employeeId, string $query)
 * @method static array getContactDistribution()
 */
class EmployeeEmergencyContactFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return EmployeeEmergencyContactRepositoryInterface::class;
    }

    /**
     * Get all emergency contacts with error handling.
     */
    public static function getAll(): Collection
    {
        try {
            return static::all();
        } catch (\Exception $e) {
            static::logError('Failed to get all emergency contacts', $e);

            return collect();
        }
    }

    /**
     * Get all emergency contacts as DTOs with error handling.
     */
    public static function getAllDTO(): Collection
    {
        try {
            $contacts = static::all();

            return $contacts->map(function ($contact) {
                return EmployeeEmergencyContactDTO::fromModel($contact);
            });
        } catch (\Exception $e) {
            static::logError('Failed to get all emergency contacts as DTOs', $e);

            return collect();
        }
    }

    /**
     * Find emergency contact by ID with error handling.
     */
    public static function findSafe(int $id): ?EmployeeEmergencyContact
    {
        try {
            return static::find($id);
        } catch (\Exception $e) {
            static::logError("Failed to find emergency contact with ID: {$id}", $e);

            return null;
        }
    }

    /**
     * Find emergency contact by ID and return as DTO with error handling.
     */
    public static function findSafeDTO(int $id): ?EmployeeEmergencyContactDTO
    {
        try {
            return static::findDTO($id);
        } catch (\Exception $e) {
            static::logError("Failed to find emergency contact DTO with ID: {$id}", $e);

            return null;
        }
    }

    /**
     * Create emergency contact with error handling and validation.
     */
    public static function createSafe(array $data): ?EmployeeEmergencyContact
    {
        try {
            // Basic validation
            if (empty($data['employee_id']) || empty($data['contact_name']) || empty($data['phone_primary'])) {
                static::logWarning('Invalid data provided for emergency contact creation', $data);

                return null;
            }

            $contact = static::create($data);
            static::logInfo('Emergency contact created successfully', ['id' => $contact->id]);

            return $contact;
        } catch (\Exception $e) {
            static::logError('Failed to create emergency contact', $e);

            return null;
        }
    }

    /**
     * Create emergency contact and return as DTO with error handling.
     */
    public static function createSafeDTO(array $data): ?EmployeeEmergencyContactDTO
    {
        try {
            $contact = static::createSafe($data);

            return $contact ? EmployeeEmergencyContactDTO::fromModel($contact) : null;
        } catch (\Exception $e) {
            static::logError('Failed to create emergency contact DTO', $e);

            return null;
        }
    }

    /**
     * Update emergency contact with error handling.
     */
    public static function updateSafe(EmployeeEmergencyContact $contact, array $data): bool
    {
        try {
            $result = static::update($contact, $data);
            if ($result) {
                static::logInfo('Emergency contact updated successfully', ['id' => $contact->id]);
            }

            return $result;
        } catch (\Exception $e) {
            static::logError('Failed to update emergency contact', $e);

            return false;
        }
    }

    /**
     * Delete emergency contact with error handling.
     */
    public static function deleteSafe(EmployeeEmergencyContact $contact): bool
    {
        try {
            $result = static::delete($contact);
            if ($result) {
                static::logInfo('Emergency contact deleted successfully', ['id' => $contact->id]);
            }

            return $result;
        } catch (\Exception $e) {
            static::logError('Failed to delete emergency contact', $e);

            return false;
        }
    }

    /**
     * Get emergency contacts by employee ID with error handling.
     */
    public static function getByEmployeeSafe(int $employeeId): Collection
    {
        try {
            return static::findByEmployeeId($employeeId);
        } catch (\Exception $e) {
            static::logError("Failed to get emergency contacts for employee ID: {$employeeId}", $e);

            return collect();
        }
    }

    /**
     * Get emergency contacts by employee ID as DTOs with error handling.
     */
    public static function getByEmployeeSafeDTO(int $employeeId): Collection
    {
        try {
            return static::findByEmployeeIdDTO($employeeId);
        } catch (\Exception $e) {
            static::logError("Failed to get emergency contact DTOs for employee ID: {$employeeId}", $e);

            return collect();
        }
    }

    /**
     * Search emergency contacts with error handling.
     */
    public static function searchSafe(string $query): Collection
    {
        try {
            if (empty(trim($query))) {
                return collect();
            }

            return static::searchContacts($query);
        } catch (\Exception $e) {
            static::logError("Failed to search emergency contacts with query: {$query}", $e);

            return collect();
        }
    }

    /**
     * Search emergency contacts and return as DTOs with error handling.
     */
    public static function searchSafeDTO(string $query): Collection
    {
        try {
            $contacts = static::searchSafe($query);

            return $contacts->map(function ($contact) {
                return EmployeeEmergencyContactDTO::fromModel($contact);
            });
        } catch (\Exception $e) {
            static::logError("Failed to search emergency contact DTOs with query: {$query}", $e);

            return collect();
        }
    }

    /**
     * Get emergency contact count for employee with error handling.
     */
    public static function getCountForEmployeeSafe(int $employeeId): int
    {
        try {
            return static::getEmployeeContactCount($employeeId);
        } catch (\Exception $e) {
            static::logError("Failed to get emergency contact count for employee ID: {$employeeId}", $e);

            return 0;
        }
    }

    /**
     * Get total emergency contact count with error handling.
     */
    public static function getTotalCountSafe(): int
    {
        try {
            return static::getTotalContactCount();
        } catch (\Exception $e) {
            static::logError('Failed to get total emergency contact count', $e);

            return 0;
        }
    }

    /**
     * Get emergency contact statistics with error handling.
     */
    public static function getStatisticsSafe(): array
    {
        try {
            return static::getContactStatistics();
        } catch (\Exception $e) {
            static::logError('Failed to get emergency contact statistics', $e);

            return [
                'total_contacts' => 0,
                'active_contacts' => 0,
                'primary_contacts' => 0,
                'contacts_by_relationship' => [],
                'contacts_by_country' => [],
                'contacts_by_state' => [],
            ];
        }
    }

    /**
     * Get employee emergency contact statistics with error handling.
     */
    public static function getEmployeeStatisticsSafe(int $employeeId): array
    {
        try {
            return static::getEmployeeContactStatistics($employeeId);
        } catch (\Exception $e) {
            static::logError("Failed to get emergency contact statistics for employee ID: {$employeeId}", $e);

            return [
                'total_contacts' => 0,
                'active_contacts' => 0,
                'primary_contacts' => 0,
                'contacts_by_relationship' => [],
                'has_primary' => false,
                'has_active' => false,
            ];
        }
    }

    /**
     * Export emergency contact data with error handling.
     */
    public static function exportSafe(array $filters = []): string
    {
        try {
            return static::exportContactData($filters);
        } catch (\Exception $e) {
            static::logError('Failed to export emergency contact data', $e);

            return '';
        }
    }

    /**
     * Import emergency contact data with error handling.
     */
    public static function importSafe(string $data): bool
    {
        try {
            if (empty(trim($data))) {
                static::logWarning('Empty data provided for emergency contact import');

                return false;
            }

            $result = static::importContactData($data);
            if ($result) {
                static::logInfo('Emergency contact data imported successfully');
            }

            return $result;
        } catch (\Exception $e) {
            static::logError('Failed to import emergency contact data', $e);

            return false;
        }
    }

    /**
     * Validate emergency contact information with error handling.
     */
    public static function validateSafe(int $contactId): array
    {
        try {
            return static::validateContactInformation($contactId);
        } catch (\Exception $e) {
            static::logError("Failed to validate emergency contact information for ID: {$contactId}", $e);

            return [
                'valid' => false,
                'errors' => ['Validation failed due to system error'],
                'contact_method' => 'unknown',
                'has_phone' => false,
                'has_email' => false,
                'has_address' => false,
            ];
        }
    }

    /**
     * Activate emergency contact with error handling.
     */
    public static function activateSafe(EmployeeEmergencyContact $contact): bool
    {
        try {
            $result = static::activate($contact);
            if ($result) {
                static::logInfo('Emergency contact activated successfully', ['id' => $contact->id]);
            }

            return $result;
        } catch (\Exception $e) {
            static::logError('Failed to activate emergency contact', $e);

            return false;
        }
    }

    /**
     * Deactivate emergency contact with error handling.
     */
    public static function deactivateSafe(EmployeeEmergencyContact $contact): bool
    {
        try {
            $result = static::deactivate($contact);
            if ($result) {
                static::logInfo('Emergency contact deactivated successfully', ['id' => $contact->id]);
            }

            return $result;
        } catch (\Exception $e) {
            static::logError('Failed to deactivate emergency contact', $e);

            return false;
        }
    }

    /**
     * Set emergency contact as primary with error handling.
     */
    public static function setAsPrimarySafe(EmployeeEmergencyContact $contact): bool
    {
        try {
            $result = static::setAsPrimary($contact);
            if ($result) {
                static::logInfo('Emergency contact set as primary successfully', ['id' => $contact->id]);
            }

            return $result;
        } catch (\Exception $e) {
            static::logError('Failed to set emergency contact as primary', $e);

            return false;
        }
    }

    /**
     * Remove primary status from emergency contact with error handling.
     */
    public static function removePrimarySafe(EmployeeEmergencyContact $contact): bool
    {
        try {
            $result = static::removePrimary($contact);
            if ($result) {
                static::logInfo('Primary status removed from emergency contact successfully', ['id' => $contact->id]);
            }

            return $result;
        } catch (\Exception $e) {
            static::logError('Failed to remove primary status from emergency contact', $e);

            return false;
        }
    }

    /**
     * Get employee primary contact with error handling.
     */
    public static function getPrimaryForEmployeeSafe(int $employeeId): ?EmployeeEmergencyContact
    {
        try {
            return static::getEmployeePrimaryContact($employeeId);
        } catch (\Exception $e) {
            static::logError("Failed to get primary emergency contact for employee ID: {$employeeId}", $e);

            return null;
        }
    }

    /**
     * Get employee active contacts with error handling.
     */
    public static function getActiveForEmployeeSafe(int $employeeId): Collection
    {
        try {
            return static::getEmployeeActiveContacts($employeeId);
        } catch (\Exception $e) {
            static::logError("Failed to get active emergency contacts for employee ID: {$employeeId}", $e);

            return collect();
        }
    }

    /**
     * Check if employee has emergency contacts with error handling.
     */
    public static function hasContactsSafe(int $employeeId): bool
    {
        try {
            return static::getEmployeeContactCount($employeeId) > 0;
        } catch (\Exception $e) {
            static::logError("Failed to check if employee has emergency contacts for ID: {$employeeId}", $e);

            return false;
        }
    }

    /**
     * Check if employee has primary emergency contact with error handling.
     */
    public static function hasPrimaryContactSafe(int $employeeId): bool
    {
        try {
            return static::getEmployeePrimaryContact($employeeId) !== null;
        } catch (\Exception $e) {
            static::logError("Failed to check if employee has primary emergency contact for ID: {$employeeId}", $e);

            return false;
        }
    }

    /**
     * Check if employee has active emergency contacts with error handling.
     */
    public static function hasActiveContactsSafe(int $employeeId): bool
    {
        try {
            $contacts = static::getEmployeeActiveContacts($employeeId);

            return $contacts->isNotEmpty();
        } catch (\Exception $e) {
            static::logError("Failed to check if employee has active emergency contacts for ID: {$employeeId}", $e);

            return false;
        }
    }

    /**
     * Log information message.
     */
    protected static function logInfo(string $message, array $context = []): void
    {
        if (function_exists('logger')) {
            logger()->info($message, $context);
        }
    }

    /**
     * Log warning message.
     */
    protected static function logWarning(string $message, array $context = []): void
    {
        if (function_exists('logger')) {
            logger()->warning($message, $context);
        }
    }

    /**
     * Log error message.
     */
    protected static function logError(string $message, ?\Throwable $exception = null, array $context = []): void
    {
        if (function_exists('logger')) {
            $context['exception'] = $exception ? $exception->getMessage() : null;
            $context['trace'] = $exception ? $exception->getTraceAsString() : null;
            logger()->error($message, $context);
        }
    }
}
