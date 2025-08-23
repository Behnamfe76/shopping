<?php

namespace App\Traits;

use App\Models\EmployeeEmergencyContact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HasEmployeeEmergencyContactStatusManagement
{
    /**
     * Set a contact as primary emergency contact.
     */
    public function setAsPrimaryEmergencyContact(EmployeeEmergencyContact $contact): bool
    {
        try {
            DB::beginTransaction();

            // Remove primary status from other contacts for this employee
            $this->removePrimaryStatusFromOtherContacts($contact->employee_id);

            // Set this contact as primary
            $contact->update(['is_primary' => true]);

            // Fire event
            event(new \App\Events\EmployeeEmergencyContact\EmployeeEmergencyContactSetPrimary($contact));

            DB::commit();

            Log::info('Emergency contact set as primary', [
                'contact_id' => $contact->id,
                'employee_id' => $contact->employee_id,
                'contact_name' => $contact->contact_name,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to set emergency contact as primary', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Remove primary status from a contact.
     */
    public function removePrimaryStatusFromContact(EmployeeEmergencyContact $contact): bool
    {
        try {
            $contact->update(['is_primary' => false]);

            Log::info('Primary status removed from emergency contact', [
                'contact_id' => $contact->id,
                'employee_id' => $contact->employee_id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to remove primary status from emergency contact', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Remove primary status from all other contacts for an employee.
     */
    private function removePrimaryStatusFromOtherContacts(int $employeeId): void
    {
        EmployeeEmergencyContact::where('employee_id', $employeeId)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);
    }

    /**
     * Activate an emergency contact.
     */
    public function activateEmergencyContact(EmployeeEmergencyContact $contact): bool
    {
        try {
            $contact->update(['is_active' => true]);

            Log::info('Emergency contact activated', [
                'contact_id' => $contact->id,
                'employee_id' => $contact->employee_id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to activate emergency contact', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Deactivate an emergency contact.
     */
    public function deactivateEmergencyContact(EmployeeEmergencyContact $contact): bool
    {
        try {
            // If this is the primary contact, remove primary status first
            if ($contact->is_primary) {
                $this->removePrimaryStatusFromContact($contact);
            }

            $contact->update(['is_active' => false]);

            Log::info('Emergency contact deactivated', [
                'contact_id' => $contact->id,
                'employee_id' => $contact->employee_id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to deactivate emergency contact', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Toggle emergency contact status.
     */
    public function toggleEmergencyContactStatus(EmployeeEmergencyContact $contact): bool
    {
        if ($contact->is_active) {
            return $this->deactivateEmergencyContact($contact);
        } else {
            return $this->activateEmergencyContact($contact);
        }
    }

    /**
     * Get the primary emergency contact for an employee.
     */
    public function getPrimaryEmergencyContact(int $employeeId): ?EmployeeEmergencyContact
    {
        return EmployeeEmergencyContact::where('employee_id', $employeeId)
            ->where('is_primary', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if an employee has a primary emergency contact.
     */
    public function hasPrimaryEmergencyContact(int $employeeId): bool
    {
        return $this->getPrimaryEmergencyContact($employeeId) !== null;
    }

    /**
     * Get all active emergency contacts for an employee.
     */
    public function getActiveEmergencyContacts(int $employeeId): \Illuminate\Database\Eloquent\Collection
    {
        return EmployeeEmergencyContact::where('employee_id', $employeeId)
            ->where('is_active', true)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get all inactive emergency contacts for an employee.
     */
    public function getInactiveEmergencyContacts(int $employeeId): \Illuminate\Database\Eloquent\Collection
    {
        return EmployeeEmergencyContact::where('employee_id', $employeeId)
            ->where('is_active', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get emergency contact count by status for an employee.
     */
    public function getEmergencyContactCountByStatus(int $employeeId): array
    {
        return [
            'total' => EmployeeEmergencyContact::where('employee_id', $employeeId)->count(),
            'active' => EmployeeEmergencyContact::where('employee_id', $employeeId)->where('is_active', true)->count(),
            'inactive' => EmployeeEmergencyContact::where('employee_id', $employeeId)->where('is_active', false)->count(),
            'primary' => EmployeeEmergencyContact::where('employee_id', $employeeId)->where('is_primary', true)->count(),
        ];
    }

    /**
     * Validate emergency contact status consistency.
     */
    public function validateEmergencyContactStatus(int $employeeId): array
    {
        $issues = [];
        $contacts = EmployeeEmergencyContact::where('employee_id', $employeeId)->get();

        // Check for multiple primary contacts
        $primaryContacts = $contacts->where('is_primary', true);
        if ($primaryContacts->count() > 1) {
            $issues[] = 'Multiple primary contacts found';
        }

        // Check for inactive primary contact
        $inactivePrimary = $contacts->where('is_primary', true)->where('is_active', false);
        if ($inactivePrimary->count() > 0) {
            $issues[] = 'Primary contact is inactive';
        }

        // Check if no primary contact exists
        if ($primaryContacts->count() === 0) {
            $issues[] = 'No primary contact designated';
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues,
            'contact_count' => $contacts->count(),
            'primary_count' => $primaryContacts->count(),
            'active_count' => $contacts->where('is_active', true)->count(),
        ];
    }
}
