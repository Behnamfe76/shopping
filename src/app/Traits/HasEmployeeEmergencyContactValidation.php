<?php

namespace App\Traits;

use App\Models\EmployeeEmergencyContact;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

trait HasEmployeeEmergencyContactValidation
{
    /**
     * Validate emergency contact data.
     */
    public function validateEmergencyContactData(array $data): array
    {
        $rules = [
            'contact_name' => 'required|string|max:255',
            'relationship' => 'required|string|in:spouse,parent,child,sibling,friend,other',
            'phone_primary' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ];

        $messages = [
            'contact_name.required' => 'Contact name is required.',
            'contact_name.max' => 'Contact name cannot exceed 255 characters.',
            'relationship.required' => 'Relationship is required.',
            'relationship.in' => 'Invalid relationship type.',
            'phone_primary.required' => 'Primary phone number is required.',
            'phone_primary.max' => 'Primary phone number cannot exceed 20 characters.',
            'phone_secondary.max' => 'Secondary phone number cannot exceed 20 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'city.max' => 'City cannot exceed 100 characters.',
            'state.max' => 'State cannot exceed 100 characters.',
            'postal_code.max' => 'Postal code cannot exceed 20 characters.',
            'country.max' => 'Country cannot exceed 100 characters.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return [
                'is_valid' => false,
                'errors' => $validator->errors()->toArray(),
            ];
        }

        // Additional business logic validation
        $businessValidation = $this->validateBusinessRules($data);
        if (!$businessValidation['is_valid']) {
            return $businessValidation;
        }

        return [
            'is_valid' => true,
            'errors' => [],
        ];
    }

    /**
     * Validate business rules for emergency contacts.
     */
    private function validateBusinessRules(array $data): array
    {
        $errors = [];

        // Validate phone number format
        if (!empty($data['phone_primary']) && !$this->isValidPhoneNumber($data['phone_primary'])) {
            $errors['phone_primary'] = ['Invalid phone number format. Please use a valid phone number.'];
        }

        if (!empty($data['phone_secondary']) && !$this->isValidPhoneNumber($data['phone_secondary'])) {
            $errors['phone_secondary'] = ['Invalid secondary phone number format. Please use a valid phone number.'];
        }

        // Validate email domain if provided
        if (!empty($data['email']) && !$this->isValidEmailDomain($data['email'])) {
            $errors['email'] = ['Email domain appears to be invalid. Please check the email address.'];
        }

        // Validate postal code format if country is US
        if (!empty($data['country']) && strtolower($data['country']) === 'united states' && !empty($data['postal_code'])) {
            if (!$this->isValidUSPostalCode($data['postal_code'])) {
                $errors['postal_code'] = ['Invalid US postal code format. Please use format: 12345 or 12345-6789.'];
            }
        }

        // Validate that at least one contact method is provided
        if (empty($data['phone_primary']) && empty($data['email'])) {
            $errors['contact_method'] = ['At least one contact method (phone or email) is required.'];
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Validate phone number format.
     */
    private function isValidPhoneNumber(string $phone): bool
    {
        // Remove all non-digit characters except +, -, (, ), and space
        $cleaned = preg_replace('/[^0-9+\-\(\)\s]/', '', $phone);

        // Check if the cleaned phone number has reasonable length
        $digitCount = preg_match_all('/[0-9]/', $cleaned);

        return $digitCount >= 10 && $digitCount <= 15 && strlen($cleaned) <= 20;
    }

    /**
     * Validate email domain.
     */
    private function isValidEmailDomain(string $email): bool
    {
        $domain = substr(strrchr($email, '@'), 1);

        if (empty($domain) || strlen($domain) < 2) {
            return false;
        }

        // Check for common invalid domains
        $invalidDomains = ['test', 'example', 'invalid', 'fake', 'dummy'];
        foreach ($invalidDomains as $invalid) {
            if (stripos($domain, $invalid) === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate US postal code format.
     */
    private function isValidUSPostalCode(string $postalCode): bool
    {
        // US postal code format: 12345 or 12345-6789
        return preg_match('/^\d{5}(-\d{4})?$/', $postalCode);
    }

    /**
     * Validate emergency contact uniqueness for an employee.
     */
    public function validateEmergencyContactUniqueness(int $employeeId, array $data, ?int $excludeId = null): array
    {
        $errors = [];

        // Check for duplicate contact names (case-insensitive)
        $nameQuery = EmployeeEmergencyContact::where('employee_id', $employeeId)
            ->whereRaw('LOWER(contact_name) = ?', [strtolower($data['contact_name'])]);

        if ($excludeId) {
            $nameQuery->where('id', '!=', $excludeId);
        }

        if ($nameQuery->exists()) {
            $errors['contact_name'] = ['An emergency contact with this name already exists for this employee.'];
        }

        // Check for duplicate phone numbers
        if (!empty($data['phone_primary'])) {
            $phoneQuery = EmployeeEmergencyContact::where('employee_id', $employeeId)
                ->where('phone_primary', $data['phone_primary']);

            if ($excludeId) {
                $phoneQuery->where('id', '!=', $excludeId);
            }

            if ($phoneQuery->exists()) {
                $errors['phone_primary'] = ['This phone number is already registered with another emergency contact.'];
            }
        }

        // Check for duplicate email addresses
        if (!empty($data['email'])) {
            $emailQuery = EmployeeEmergencyContact::where('employee_id', $employeeId)
                ->where('email', $data['email']);

            if ($excludeId) {
                $emailQuery->where('id', '!=', $excludeId);
            }

            if ($emailQuery->exists()) {
                $errors['email'] = ['This email address is already registered with another emergency contact.'];
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Validate primary contact constraints.
     */
    public function validatePrimaryContactConstraints(int $employeeId, array $data, ?int $excludeId = null): array
    {
        $errors = [];

        if (!empty($data['is_primary']) && $data['is_primary']) {
            // Check if another primary contact already exists
            $existingPrimaryQuery = EmployeeEmergencyContact::where('employee_id', $employeeId)
                ->where('is_primary', true);

            if ($excludeId) {
                $existingPrimaryQuery->where('id', '!=', $excludeId);
            }

            if ($existingPrimaryQuery->exists()) {
                $errors['is_primary'] = ['Only one primary emergency contact is allowed per employee.'];
            }

            // Ensure the contact is active if setting as primary
            if (!empty($data['is_active']) && !$data['is_active']) {
                $errors['is_primary'] = ['Primary emergency contact must be active.'];
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Comprehensive validation for emergency contact.
     */
    public function validateEmergencyContactComprehensive(int $employeeId, array $data, ?int $excludeId = null): array
    {
        // Basic data validation
        $dataValidation = $this->validateEmergencyContactData($data);
        if (!$dataValidation['is_valid']) {
            return $dataValidation;
        }

        // Uniqueness validation
        $uniquenessValidation = $this->validateEmergencyContactUniqueness($employeeId, $data, $excludeId);
        if (!$uniquenessValidation['is_valid']) {
            return $uniquenessValidation;
        }

        // Primary contact constraints validation
        $primaryValidation = $this->validatePrimaryContactConstraints($employeeId, $data, $excludeId);
        if (!$primaryValidation['is_valid']) {
            return $primaryValidation;
        }

        return [
            'is_valid' => true,
            'errors' => [],
            'validation_passed' => [
                'data_validation' => true,
                'uniqueness_validation' => true,
                'primary_contact_validation' => true,
            ],
        ];
    }
}
