<?php

namespace App\Listeners\EmployeePosition;

use App\Events\EmployeePosition\EmployeePositionSetHiring;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CreateJobPosting implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(EmployeePositionSetHiring $event): void
    {
        try {
            $position = $event->position;
            $hiringDetails = $event->hiringDetails;

            // Create job posting
            $this->createJobPosting($position, $hiringDetails);

            // Update position with job posting reference
            $this->updatePositionWithJobPosting($position);

            // Log the action
            Log::info('Job posting created for hiring position', [
                'position_id' => $position->id,
                'title' => $position->title,
                'hiring_details' => $hiringDetails
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create job posting', [
                'event' => get_class($event),
                'position_id' => $event->position->id ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create job posting
     */
    protected function createJobPosting($position, array $hiringDetails): void
    {
        try {
            // Check if job_postings table exists
            if (DB::getSchemaBuilder()->hasTable('job_postings')) {
                $jobPostingId = DB::table('job_postings')->insertGetId([
                    'position_id' => $position->id,
                    'title' => $position->title,
                    'department_id' => $position->department_id,
                    'level' => $position->level->value,
                    'status' => 'active',
                    'is_remote' => $position->is_remote,
                    'is_travel_required' => $position->is_travel_required,
                    'salary_min' => $position->salary_min,
                    'salary_max' => $position->salary_max,
                    'hourly_rate_min' => $position->hourly_rate_min,
                    'hourly_rate_max' => $position->hourly_rate_max,
                    'requirements' => $position->requirements,
                    'responsibilities' => $position->responsibilities,
                    'skills_required' => json_encode($position->skills_required ?? []),
                    'experience_required' => $position->experience_required,
                    'education_required' => $position->education_required,
                    'urgency_level' => $hiringDetails['urgency_level'] ?? 'normal',
                    'expected_fill_date' => $hiringDetails['expected_fill_date'] ?? null,
                    'hiring_manager' => $hiringDetails['hiring_manager'] ?? null,
                    'recruitment_budget' => $hiringDetails['recruitment_budget'] ?? null,
                    'application_deadline' => $this->calculateApplicationDeadline($hiringDetails),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Store the job posting ID for later use
                $this->jobPostingId = $jobPostingId;

            } else {
                // Fallback to positions table if job_postings doesn't exist
                $this->updatePositionWithJobPostingDetails($position, $hiringDetails);

                Log::info('Job posting details added to position (no job_postings table)', [
                    'position_id' => $position->id,
                    'title' => $position->title
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to create job posting', [
                'position_id' => $position->id,
                'error' => $e->getMessage()
            ]);

            // Fallback to position update
            $this->updatePositionWithJobPostingDetails($position, $hiringDetails);
        }
    }

    /**
     * Update position with job posting reference
     */
    protected function updatePositionWithJobPosting($position): void
    {
        try {
            if (isset($this->jobPostingId)) {
                $position->update([
                    'job_posting_id' => $this->jobPostingId,
                    'job_posting_created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update position with job posting reference', [
                'position_id' => $position->id,
                'job_posting_id' => $this->jobPostingId ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update position with job posting details (fallback)
     */
    protected function updatePositionWithJobPostingDetails($position, array $hiringDetails): void
    {
        try {
            $updateData = [
                'job_posting_created_at' => now(),
                'job_posting_status' => 'active',
                'urgency_level' => $hiringDetails['urgency_level'] ?? 'normal',
                'expected_fill_date' => $hiringDetails['expected_fill_date'] ?? null,
                'hiring_manager' => $hiringDetails['hiring_manager'] ?? null,
                'recruitment_budget' => $hiringDetails['recruitment_budget'] ?? null,
                'application_deadline' => $this->calculateApplicationDeadline($hiringDetails),
                'updated_at' => now(),
            ];

            $position->update($updateData);

        } catch (\Exception $e) {
            Log::error('Failed to update position with job posting details', [
                'position_id' => $position->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate application deadline based on urgency level
     */
    protected function calculateApplicationDeadline(array $hiringDetails): string
    {
        $urgencyLevel = $hiringDetails['urgency_level'] ?? 'normal';
        $expectedFillDate = $hiringDetails['expected_fill_date'] ?? null;

        // Set application deadline based on urgency level
        switch ($urgencyLevel) {
            case 'critical':
                $deadlineDays = 7; // 1 week
                break;
            case 'urgent':
                $deadlineDays = 14; // 2 weeks
                break;
            case 'high':
                $deadlineDays = 21; // 3 weeks
                break;
            case 'normal':
            default:
                $deadlineDays = 30; // 1 month
                break;
        }

        $deadline = now()->addDays($deadlineDays);

        // If expected fill date is provided and it's sooner than calculated deadline, use it
        if ($expectedFillDate) {
            $expectedDate = \Carbon\Carbon::parse($expectedFillDate);
            if ($expectedDate->isBefore($deadline)) {
                $deadline = $expectedDate->subDays(7); // 1 week before expected fill date
            }
        }

        return $deadline->toDateString();
    }

    /**
     * Create job posting template
     */
    protected function createJobPostingTemplate($position, array $hiringDetails): string
    {
        $template = "# {$position->title}\n\n";
        $template .= "**Department:** {$position->department->name}\n";
        $template .= "**Level:** {$position->level->label()}\n";
        $template .= "**Location:** " . ($position->is_remote ? 'Remote' : 'On-site') . "\n";

        if ($position->is_travel_required) {
            $template .= "**Travel:** Required ({$position->travel_percentage}%)\n";
        }

        $template .= "\n## About the Role\n\n";
        $template .= $position->description ?? 'No description provided.';

        $template .= "\n\n## Responsibilities\n\n";
        if ($position->responsibilities) {
            $template .= $position->responsibilities;
        } else {
            $template .= "Responsibilities will be discussed during the interview process.";
        }

        $template .= "\n\n## Requirements\n\n";
        if ($position->requirements) {
            $template .= $position->requirements;
        } else {
            $template .= "Requirements will be discussed during the interview process.";
        }

        if (!empty($position->skills_required)) {
            $template .= "\n\n**Required Skills:**\n";
            foreach ($position->skills_required as $skill) {
                $template .= "- {$skill}\n";
            }
        }

        if ($position->experience_required) {
            $template .= "\n**Experience:** {$position->experience_required} years minimum\n";
        }

        if ($position->education_required) {
            $template .= "\n**Education:** {$position->education_required}\n";
        }

        $template .= "\n## Compensation\n\n";
        if ($position->salary_min && $position->salary_max) {
            $template .= "**Annual Salary:** $" . number_format($position->salary_min) . " - $" . number_format($position->salary_max) . "\n";
        }

        if ($position->hourly_rate_min && $position->hourly_rate_max) {
            $template .= "**Hourly Rate:** $" . number_format($position->hourly_rate_min, 2) . " - $" . number_format($position->hourly_rate_max, 2) . "\n";
        }

        $template .= "\n## Application Process\n\n";
        $template .= "Please submit your resume and cover letter through our application system.\n";
        $template .= "**Application Deadline:** " . $this->calculateApplicationDeadline($hiringDetails) . "\n";

        if (isset($hiringDetails['hiring_manager'])) {
            $template .= "\n**Hiring Manager:** {$hiringDetails['hiring_manager']}\n";
        }

        return $template;
    }

    /**
     * Publish job posting to external platforms
     */
    protected function publishToExternalPlatforms($position, array $hiringDetails): void
    {
        try {
            // This would typically integrate with external job boards
            // For now, we'll just log the intention

            $platforms = [
                'internal_careers_page' => true,
                'linkedin_jobs' => $this->shouldPublishToLinkedIn($position, $hiringDetails),
                'indeed' => $this->shouldPublishToIndeed($position, $hiringDetails),
                'glassdoor' => $this->shouldPublishToGlassdoor($position, $hiringDetails),
            ];

            foreach ($platforms as $platform => $shouldPublish) {
                if ($shouldPublish) {
                    Log::info("Job posting published to {$platform}", [
                        'position_id' => $position->id,
                        'title' => $position->title,
                        'platform' => $platform
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to publish job posting to external platforms', [
                'position_id' => $position->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Determine if job should be published to LinkedIn
     */
    protected function shouldPublishToLinkedIn($position, array $hiringDetails): bool
    {
        // Publish to LinkedIn for mid-level and above positions
        $seniorLevels = ['senior', 'lead', 'manager', 'director', 'executive'];
        return in_array($position->level->value, $seniorLevels);
    }

    /**
     * Determine if job should be published to Indeed
     */
    protected function shouldPublishToIndeed($position, array $hiringDetails): bool
    {
        // Publish to Indeed for all positions
        return true;
    }

    /**
     * Determine if job should be published to Glassdoor
     */
    protected function shouldPublishToGlassdoor($position, array $hiringDetails): bool
    {
        // Publish to Glassdoor for positions with salary information
        return !empty($position->salary_min) && !empty($position->salary_max);
    }
}
