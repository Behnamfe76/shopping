<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryCreated;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryUpdated;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryApproved;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryRejected;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryProcessed;
use App\Events\EmployeeSalaryHistory\EmployeeSalaryHistoryRetroactive;
use App\Listeners\EmployeeSalaryHistory\SendSalaryHistoryNotification;
use App\Listeners\EmployeeSalaryHistory\UpdateEmployeeSalaryRecord;
use App\Listeners\EmployeeSalaryHistory\LogSalaryHistoryActivity;
use App\Listeners\EmployeeSalaryHistory\UpdatePayrollRecords;
use App\Listeners\EmployeeSalaryHistory\UpdateSalaryMetrics;
use App\Listeners\EmployeeSalaryHistory\ProcessRetroactivePayroll;

class EmployeeSalaryHistoryEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        EmployeeSalaryHistoryCreated::class => [
            SendSalaryHistoryNotification::class,
            LogSalaryHistoryActivity::class,
        ],

        EmployeeSalaryHistoryUpdated::class => [
            LogSalaryHistoryActivity::class,
        ],

        EmployeeSalaryHistoryApproved::class => [
            SendSalaryHistoryNotification::class,
            UpdateEmployeeSalaryRecord::class,
            LogSalaryHistoryActivity::class,
            UpdateSalaryMetrics::class,
        ],

        EmployeeSalaryHistoryRejected::class => [
            SendSalaryHistoryNotification::class,
            LogSalaryHistoryActivity::class,
        ],

        EmployeeSalaryHistoryProcessed::class => [
            UpdateEmployeeSalaryRecord::class,
            UpdatePayrollRecords::class,
            UpdateSalaryMetrics::class,
            ProcessRetroactivePayroll::class,
            LogSalaryHistoryActivity::class,
        ],

        EmployeeSalaryHistoryRetroactive::class => [
            ProcessRetroactivePayroll::class,
            LogSalaryHistoryActivity::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
