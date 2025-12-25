<?php

namespace Fereydooni\Shopping\database\seeders;

use Fereydooni\Shopping\app\Enums\Relationship;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Models\EmployeeEmergencyContact;
use Fereydooni\Shopping\database\factories\EmployeeEmergencyContactFactory;
use Illuminate\Database\Seeder;

class EmployeeEmergencyContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Employee Emergency Contacts...');

        // Get existing employees or create some if none exist
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Creating sample employees first...');
            // You might want to run EmployeeSeeder here if it exists
            $employees = Employee::factory(10)->create();
        }

        $this->seedEmergencyContacts($employees);

        $this->command->info('Employee Emergency Contacts seeded successfully!');
    }

    /**
     * Seed emergency contacts for the given employees.
     */
    protected function seedEmergencyContacts($employees): void
    {
        $relationships = Relationship::cases();
        $relationshipWeights = [
            Relationship::SPOUSE => 0.3,      // 30% chance
            Relationship::PARENT => 0.25,     // 25% chance
            Relationship::CHILD => 0.2,       // 20% chance
            Relationship::SIBLING => 0.15,    // 15% chance
            Relationship::FRIEND => 0.08,     // 8% chance
            Relationship::OTHER => 0.02,      // 2% chance
        ];

        foreach ($employees as $employee) {
            $this->seedEmployeeEmergencyContacts($employee, $relationships, $relationshipWeights);
        }
    }

    /**
     * Seed emergency contacts for a specific employee.
     */
    protected function seedEmployeeEmergencyContacts($employee, $relationships, $relationshipWeights): void
    {
        // Determine how many contacts this employee should have
        $contactCount = $this->getRandomContactCount();

        // Ensure at least one primary contact
        $hasPrimary = false;

        for ($i = 0; $i < $contactCount; $i++) {
            $relationship = $this->getRandomRelationship($relationships, $relationshipWeights);
            $isPrimary = ! $hasPrimary && ($i === 0 || $this->faker->boolean(20));

            if ($isPrimary) {
                $hasPrimary = true;
            }

            $contact = $this->createEmergencyContact($employee, $relationship, $isPrimary);

            $this->command->info("Created emergency contact: {$contact->contact_name} ({$contact->relationship->label()}) for employee {$employee->first_name} {$employee->last_name}");
        }

        // If no primary contact was created, make the first one primary
        if (! $hasPrimary && $contactCount > 0) {
            $firstContact = EmployeeEmergencyContact::where('employee_id', $employee->id)->first();
            if ($firstContact) {
                $firstContact->setAsPrimary();
                $this->command->info("Set {$firstContact->contact_name} as primary contact for employee {$employee->first_name} {$employee->last_name}");
            }
        }
    }

    /**
     * Create an emergency contact with realistic data.
     */
    protected function createEmergencyContact($employee, $relationship, $isPrimary): EmployeeEmergencyContact
    {
        $factory = EmployeeEmergencyContactFactory::new();

        $contact = $factory
            ->forEmployee($employee)
            ->withRelationship($relationship)
            ->realistic()
            ->create([
                'is_primary' => $isPrimary,
                'is_active' => $this->faker->boolean(90), // 90% chance of being active
            ]);

        return $contact;
    }

    /**
     * Get a random number of contacts for an employee.
     */
    protected function getRandomContactCount(): int
    {
        $weights = [
            1 => 0.4,  // 40% chance of 1 contact
            2 => 0.35, // 35% chance of 2 contacts
            3 => 0.2,  // 20% chance of 3 contacts
            4 => 0.05, // 5% chance of 4 contacts
        ];

        $random = $this->faker->randomFloat(2, 0, 1);
        $cumulative = 0;

        foreach ($weights as $count => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $count;
            }
        }

        return 1; // Default fallback
    }

    /**
     * Get a random relationship based on weights.
     */
    protected function getRandomRelationship($relationships, $weights): Relationship
    {
        $random = $this->faker->randomFloat(2, 0, 1);
        $cumulative = 0;

        foreach ($weights as $relationship => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $relationship;
            }
        }

        return $relationships[0]; // Default fallback
    }

    /**
     * Create sample emergency contacts with specific scenarios.
     */
    public function createSampleScenarios(): void
    {
        $this->command->info('Creating sample emergency contact scenarios...');

        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found for sample scenarios.');

            return;
        }

        // Scenario 1: Employee with only spouse contact
        $employee1 = $employees->first();
        $this->createSpouseOnlyScenario($employee1);

        // Scenario 2: Employee with family contacts (spouse + children)
        if ($employees->count() > 1) {
            $employee2 = $employees->get(1);
            $this->createFamilyScenario($employee2);
        }

        // Scenario 3: Employee with multiple relationship types
        if ($employees->count() > 2) {
            $employee3 = $employees->get(2);
            $this->createMultipleRelationshipsScenario($employee3);
        }

        // Scenario 4: Employee with inactive contacts
        if ($employees->count() > 3) {
            $employee4 = $employees->get(3);
            $this->createInactiveContactsScenario($employee4);
        }

        $this->command->info('Sample scenarios created successfully!');
    }

    /**
     * Create scenario: Employee with only spouse contact.
     */
    protected function createSpouseOnlyScenario($employee): void
    {
        EmployeeEmergencyContact::where('employee_id', $employee->id)->delete();

        EmployeeEmergencyContactFactory::new()
            ->forEmployee($employee)
            ->spouse()
            ->primary()
            ->active()
            ->withCompleteContactInfo()
            ->create();

        $this->command->info("Created spouse-only scenario for employee {$employee->first_name} {$employee->last_name}");
    }

    /**
     * Create scenario: Employee with family contacts.
     */
    protected function createFamilyScenario($employee): void
    {
        EmployeeEmergencyContact::where('employee_id', $employee->id)->delete();

        // Spouse (primary)
        EmployeeEmergencyContactFactory::new()
            ->forEmployee($employee)
            ->spouse()
            ->primary()
            ->active()
            ->withCompleteContactInfo()
            ->create();

        // Child 1
        EmployeeEmergencyContactFactory::new()
            ->forEmployee($employee)
            ->child()
            ->active()
            ->withCompleteContactInfo()
            ->create();

        // Child 2
        EmployeeEmergencyContactFactory::new()
            ->forEmployee($employee)
            ->child()
            ->active()
            ->withCompleteContactInfo()
            ->create();

        $this->command->info("Created family scenario for employee {$employee->first_name} {$employee->last_name}");
    }

    /**
     * Create scenario: Employee with multiple relationship types.
     */
    protected function createMultipleRelationshipsScenario($employee): void
    {
        EmployeeEmergencyContact::where('employee_id', $employee->id)->delete();

        // Spouse (primary)
        EmployeeEmergencyContactFactory::new()
            ->forEmployee($employee)
            ->spouse()
            ->primary()
            ->active()
            ->withCompleteContactInfo()
            ->create();

        // Parent
        EmployeeEmergencyContactFactory::new()
            ->forEmployee($employee)
            ->parent()
            ->active()
            ->withCompleteContactInfo()
            ->create();

        // Sibling
        EmployeeEmergencyContactFactory::new()
            ->forEmployee($employee)
            ->sibling()
            ->active()
            ->withCompleteContactInfo()
            ->create();

        // Friend
        EmployeeEmergencyContactFactory::new()
            ->forEmployee($employee)
            ->friend()
            ->active()
            ->withCompleteContactInfo()
            ->create();

        $this->command->info("Created multiple relationships scenario for employee {$employee->first_name} {$employee->last_name}");
    }

    /**
     * Create scenario: Employee with inactive contacts.
     */
    protected function createInactiveContactsScenario($employee): void
    {
        EmployeeEmergencyContact::where('employee_id', $employee->id)->delete();

        // Active primary contact
        EmployeeEmergencyContactFactory::new()
            ->forEmployee($employee)
            ->spouse()
            ->primary()
            ->active()
            ->withCompleteContactInfo()
            ->create();

        // Inactive contact
        EmployeeEmergencyContactFactory::new()
            ->forEmployee($employee)
            ->parent()
            ->inactive()
            ->withCompleteContactInfo()
            ->create();

        // Another inactive contact
        EmployeeEmergencyContactFactory::new()
            ->forEmployee($employee)
            ->friend()
            ->inactive()
            ->withCompleteContactInfo()
            ->create();

        $this->command->info("Created inactive contacts scenario for employee {$employee->first_name} {$employee->last_name}");
    }

    /**
     * Get the faker instance.
     */
    protected function getFaker()
    {
        return \Faker\Factory::create();
    }
}
