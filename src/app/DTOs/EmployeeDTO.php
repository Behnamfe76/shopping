<?php

namespace Fereydooni\Shopping\app\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Illuminate\Support\Carbon;
use Fereydooni\Shopping\app\Enums\EmployeeStatus;
use Fereydooni\Shopping\app\Enums\EmploymentType;
use Fereydooni\Shopping\app\Enums\Gender;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Models\User;

class EmployeeDTO extends Data
{
    public function __construct(
        #[Nullable]
        public ?int $id,

        #[Required, IntegerType]
        public int $user_id,

        #[Required, StringType, Max(50), Unique('employees', 'employee_number')]
        public string $employee_number,

        #[Required, StringType, Max(100)]
        public string $first_name,

        #[Required, StringType, Max(100)]
        public string $last_name,

        #[Required, Email, Max(255), Unique('employees', 'email')]
        public string $email,

        #[Nullable, StringType, Max(20), Regex('/^\+?[1-9]\d{1,14}$/')]
        public ?string $phone,

        #[Nullable, Date]
        public ?Carbon $date_of_birth,

        #[Nullable]
        public ?Gender $gender,

        #[Required, Date]
        public Carbon $hire_date,

        #[Nullable, Date]
        public ?Carbon $termination_date,

        #[Required, StringType, Max(100)]
        public string $position,

        #[Required, StringType, Max(100)]
        public string $department,

        #[Nullable, IntegerType]
        public ?int $manager_id,

        #[Nullable, Numeric, Min(0)]
        public ?float $salary,

        #[Nullable, Numeric, Min(0)]
        public ?float $hourly_rate,

        #[Required]
        public EmploymentType $employment_type,

        #[Required]
        public EmployeeStatus $status,

        #[Nullable, StringType, Max(100)]
        public ?string $emergency_contact_name,

        #[Nullable, StringType, Max(20), Regex('/^\+?[1-9]\d{1,14}$/')]
        public ?string $emergency_contact_phone,

        #[Nullable, StringType, Max(50)]
        public ?string $emergency_contact_relationship,

        #[Nullable, StringType, Max(255)]
        public ?string $address,

        #[Nullable, StringType, Max(100)]
        public ?string $city,

        #[Nullable, StringType, Max(100)]
        public ?string $state,

        #[Nullable, StringType, Max(20)]
        public ?string $postal_code,

        #[Nullable, StringType, Max(100)]
        public ?string $country,

        #[Nullable, StringType, Max(50)]
        public ?string $tax_id,

        #[Nullable, StringType, Max(20)]
        public ?string $social_security_number,

        #[Nullable, StringType, Max(50)]
        public ?string $bank_account_number,

        #[Nullable, StringType, Max(20)]
        public ?string $bank_routing_number,

        #[BooleanType]
        public bool $benefits_enrolled,

        #[IntegerType, Min(0)]
        public int $vacation_days_used,

        #[IntegerType, Min(0)]
        public int $vacation_days_total,

        #[IntegerType, Min(0)]
        public int $sick_days_used,

        #[IntegerType, Min(0)]
        public int $sick_days_total,

        #[Nullable, Numeric, Min(0), Max(5)]
        public ?float $performance_rating,

        #[Nullable, Date]
        public ?Carbon $last_review_date,

        #[Nullable, Date]
        public ?Carbon $next_review_date,

        #[Nullable]
        public ?array $training_completed,

        #[Nullable]
        public ?array $certifications,

        #[Nullable]
        public ?array $skills,

        #[Nullable, StringType, Max(1000)]
        public ?string $notes,

        #[Nullable]
        public ?Carbon $created_at,

        #[Nullable]
        public ?Carbon $updated_at,

        // Relationships
        #[Nullable]
        public ?User $user,

        #[Nullable]
        public ?Employee $manager,

        #[Nullable]
        public ?array $subordinates,
    ) {
    }

    public static function fromModel(Employee $employee): self
    {
        return new self(
            id: $employee->id,
            user_id: $employee->user_id,
            employee_number: $employee->employee_number,
            first_name: $employee->first_name,
            last_name: $employee->last_name,
            email: $employee->email,
            phone: $employee->phone,
            date_of_birth: $employee->date_of_birth,
            gender: $employee->gender,
            hire_date: $employee->hire_date,
            termination_date: $employee->termination_date,
            position: $employee->position,
            department: $employee->department,
            manager_id: $employee->manager_id,
            salary: $employee->salary,
            hourly_rate: $employee->hourly_rate,
            employment_type: $employee->employment_type,
            status: $employee->status,
            emergency_contact_name: $employee->emergency_contact_name,
            emergency_contact_phone: $employee->emergency_contact_phone,
            emergency_contact_relationship: $employee->emergency_contact_relationship,
            address: $employee->address,
            city: $employee->city,
            state: $employee->state,
            postal_code: $employee->postal_code,
            country: $employee->country,
            tax_id: $employee->tax_id,
            social_security_number: $employee->social_security_number,
            bank_account_number: $employee->bank_account_number,
            bank_routing_number: $employee->bank_routing_number,
            benefits_enrolled: $employee->benefits_enrolled,
            vacation_days_used: $employee->vacation_days_used,
            vacation_days_total: $employee->vacation_days_total,
            sick_days_used: $employee->sick_days_used,
            sick_days_total: $employee->sick_days_total,
            performance_rating: $employee->performance_rating,
            last_review_date: $employee->last_review_date,
            next_review_date: $employee->next_review_date,
            training_completed: $employee->training_completed,
            certifications: $employee->certifications,
            skills: $employee->skills,
            notes: $employee->notes,
            created_at: $employee->created_at,
            updated_at: $employee->updated_at,
            user: $employee->user,
            manager: $employee->manager,
            subordinates: $employee->subordinates?->map(fn($sub) => self::fromModel($sub))->toArray(),
        );
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getRemainingVacationDaysAttribute(): int
    {
        return $this->vacation_days_total - $this->vacation_days_used;
    }

    public function getRemainingSickDaysAttribute(): int
    {
        return $this->sick_days_total - $this->sick_days_used;
    }

    public function getYearsOfServiceAttribute(): int
    {
        return $this->hire_date->diffInYears(now());
    }

    public function isActive(): bool
    {
        return $this->status === EmployeeStatus::ACTIVE;
    }

    public function isTerminated(): bool
    {
        return $this->status === EmployeeStatus::TERMINATED;
    }

    public function isOnLeave(): bool
    {
        return $this->status === EmployeeStatus::ON_LEAVE;
    }

    public function hasManager(): bool
    {
        return !is_null($this->manager_id);
    }

    public function isManager(): bool
    {
        return !empty($this->subordinates);
    }
}

