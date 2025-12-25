<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeAnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            // Employee counts
            'total_employees' => $this->resource['total_employees'] ?? 0,
            'active_employees' => $this->resource['active_employees'] ?? 0,
            'inactive_employees' => $this->resource['inactive_employees'] ?? 0,
            'terminated_employees' => $this->resource['terminated_employees'] ?? 0,
            'pending_employees' => $this->resource['pending_employees'] ?? 0,
            'on_leave_employees' => $this->resource['on_leave_employees'] ?? 0,

            // Status distribution
            'status_distribution' => [
                'active' => [
                    'count' => $this->resource['active_employees'] ?? 0,
                    'percentage' => $this->calculatePercentage($this->resource['active_employees'] ?? 0, $this->resource['total_employees'] ?? 1),
                ],
                'inactive' => [
                    'count' => $this->resource['inactive_employees'] ?? 0,
                    'percentage' => $this->calculatePercentage($this->resource['inactive_employees'] ?? 0, $this->resource['total_employees'] ?? 1),
                ],
                'terminated' => [
                    'count' => $this->resource['terminated_employees'] ?? 0,
                    'percentage' => $this->calculatePercentage($this->resource['terminated_employees'] ?? 0, $this->resource['total_employees'] ?? 1),
                ],
                'pending' => [
                    'count' => $this->resource['pending_employees'] ?? 0,
                    'percentage' => $this->calculatePercentage($this->resource['pending_employees'] ?? 0, $this->resource['total_employees'] ?? 1),
                ],
                'on_leave' => [
                    'count' => $this->resource['on_leave_employees'] ?? 0,
                    'percentage' => $this->calculatePercentage($this->resource['on_leave_employees'] ?? 0, $this->resource['total_employees'] ?? 1),
                ],
            ],

            // Employment type distribution
            'employment_type_distribution' => $this->resource['employment_type_distribution'] ?? [],

            // Department distribution
            'department_distribution' => $this->resource['department_distribution'] ?? [],

            // Performance metrics
            'average_performance_rating' => $this->resource['average_performance_rating'] ?? 0,
            'performance_distribution' => $this->resource['performance_distribution'] ?? [],
            'top_performers_count' => $this->resource['top_performers_count'] ?? 0,
            'employees_needing_reviews' => $this->resource['employees_needing_reviews'] ?? 0,
            'overdue_reviews' => $this->resource['overdue_reviews'] ?? 0,

            // Salary metrics
            'total_salary' => $this->resource['total_salary'] ?? 0,
            'average_salary' => $this->resource['average_salary'] ?? 0,
            'salary_distribution' => $this->resource['salary_distribution'] ?? [],
            'salary_by_department' => $this->resource['salary_by_department'] ?? [],
            'salary_by_employment_type' => $this->resource['salary_by_employment_type'] ?? [],

            // Time-off metrics
            'vacation_utilization_rate' => $this->resource['vacation_utilization_rate'] ?? 0,
            'sick_leave_utilization_rate' => $this->resource['sick_leave_utilization_rate'] ?? 0,
            'employees_with_low_vacation' => $this->resource['employees_with_low_vacation'] ?? 0,
            'employees_with_low_sick_days' => $this->resource['employees_with_low_sick_days'] ?? 0,

            // Benefits metrics
            'benefits_enrollment_rate' => $this->resource['benefits_enrollment_rate'] ?? 0,
            'employees_enrolled_in_benefits' => $this->resource['employees_enrolled_in_benefits'] ?? 0,
            'employees_not_enrolled_in_benefits' => $this->resource['employees_not_enrolled_in_benefits'] ?? 0,

            // Hierarchy metrics
            'total_managers' => $this->resource['total_managers'] ?? 0,
            'total_subordinates' => $this->resource['total_subordinates'] ?? 0,
            'employees_without_manager' => $this->resource['employees_without_manager'] ?? 0,
            'average_span_of_control' => $this->resource['average_span_of_control'] ?? 0,
            'hierarchy_levels' => $this->resource['hierarchy_levels'] ?? 0,

            // Demographics
            'gender_distribution' => $this->resource['gender_distribution'] ?? [],
            'age_distribution' => $this->resource['age_distribution'] ?? [],
            'tenure_distribution' => $this->resource['tenure_distribution'] ?? [],

            // Growth and turnover metrics
            'new_hires_this_month' => $this->resource['new_hires_this_month'] ?? 0,
            'terminations_this_month' => $this->resource['terminations_this_month'] ?? 0,
            'growth_rate' => $this->resource['growth_rate'] ?? 0,
            'turnover_rate' => $this->resource['turnover_rate'] ?? 0,
            'retention_rate' => $this->resource['retention_rate'] ?? 0,

            // Training and development
            'employees_with_skills' => $this->resource['employees_with_skills'] ?? 0,
            'employees_with_certifications' => $this->resource['employees_with_certifications'] ?? 0,
            'average_training_completed' => $this->resource['average_training_completed'] ?? 0,

            // Alerts and notifications
            'at_risk_employees' => $this->resource['at_risk_employees'] ?? 0,
            'employees_needing_attention' => $this->resource['employees_needing_attention'] ?? 0,

            // Generated at
            'generated_at' => now()->toISOString(),
            'period' => $this->resource['period'] ?? 'current',
            'department_filter' => $this->resource['department_filter'] ?? null,
        ];
    }

    /**
     * Calculate percentage.
     */
    private function calculatePercentage(int $value, int $total): float
    {
        if ($total === 0) {
            return 0;
        }

        return round(($value / $total) * 100, 2);
    }
}
