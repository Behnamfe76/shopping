<?php

namespace App\Listeners\EmployeeEmergencyContact;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ValidateContactInformation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        try {
            $contact = $event->contact;
            $validationResult = $this->validateContact($contact);

            if (! $validationResult['is_valid']) {
                Log::warning('Emergency contact validation failed', [
                    'contact_id' => $contact->id,
                    'employee_id' => $contact->employee_id,
                    'errors' => $validationResult['errors'],
                ]);

                // Could dispatch a validation failed event here
                // event(new EmergencyContactValidationFailed($contact, $validationResult['errors']));
            } else {
                Log::info('Emergency contact validation passed', [
                    'contact_id' => $contact->id,
                    'employee_id' => $contact->employee_id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to validate emergency contact information', [
                'event' => get_class($event),
                'contact_id' => $event->contact->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Validate contact information.
     */
    private function validateContact($contact): array
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
        ];

        $data = $contact->toArray();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return [
                'is_valid' => false,
                'errors' => $validator->errors()->toArray(),
            ];
        }

        // Additional business logic validation
        $businessValidation = $this->validateBusinessRules($contact);
        if (! $businessValidation['is_valid']) {
            return $businessValidation;
        }

        return [
            'is_valid' => true,
            'errors' => [],
        ];
    }

    /**
     * Validate business rules.
     */
    private function validateBusinessRules($contact): array
    {
        $errors = [];

        // Check if primary contact already exists for this employee
        if ($contact->is_primary) {
            $existingPrimary = $contact->employee->emergencyContacts()
                ->where('is_primary', true)
                ->where('id', '!=', $contact->id)
                ->exists();

            if ($existingPrimary) {
                $errors['is_primary'] = ['Only one primary contact allowed per employee.'];
            }
        }

        // Validate phone number format (basic validation)
        if (! empty($contact->phone_primary) && ! $this->isValidPhoneNumber($contact->phone_primary)) {
            $errors['phone_primary'] = ['Invalid phone number format.'];
        }

        if (! empty($contact->phone_secondary) && ! $this->isValidPhoneNumber($contact->phone_secondary)) {
            $errors['phone_secondary'] = ['Invalid secondary phone number format.'];
        }

        // Validate email domain if provided
        if (! empty($contact->email) && ! $this->isValidEmailDomain($contact->email)) {
            $errors['email'] = ['Email domain appears to be invalid.'];
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
        // Basic phone validation - can be enhanced with more sophisticated regex
        $phone = preg_replace('/[^0-9+\-\(\)\s]/', '', $phone);

        return strlen($phone) >= 10 && strlen($phone) <= 20;
    }

    /**
     * Validate email domain.
     */
    private function isValidEmailDomain(string $email): bool
    {
        $domain = substr(strrchr($email, '@'), 1);

        return ! empty($domain) && strlen($domain) > 1;
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Validate contact information job failed', [
            'event' => get_class($event),
            'contact_id' => $event->contact->id ?? null,
            'error' => $exception->getMessage(),
        ]);
    }
}
