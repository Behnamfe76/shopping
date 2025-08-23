<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeTimeOff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EmployeeTimeOffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding EmployeeTimeOff data...');

        // Get existing employees and users
        $employees = Employee::with('user')->get();
        $users = User::where('role', 'manager')->orWhere('role', 'hr')->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Please run EmployeeSeeder first.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create sample time-off requests
        $this->createSampleTimeOffRequests($employees, $users);

        $this->command->info('EmployeeTimeOff seeding completed successfully!');
    }

    /**
     * Create sample time-off requests
     */
    private function createSampleTimeOffRequests($employees, $users): void
    {
        $timeOffTypes = ['vacation', 'sick', 'personal', 'bereavement', 'jury_duty', 'military', 'other'];
        $statuses = ['pending', 'approved', 'rejected', 'cancelled'];

        // Create time-off requests for each employee
        foreach ($employees as $employee) {
            $this->createEmployeeTimeOffRequests($employee, $users, $timeOffTypes, $statuses);
        }
    }

    /**
     * Create time-off requests for a specific employee
     */
    private function createEmployeeTimeOffRequests($employee, $users, $timeOffTypes, $statuses): void
    {
        $numRequests = rand(2, 8); // Random number of requests per employee

        for ($i = 0; $i < $numRequests; $i++) {
            $timeOffType = $timeOffTypes[array_rand($timeOffTypes)];
            $status = $statuses[array_rand($statuses)];

            $timeOff = $this->createTimeOffRequest($employee, $users, $timeOffType, $status);

            // Create additional related requests for some employees
            if ($i === 0 && rand(1, 3) === 1) {
                $this->createRelatedTimeOffRequests($employee, $users, $timeOff);
            }
        }
    }

    /**
     * Create a single time-off request
     */
    private function createTimeOffRequest($employee, $users, $timeOffType, $status): EmployeeTimeOff
    {
        $startDate = $this->generateStartDate($timeOffType);
        $endDate = $this->generateEndDate($startDate, $timeOffType);
        $isHalfDay = $this->shouldBeHalfDay($timeOffType);
        $totalDays = $this->calculateTotalDays($startDate, $endDate, $isHalfDay);

        $timeOffData = [
            'employee_id' => $employee->id,
            'user_id' => $employee->user_id,
            'time_off_type' => $timeOffType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $isHalfDay ? '09:00:00' : null,
            'end_time' => $isHalfDay ? '13:00:00' : null,
            'total_hours' => $isHalfDay ? 4 : ($totalDays * 8),
            'total_days' => $totalDays,
            'reason' => $this->generateReason($timeOffType),
            'description' => $this->generateDescription($timeOffType),
            'status' => $status,
            'is_half_day' => $isHalfDay,
            'is_urgent' => $this->shouldBeUrgent($timeOffType),
            'attachments' => $this->generateAttachments($timeOffType),
            'created_at' => $this->generateCreatedAt($startDate),
            'updated_at' => $this->generateUpdatedAt($startDate),
        ];

        // Add approval/rejection data based on status
        if ($status === 'approved') {
            $timeOffData = array_merge($timeOffData, $this->generateApprovalData($users));
        } elseif ($status === 'rejected') {
            $timeOffData = array_merge($timeOffData, $this->generateRejectionData($users));
        }

        return EmployeeTimeOff::create($timeOffData);
    }

    /**
     * Create related time-off requests (e.g., multiple days for same reason)
     */
    private function createRelatedTimeOffRequests($employee, $users, $originalTimeOff): void
    {
        $relatedTypes = ['vacation', 'personal'];

        if (in_array($originalTimeOff->time_off_type, $relatedTypes)) {
            // Create a follow-up request
            $followUpStart = Carbon::parse($originalTimeOff->end_date)->addDays(rand(1, 7));
            $followUpEnd = Carbon::parse($followUpStart)->addDays(rand(1, 3));

            EmployeeTimeOff::create([
                'employee_id' => $employee->id,
                'user_id' => $employee->user_id,
                'time_off_type' => $originalTimeOff->time_off_type,
                'start_date' => $followUpStart,
                'end_date' => $followUpEnd,
                'total_hours' => Carbon::parse($followUpStart)->diffInDays($followUpEnd) * 8,
                'total_days' => Carbon::parse($followUpStart)->diffInDays($followUpEnd) + 1,
                'reason' => 'Extended ' . $originalTimeOff->reason,
                'description' => 'Extension of previous request',
                'status' => 'pending',
                'is_half_day' => false,
                'is_urgent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Generate start date based on time-off type
     */
    private function generateStartDate(string $timeOffType): Carbon
    {
        $now = Carbon::now();

        switch ($timeOffType) {
            case 'sick':
            case 'bereavement':
                // Sick and bereavement can be in the past
                return $now->copy()->subDays(rand(1, 30));

            case 'vacation':
            case 'personal':
                // Vacation and personal are usually planned ahead
                return $now->copy()->addDays(rand(7, 90));

            case 'jury_duty':
            case 'military':
                // Jury duty and military are usually planned ahead
                return $now->copy()->addDays(rand(14, 180));

            default:
                return $now->copy()->addDays(rand(1, 60));
        }
    }

    /**
     * Generate end date based on start date and type
     */
    private function generateEndDate(Carbon $startDate, string $timeOffType): Carbon
    {
        $maxDays = $this->getMaxDaysForType($timeOffType);
        $daysToAdd = rand(1, $maxDays);

        return $startDate->copy()->addDays($daysToAdd - 1);
    }

    /**
     * Get maximum days for time-off type
     */
    private function getMaxDaysForType(string $timeOffType): int
    {
        return match ($timeOffType) {
            'sick' => 5,
            'personal' => 3,
            'bereavement' => 5,
            'jury_duty' => 10,
            'military' => 30,
            'vacation' => 14,
            default => 7,
        };
    }

    /**
     * Determine if time-off should be half-day
     */
    private function shouldBeHalfDay(string $timeOffType): bool
    {
        $halfDayTypes = ['personal', 'sick'];
        return in_array($timeOffType, $halfDayTypes) && rand(1, 4) === 1;
    }

    /**
     * Calculate total days
     */
    private function calculateTotalDays(Carbon $startDate, Carbon $endDate, bool $isHalfDay): float
    {
        $days = $startDate->diffInDays($endDate) + 1;
        return $isHalfDay ? 0.5 : $days;
    }

    /**
     * Generate reason based on time-off type
     */
    private function generateReason(string $timeOffType): string
    {
        $reasons = [
            'vacation' => [
                'Annual family vacation',
                'Holiday trip',
                'Rest and relaxation',
                'Family event',
                'Personal travel',
            ],
            'sick' => [
                'Illness',
                'Medical appointment',
                'Recovery from surgery',
                'Mental health day',
                'Contagious illness',
            ],
            'personal' => [
                'Personal appointment',
                'Family matter',
                'Home maintenance',
                'Personal development',
                'Religious observance',
            ],
            'bereavement' => [
                'Family member passing',
                'Funeral attendance',
                'Grieving period',
                'Memorial service',
            ],
            'jury_duty' => [
                'Jury duty summons',
                'Court appearance',
                'Legal obligation',
            ],
            'military' => [
                'Military training',
                'Reserve duty',
                'Military leave',
                'Service obligation',
            ],
            'other' => [
                'Special circumstance',
                'Unforeseen event',
                'Personal emergency',
                'Work-related absence',
            ],
        ];

        $typeReasons = $reasons[$timeOffType] ?? $reasons['other'];
        return $typeReasons[array_rand($typeReasons)];
    }

    /**
     * Generate description
     */
    private function generateDescription(string $timeOffType): string
    {
        $descriptions = [
            'vacation' => 'Planning to spend quality time with family and recharge.',
            'sick' => 'Unable to work due to health condition.',
            'personal' => 'Personal matter requiring immediate attention.',
            'bereavement' => 'Time needed to grieve and attend funeral services.',
            'jury_duty' => 'Legal obligation to serve on jury.',
            'military' => 'Military service requirement.',
            'other' => 'Special circumstance requiring time off.',
        ];

        return $descriptions[$timeOffType] ?? $descriptions['other'];
    }

    /**
     * Determine if time-off should be urgent
     */
    private function shouldBeUrgent(string $timeOffType): bool
    {
        $urgentTypes = ['sick', 'bereavement', 'personal'];
        return in_array($timeOffType, $urgentTypes) && rand(1, 5) === 1;
    }

    /**
     * Generate attachments
     */
    private function generateAttachments(string $timeOffType): ?string
    {
        $attachmentTypes = [
            'sick' => ['medical_certificate.pdf', 'doctor_note.pdf'],
            'bereavement' => ['death_certificate.pdf', 'funeral_notice.pdf'],
            'jury_duty' => ['jury_summons.pdf', 'court_notice.pdf'],
            'military' => ['military_orders.pdf', 'service_notice.pdf'],
        ];

        if (isset($attachmentTypes[$timeOffType]) && rand(1, 3) === 1) {
            $attachments = $attachmentTypes[$timeOffType];
            $selected = array_rand($attachments, rand(1, count($attachments)));
            $selected = is_array($selected) ? $selected : [$selected];

            return json_encode(array_map(fn($index) => $attachments[$index], $selected));
        }

        return null;
    }

    /**
     * Generate created at date
     */
    private function generateCreatedAt(Carbon $startDate): Carbon
    {
        $daysBefore = rand(1, 30);
        return $startDate->copy()->subDays($daysBefore);
    }

    /**
     * Generate updated at date
     */
    private function generateUpdatedAt(Carbon $startDate): Carbon
    {
        $createdAt = $this->generateCreatedAt($startDate);
        $daysAfter = rand(0, 7);
        return $createdAt->copy()->addDays($daysAfter);
    }

    /**
     * Generate approval data
     */
    private function generateApprovalData($users): array
    {
        $approver = $users->random();
        return [
            'approved_by' => $approver->id,
            'approved_at' => now()->subDays(rand(1, 7)),
        ];
    }

    /**
     * Generate rejection data
     */
    private function generateRejectionData($users): array
    {
        $rejector = $users->random();
        $rejectionReasons = [
            'Insufficient notice period',
            'Team workload too high',
            'Insufficient time-off balance',
            'Request conflicts with project deadlines',
            'Insufficient documentation provided',
        ];

        return [
            'rejected_by' => $rejector->id,
            'rejected_at' => now()->subDays(rand(1, 7)),
            'rejection_reason' => $rejectionReasons[array_rand($rejectionReasons)],
        ];
    }
}
