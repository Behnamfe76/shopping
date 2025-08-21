<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Support\Facades\DB;
use Fereydooni\Shopping\app\Models\CustomerSegment;
use Fereydooni\Shopping\app\Enums\SegmentStatus;
use Fereydooni\Shopping\app\Enums\SegmentPriority;

trait HasCustomerSegmentStatusManagement
{
    // Status management operations
    public function activate(CustomerSegment $segment): bool
    {
        return DB::transaction(function () use ($segment) {
            $oldStatus = $segment->status;
            $result = $segment->activate();
            
            if ($result) {
                // Log the status change
                $this->logStatusChange($segment, 'activated', $oldStatus, $segment->status);
            }
            
            return $result;
        });
    }

    public function deactivate(CustomerSegment $segment): bool
    {
        return DB::transaction(function () use ($segment) {
            $oldStatus = $segment->status;
            $result = $segment->deactivate();
            
            if ($result) {
                // Log the status change
                $this->logStatusChange($segment, 'deactivated', $oldStatus, $segment->status);
            }
            
            return $result;
        });
    }

    public function archive(CustomerSegment $segment): bool
    {
        return DB::transaction(function () use ($segment) {
            $oldStatus = $segment->status;
            $result = $segment->archive();
            
            if ($result) {
                // Log the status change
                $this->logStatusChange($segment, 'archived', $oldStatus, $segment->status);
            }
            
            return $result;
        });
    }

    public function makeAutomatic(CustomerSegment $segment): bool
    {
        return DB::transaction(function () use ($segment) {
            $oldValue = $segment->is_automatic;
            $result = $segment->makeAutomatic();
            
            if ($result) {
                // Log the change
                $this->logPropertyChange($segment, 'made_automatic', ['is_automatic' => $oldValue], ['is_automatic' => true]);
            }
            
            return $result;
        });
    }

    public function makeManual(CustomerSegment $segment): bool
    {
        return DB::transaction(function () use ($segment) {
            $oldValue = $segment->is_automatic;
            $result = $segment->makeManual();
            
            if ($result) {
                // Log the change
                $this->logPropertyChange($segment, 'made_manual', ['is_automatic' => $oldValue], ['is_automatic' => false]);
            }
            
            return $result;
        });
    }

    public function makeDynamic(CustomerSegment $segment): bool
    {
        return DB::transaction(function () use ($segment) {
            $oldValues = [
                'is_dynamic' => $segment->is_dynamic,
                'is_static' => $segment->is_static
            ];
            
            $result = $segment->makeDynamic();
            
            if ($result) {
                // Log the change
                $this->logPropertyChange($segment, 'made_dynamic', $oldValues, [
                    'is_dynamic' => true,
                    'is_static' => false
                ]);
            }
            
            return $result;
        });
    }

    public function makeStatic(CustomerSegment $segment): bool
    {
        return DB::transaction(function () use ($segment) {
            $oldValues = [
                'is_dynamic' => $segment->is_dynamic,
                'is_static' => $segment->is_static
            ];
            
            $result = $segment->makeStatic();
            
            if ($result) {
                // Log the change
                $this->logPropertyChange($segment, 'made_static', $oldValues, [
                    'is_dynamic' => false,
                    'is_static' => true
                ]);
            }
            
            return $result;
        });
    }

    public function setPriority(CustomerSegment $segment, SegmentPriority $priority): bool
    {
        return DB::transaction(function () use ($segment, $priority) {
            $oldPriority = $segment->priority;
            $result = $segment->setPriority($priority);
            
            if ($result) {
                // Log the priority change
                $this->logPropertyChange($segment, 'priority_changed', 
                    ['priority' => $oldPriority->value], 
                    ['priority' => $priority->value]
                );
            }
            
            return $result;
        });
    }

