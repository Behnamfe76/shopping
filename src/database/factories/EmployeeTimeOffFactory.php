<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeeTimeOff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeTimeOff>
 */
class EmployeeTimeOffFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmployeeTimeOff::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+6 months');
        $endDate = Carbon::parse($startDate)->addDays($this->faker->numberBetween(1, 14));

        $timeOffTypes = ['vacation', 'sick', 'personal', 'bereavement', 'jury_duty', 'military', 'other'];
        $statuses = ['pending', 'approved', 'rejected', 'cancelled'];

        $isHalfDay = $this->faker->boolean(20);
        $totalDays = $isHalfDay ? 0.5 : $this->faker->numberBetween(1, 14);

        return [
            'employee_id' => Employee::factory(),
            'user_id' => User::factory(),
            'time_off_type' => $this->faker->randomElement($timeOffTypes),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $isHalfDay ? $this->faker->time('H:i:s') : null,
            'end_time' => $isHalfDay ? $this->faker->time('H:i:s') : null,
            'total_hours' => $isHalfDay ? 4 : ($totalDays * 8),
            'total_days' => $totalDays,
            'reason' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement($statuses),
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
            'is_half_day' => $isHalfDay,
            'is_urgent' => $this->faker->boolean(10),
            'attachments' => $this->faker->boolean(30) ? json_encode([
                $this->faker->filePath(),
                $this->faker->filePath(),
            ]) : null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Indicate that the time-off is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by' => User::factory(),
            'approved_at' => $this->faker->dateTimeBetween($attributes['created_at'], 'now'),
        ]);
    }

    /**
     * Indicate that the time-off is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejected_by' => User::factory(),
            'rejected_at' => $this->faker->dateTimeBetween($attributes['created_at'], 'now'),
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the time-off is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    /**
     * Indicate that the time-off is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_urgent' => true,
        ]);
    }

    /**
     * Indicate that the time-off is a half-day.
     */
    public function halfDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_half_day' => true,
            'total_days' => 0.5,
            'total_hours' => 4,
            'start_time' => $this->faker->time('H:i:s'),
            'end_time' => $this->faker->time('H:i:s'),
        ]);
    }

    /**
     * Create vacation time-off.
     */
    public function vacation(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_off_type' => 'vacation',
            'start_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'end_date' => function (array $attributes) {
                return Carbon::parse($attributes['start_date'])->addDays($this->faker->numberBetween(1, 10));
            },
        ]);
    }

    /**
     * Create sick time-off.
     */
    public function sick(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_off_type' => 'sick',
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date' => function (array $attributes) {
                return Carbon::parse($attributes['start_date'])->addDays($this->faker->numberBetween(1, 5));
            },
            'is_urgent' => true,
        ]);
    }

    /**
     * Create personal time-off.
     */
    public function personal(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_off_type' => 'personal',
            'start_date' => $this->faker->dateTimeBetween('now', '+2 months'),
            'end_date' => function (array $attributes) {
                return Carbon::parse($attributes['start_date'])->addDays($this->faker->numberBetween(1, 3));
            },
        ]);
    }

    /**
     * Create bereavement time-off.
     */
    public function bereavement(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_off_type' => 'bereavement',
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'end_date' => function (array $attributes) {
                return Carbon::parse($attributes['start_date'])->addDays($this->faker->numberBetween(1, 5));
            },
            'is_urgent' => true,
        ]);
    }

    /**
     * Create jury duty time-off.
     */
    public function juryDuty(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_off_type' => 'jury_duty',
            'start_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'end_date' => function (array $attributes) {
                return Carbon::parse($attributes['start_date'])->addDays($this->faker->numberBetween(1, 10));
            },
        ]);
    }

    /**
     * Create military time-off.
     */
    public function military(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_off_type' => 'military',
            'start_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'end_date' => function (array $attributes) {
                return Carbon::parse($attributes['start_date'])->addDays($this->faker->numberBetween(14, 30));
            },
        ]);
    }

    /**
     * Create time-off for a specific employee.
     */
    public function forEmployee(Employee $employee): static
    {
        return $this->state(fn (array $attributes) => [
            'employee_id' => $employee->id,
            'user_id' => $employee->user_id,
        ]);
    }

    /**
     * Create time-off for a specific date range.
     */
    public function forDateRange(string $startDate, string $endDate): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1,
        ]);
    }

    /**
     * Create time-off for the current year.
     */
    public function currentYear(): static
    {
        $year = now()->year;
        $startDate = $this->faker->dateTimeBetween("{$year}-01-01", "{$year}-12-31");
        $endDate = Carbon::parse($startDate)->addDays($this->faker->numberBetween(1, 7));

        return $this->state(fn (array $attributes) => [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    /**
     * Create time-off for the previous year.
     */
    public function previousYear(): static
    {
        $year = now()->subYear()->year;
        $startDate = $this->faker->dateTimeBetween("{$year}-01-01", "{$year}-12-31");
        $endDate = Carbon::parse($startDate)->addDays($this->faker->numberBetween(1, 7));

        return $this->state(fn (array $attributes) => [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }
}
