<?php

namespace App\Listeners\EmployeeBenefits;

use App\Events\EmployeeBenefits\EmployeeBenefitsCancelled;
use App\Events\EmployeeBenefits\EmployeeBenefitsCreated;
use App\Events\EmployeeBenefits\EmployeeBenefitsEnrolled;
use App\Events\EmployeeBenefits\EmployeeBenefitsExpiring;
use App\Events\EmployeeBenefits\EmployeeBenefitsTerminated;
use App\Events\EmployeeBenefits\EmployeeBenefitsUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessBenefitsDocuments implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        try {
            match (true) {
                $event instanceof EmployeeBenefitsCreated => $this->handleBenefitsCreated($event),
                $event instanceof EmployeeBenefitsUpdated => $this->handleBenefitsUpdated($event),
                $event instanceof EmployeeBenefitsEnrolled => $this->handleBenefitsEnrolled($event),
                $event instanceof EmployeeBenefitsTerminated => $this->handleBenefitsTerminated($event),
                $event instanceof EmployeeBenefitsCancelled => $this->handleBenefitsCancelled($event),
                $event instanceof EmployeeBenefitsExpiring => $this->handleBenefitsExpiring($event),
                default => Log::info('Unknown EmployeeBenefits event type', ['event' => get_class($event)])
            };
        } catch (\Exception $e) {
            Log::error('Error processing benefits documents', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle benefits created event.
     */
    protected function handleBenefitsCreated(EmployeeBenefitsCreated $event): void
    {
        $benefit = $event->employeeBenefits;

        // Process enrollment documents
        $this->processEnrollmentDocuments($benefit);

        // Generate benefit summary document
        $this->generateBenefitSummary($benefit);

        Log::info('Benefits documents processed for creation', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id,
        ]);
    }

    /**
     * Handle benefits updated event.
     */
    protected function handleBenefitsUpdated(EmployeeBenefitsUpdated $event): void
    {
        $benefit = $event->employeeBenefits;
        $changes = $event->changes;

        // Update benefit summary document if significant changes
        if ($this->hasSignificantChanges($changes)) {
            $this->updateBenefitSummary($benefit);
        }

        // Process any new documents
        if (isset($changes['documents'])) {
            $this->processUpdatedDocuments($benefit, $changes['documents']);
        }

        Log::info('Benefits documents processed for update', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id,
            'changes' => $changes,
        ]);
    }

    /**
     * Handle benefits enrolled event.
     */
    protected function handleBenefitsEnrolled(EmployeeBenefitsEnrolled $event): void
    {
        $benefit = $event->employeeBenefits;

        // Generate enrollment confirmation document
        $this->generateEnrollmentConfirmation($benefit);

        // Process welcome materials
        $this->processWelcomeMaterials($benefit);

        Log::info('Benefits documents processed for enrollment', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id,
        ]);
    }

    /**
     * Handle benefits terminated event.
     */
    protected function handleBenefitsTerminated(EmployeeBenefitsTerminated $event): void
    {
        $benefit = $event->employeeBenefits;

        // Generate termination document
        $this->generateTerminationDocument($benefit, $event->reason);

        // Process COBRA documents if applicable
        $this->processCOBRADocuments($benefit);

        Log::info('Benefits documents processed for termination', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id,
        ]);
    }

    /**
     * Handle benefits cancelled event.
     */
    protected function handleBenefitsCancelled(EmployeeBenefitsCancelled $event): void
    {
        $benefit = $event->employeeBenefits;

        // Generate cancellation document
        $this->generateCancellationDocument($benefit, $event->reason);

        Log::info('Benefits documents processed for cancellation', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id,
        ]);
    }

    /**
     * Handle benefits expiring event.
     */
    protected function handleBenefitsExpiring(EmployeeBenefitsExpiring $event): void
    {
        $benefit = $event->employeeBenefits;

        // Generate renewal notice
        $this->generateRenewalNotice($benefit);

        // Process renewal documents
        $this->processRenewalDocuments($benefit);

        Log::info('Benefits documents processed for expiring', [
            'benefit_id' => $benefit->id,
            'employee_id' => $benefit->employee_id,
        ]);
    }

    /**
     * Process enrollment documents.
     */
    protected function processEnrollmentDocuments($benefit): void
    {
        try {
            // Process any uploaded documents
            if ($benefit->documents) {
                $documents = json_decode($benefit->documents, true);

                foreach ($documents as $document) {
                    $this->processDocument($document, $benefit);
                }
            }

            // Generate standard enrollment forms
            $this->generateEnrollmentForms($benefit);

        } catch (\Exception $e) {
            Log::error('Error processing enrollment documents', [
                'benefit_id' => $benefit->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate benefit summary document.
     */
    protected function generateBenefitSummary($benefit): void
    {
        try {
            $summaryData = [
                'benefit_id' => $benefit->id,
                'employee_name' => $benefit->employee->full_name ?? 'Unknown',
                'benefit_type' => $benefit->benefit_type,
                'benefit_name' => $benefit->benefit_name,
                'provider' => $benefit->provider,
                'coverage_level' => $benefit->coverage_level,
                'premium_amount' => $benefit->premium_amount,
                'employee_contribution' => $benefit->employee_contribution,
                'employer_contribution' => $benefit->employer_contribution,
                'effective_date' => $benefit->effective_date,
                'end_date' => $benefit->end_date,
                'status' => $benefit->status,
                'generated_at' => now()->toISOString(),
            ];

            // Store summary document
            $filename = "benefit_summary_{$benefit->id}_{$benefit->employee_id}.json";
            Storage::disk('local')->put("benefits/summaries/{$filename}", json_encode($summaryData, JSON_PRETTY_PRINT));

        } catch (\Exception $e) {
            Log::error('Error generating benefit summary', [
                'benefit_id' => $benefit->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate enrollment confirmation document.
     */
    protected function generateEnrollmentConfirmation($benefit): void
    {
        try {
            $confirmationData = [
                'benefit_id' => $benefit->id,
                'employee_name' => $benefit->employee->full_name ?? 'Unknown',
                'benefit_type' => $benefit->benefit_type,
                'benefit_name' => $benefit->benefit_name,
                'provider' => $benefit->provider,
                'enrollment_date' => $benefit->enrollment_date,
                'effective_date' => $benefit->effective_date,
                'status' => 'enrolled',
                'confirmation_number' => 'ENR-'.str_pad($benefit->id, 6, '0', STR_PAD_LEFT),
                'generated_at' => now()->toISOString(),
            ];

            $filename = "enrollment_confirmation_{$benefit->id}.json";
            Storage::disk('local')->put("benefits/confirmations/{$filename}", json_encode($confirmationData, JSON_PRETTY_PRINT));

        } catch (\Exception $e) {
            Log::error('Error generating enrollment confirmation', [
                'benefit_id' => $benefit->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate termination document.
     */
    protected function generateTerminationDocument($benefit, string $reason): void
    {
        try {
            $terminationData = [
                'benefit_id' => $benefit->id,
                'employee_name' => $benefit->employee->full_name ?? 'Unknown',
                'benefit_type' => $benefit->benefit_type,
                'benefit_name' => $benefit->benefit_name,
                'termination_date' => now()->toDateString(),
                'end_date' => $benefit->end_date,
                'reason' => $reason,
                'termination_number' => 'TERM-'.str_pad($benefit->id, 6, '0', STR_PAD_LEFT),
                'generated_at' => now()->toISOString(),
            ];

            $filename = "termination_document_{$benefit->id}.json";
            Storage::disk('local')->put("benefits/terminations/{$filename}", json_encode($terminationData, JSON_PRETTY_PRINT));

        } catch (\Exception $e) {
            Log::error('Error generating termination document', [
                'benefit_id' => $benefit->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate cancellation document.
     */
    protected function generateCancellationDocument($benefit, string $reason): void
    {
        try {
            $cancellationData = [
                'benefit_id' => $benefit->id,
                'employee_name' => $benefit->employee->full_name ?? 'Unknown',
                'benefit_type' => $benefit->benefit_type,
                'benefit_name' => $benefit->benefit_name,
                'cancellation_date' => now()->toDateString(),
                'reason' => $reason,
                'cancellation_number' => 'CANCEL-'.str_pad($benefit->id, 6, '0', STR_PAD_LEFT),
                'generated_at' => now()->toISOString(),
            ];

            $filename = "cancellation_document_{$benefit->id}.json";
            Storage::disk('local')->put("benefits/cancellations/{$filename}", json_encode($cancellationData, JSON_PRETTY_PRINT));

        } catch (\Exception $e) {
            Log::error('Error generating cancellation document', [
                'benefit_id' => $benefit->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate renewal notice.
     */
    protected function generateRenewalNotice($benefit): void
    {
        try {
            $renewalData = [
                'benefit_id' => $benefit->id,
                'employee_name' => $benefit->employee->full_name ?? 'Unknown',
                'benefit_type' => $benefit->benefit_type,
                'benefit_name' => $benefit->benefit_name,
                'current_end_date' => $benefit->end_date,
                'renewal_deadline' => now()->addDays(30)->toDateString(),
                'renewal_notice_number' => 'RENEW-'.str_pad($benefit->id, 6, '0', STR_PAD_LEFT),
                'generated_at' => now()->toISOString(),
            ];

            $filename = "renewal_notice_{$benefit->id}.json";
            Storage::disk('local')->put("benefits/renewals/{$filename}", json_encode($renewalData, JSON_PRETTY_PRINT));

        } catch (\Exception $e) {
            Log::error('Error generating renewal notice', [
                'benefit_id' => $benefit->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if changes are significant enough to regenerate summary.
     */
    protected function hasSignificantChanges(array $changes): bool
    {
        $significantFields = [
            'benefit_type', 'benefit_name', 'provider', 'coverage_level',
            'premium_amount', 'employee_contribution', 'employer_contribution',
            'effective_date', 'end_date', 'status',
        ];

        return ! empty(array_intersect(array_keys($changes), $significantFields));
    }

    /**
     * Update benefit summary document.
     */
    protected function updateBenefitSummary($benefit): void
    {
        $this->generateBenefitSummary($benefit);
    }

    /**
     * Process updated documents.
     */
    protected function processUpdatedDocuments($benefit, $documents): void
    {
        // Process any new or updated documents
        if (is_array($documents)) {
            foreach ($documents as $document) {
                $this->processDocument($document, $benefit);
            }
        }
    }

    /**
     * Process welcome materials.
     */
    protected function processWelcomeMaterials($benefit): void
    {
        // Generate welcome packet and materials
        // This would typically include benefit guides, contact information, etc.
    }

    /**
     * Process COBRA documents.
     */
    protected function processCOBRADocuments($benefit): void
    {
        // Generate COBRA continuation coverage documents if applicable
        // This depends on the benefit type and company policy
    }

    /**
     * Process renewal documents.
     */
    protected function processRenewalDocuments($benefit): void
    {
        // Generate renewal forms and documents
    }

    /**
     * Generate enrollment forms.
     */
    protected function generateEnrollmentForms($benefit): void
    {
        // Generate standard enrollment forms
    }

    /**
     * Process individual document.
     */
    protected function processDocument($document, $benefit): void
    {
        // Process individual document (validation, storage, etc.)
        // This would include document type validation, virus scanning, etc.
    }
}