    // Status validation
    public function validateStatusChange(CustomerSegment $segment, SegmentStatus $newStatus): bool
    {
        $currentStatus = $segment->status;
        
        // Define allowed status transitions
        $allowedTransitions = [
            SegmentStatus::DRAFT->value => [SegmentStatus::ACTIVE->value, SegmentStatus::ARCHIVED->value],
            SegmentStatus::ACTIVE->value => [SegmentStatus::INACTIVE->value, SegmentStatus::ARCHIVED->value],
            SegmentStatus::INACTIVE->value => [SegmentStatus::ACTIVE->value, SegmentStatus::ARCHIVED->value],
            SegmentStatus::ARCHIVED->value => [SegmentStatus::DRAFT->value],
        ];
        
        $allowedNextStatuses = $allowedTransitions[$currentStatus->value] ?? [];
        
        return in_array($newStatus->value, $allowedNextStatuses);
    }

    public function canActivate(CustomerSegment $segment): bool
    {
        return $this->validateStatusChange($segment, SegmentStatus::ACTIVE);
    }

    public function canDeactivate(CustomerSegment $segment): bool
    {
        return $this->validateStatusChange($segment, SegmentStatus::INACTIVE);
    }

    public function canArchive(CustomerSegment $segment): bool
    {
        return $this->validateStatusChange($segment, SegmentStatus::ARCHIVED);
    }

    public function canMakeAutomatic(CustomerSegment $segment): bool
    {
        // Only manual segments can be made automatic
        return !$segment->is_automatic && $segment->type->isAutomatic();
    }

    public function canMakeManual(CustomerSegment $segment): bool
    {
        // Only automatic segments can be made manual
        return $segment->is_automatic;
    }

    public function canMakeDynamic(CustomerSegment $segment): bool
    {
        // Only static segments can be made dynamic
        return $segment->is_static && $segment->type->isDynamic();
    }

    public function canMakeStatic(CustomerSegment $segment): bool
    {
        // Only dynamic segments can be made static
        return $segment->is_dynamic;
    }

    public function canSetPriority(CustomerSegment $segment, SegmentPriority $priority): bool
    {
        // Basic validation - in a real implementation, you might have business rules
        return $segment->priority !== $priority;
    }

    // Status change events
    protected function logStatusChange(CustomerSegment $segment, string $action, SegmentStatus $oldStatus, SegmentStatus $newStatus): void
    {
        // Log the status change in segment history
        if (class_exists('Fereydooni\Shopping\app\Models\CustomerSegmentHistory')) {
            \Fereydooni\Shopping\app\Models\CustomerSegmentHistory::logAction(
                $segment,
                $action,
                "Segment status changed from {$oldStatus->label()} to {$newStatus->label()}",
                ['status' => $oldStatus->value],
                ['status' => $newStatus->value],
                auth()->id() ?? null
            );
        }
    }

    protected function logPropertyChange(CustomerSegment $segment, string $action, array $oldValues, array $newValues): void
    {
        // Log the property change in segment history
        if (class_exists('Fereydooni\Shopping\app\Models\CustomerSegmentHistory')) {
            \Fereydooni\Shopping\app\Models\CustomerSegmentHistory::logAction(
                $segment,
                $action,
                "Segment property changed: " . implode(', ', array_keys($newValues)),
                $oldValues,
                $newValues,
                auth()->id() ?? null
            );
        }
    }

    // Status change tracking
    public function getStatusChangeHistory(CustomerSegment $segment): array
    {
        if (!class_exists('Fereydooni\Shopping\app\Models\CustomerSegmentHistory')) {
            return [];
        }

        return $segment->segmentHistory()
            ->whereIn('action', ['activated', 'deactivated', 'archived', 'priority_changed'])
            ->orderBy('performed_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getLastStatusChange(CustomerSegment $segment): ?array
    {
        $history = $this->getStatusChangeHistory($segment);
        return $history[0] ?? null;
    }

    public function getStatusChangeCount(CustomerSegment $segment): int
    {
        return count($this->getStatusChangeHistory($segment));
    }
}
