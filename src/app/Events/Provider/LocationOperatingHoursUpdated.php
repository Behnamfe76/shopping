<?php

namespace Fereydooni\Shopping\app\Events\Provider;

use Fereydooni\Shopping\app\Models\ProviderLocation;
use Fereydooni\Shopping\app\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LocationOperatingHoursUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProviderLocation $providerLocation;

    public ?User $user;

    public array $oldOperatingHours;

    public array $newOperatingHours;

    public array $operatingHoursData;

    public ?string $updateReason;

    /**
     * Create a new event instance.
     */
    public function __construct(
        ProviderLocation $providerLocation,
        array $oldOperatingHours,
        array $newOperatingHours,
        ?User $user = null,
        ?string $updateReason = null
    ) {
        $this->providerLocation = $providerLocation;
        $this->user = $user;
        $this->oldOperatingHours = $oldOperatingHours;
        $this->newOperatingHours = $newOperatingHours;
        $this->updateReason = $updateReason;

        // Extract operating hours data
        $this->operatingHoursData = [
            'location_id' => $providerLocation->id,
            'provider_id' => $providerLocation->provider_id,
            'location_name' => $providerLocation->location_name,
            'old_operating_hours' => $this->formatOperatingHours($oldOperatingHours),
            'new_operating_hours' => $this->formatOperatingHours($newOperatingHours),
            'changes_summary' => $this->generateChangesSummary(),
            'update_reason' => $updateReason,
            'operating_hours_changed' => $this->operatingHoursChanged(),
            'updated_at' => now()->toISOString(),
        ];
    }

    /**
     * Format operating hours for display
     */
    protected function formatOperatingHours(array $operatingHours): array
    {
        $formatted = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            if (isset($operatingHours[$day])) {
                $hours = $operatingHours[$day];
                if (isset($hours['is_closed']) && $hours['is_closed']) {
                    $formatted[$day] = 'Closed';
                } elseif (isset($hours['open']) && isset($hours['close'])) {
                    $formatted[$day] = "{$hours['open']} - {$hours['close']}";
                    if (isset($hours['notes'])) {
                        $formatted[$day] .= " ({$hours['notes']})";
                    }
                } else {
                    $formatted[$day] = 'Not specified';
                }
            } else {
                $formatted[$day] = 'Not specified';
            }
        }

        return $formatted;
    }

    /**
     * Generate a summary of changes
     */
    protected function generateChangesSummary(): array
    {
        $summary = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            $oldHours = $this->oldOperatingHours[$day] ?? null;
            $newHours = $this->newOperatingHours[$day] ?? null;

            if ($this->hoursChanged($oldHours, $newHours)) {
                $summary[$day] = [
                    'old' => $this->formatDayHours($oldHours),
                    'new' => $this->formatDayHours($newHours),
                    'change_type' => $this->determineChangeType($oldHours, $newHours),
                ];
            }
        }

        return $summary;
    }

    /**
     * Format hours for a specific day
     */
    protected function formatDayHours(?array $hours): string
    {
        if (! $hours) {
            return 'Not specified';
        }

        if (isset($hours['is_closed']) && $hours['is_closed']) {
            return 'Closed';
        }

        if (isset($hours['open']) && isset($hours['close'])) {
            $formatted = "{$hours['open']} - {$hours['close']}";
            if (isset($hours['notes'])) {
                $formatted .= " ({$hours['notes']})";
            }

            return $formatted;
        }

        return 'Not specified';
    }

    /**
     * Check if hours for a specific day changed
     */
    protected function hoursChanged(?array $oldHours, ?array $newHours): bool
    {
        if ($oldHours === $newHours) {
            return false;
        }

        if (is_null($oldHours) && is_null($newHours)) {
            return false;
        }

        if (is_null($oldHours) || is_null($newHours)) {
            return true;
        }

        // Check if closed status changed
        $oldClosed = $oldHours['is_closed'] ?? false;
        $newClosed = $newHours['is_closed'] ?? false;

        if ($oldClosed !== $newClosed) {
            return true;
        }

        // Check if open/close times changed
        $oldOpen = $oldHours['open'] ?? null;
        $oldClose = $oldHours['close'] ?? null;
        $newOpen = $newHours['open'] ?? null;
        $newClose = $newHours['close'] ?? null;

        if ($oldOpen !== $newOpen || $oldClose !== $newClose) {
            return true;
        }

        // Check if notes changed
        $oldNotes = $oldHours['notes'] ?? null;
        $newNotes = $newHours['notes'] ?? null;

        return $oldNotes !== $newNotes;
    }

    /**
     * Determine the type of change for a day
     */
    protected function determineChangeType(?array $oldHours, ?array $newHours): string
    {
        if (is_null($oldHours) && ! is_null($newHours)) {
            return 'added';
        }

        if (! is_null($oldHours) && is_null($newHours)) {
            return 'removed';
        }

        if (is_null($oldHours) && is_null($newHours)) {
            return 'unchanged';
        }

        $oldClosed = $oldHours['is_closed'] ?? false;
        $newClosed = $newHours['is_closed'] ?? false;

        if ($oldClosed && ! $newClosed) {
            return 'opened';
        }

        if (! $oldClosed && $newClosed) {
            return 'closed';
        }

        if (! $oldClosed && ! $newClosed) {
            return 'hours_modified';
        }

        return 'modified';
    }

    /**
     * Check if operating hours actually changed
     */
    protected function operatingHoursChanged(): bool
    {
        return ! empty($this->generateChangesSummary());
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider.'.$this->providerLocation->provider_id),
            new PrivateChannel('admin.provider-locations'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'event_type' => 'location_operating_hours_updated',
            'location_id' => $this->providerLocation->id,
            'provider_id' => $this->providerLocation->provider_id,
            'location_name' => $this->providerLocation->location_name,
            'operating_hours_data' => $this->operatingHoursData,
            'user_id' => $this->user?->id,
            'user_name' => $this->user?->name,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the event name for broadcasting.
     */
    public function broadcastAs(): string
    {
        return 'provider.location.operating_hours_updated';
    }

    /**
     * Check if any days were added
     */
    public function hasDaysAdded(): bool
    {
        foreach ($this->operatingHoursData['changes_summary'] as $day => $change) {
            if ($change['change_type'] === 'added') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if any days were removed
     */
    public function hasDaysRemoved(): bool
    {
        foreach ($this->operatingHoursData['changes_summary'] as $day => $change) {
            if ($change['change_type'] === 'removed') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if any days were closed
     */
    public function hasDaysClosed(): bool
    {
        foreach ($this->operatingHoursData['changes_summary'] as $day => $change) {
            if ($change['change_type'] === 'closed') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if any days were opened
     */
    public function hasDaysOpened(): bool
    {
        foreach ($this->operatingHoursData['changes_summary'] as $day => $change) {
            if ($change['change_type'] === 'opened') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if any hours were modified
     */
    public function hasHoursModified(): bool
    {
        foreach ($this->operatingHoursData['changes_summary'] as $day => $change) {
            if ($change['change_type'] === 'hours_modified') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the update reason
     */
    public function getUpdateReason(): ?string
    {
        return $this->updateReason;
    }

    /**
     * Get the number of days that changed
     */
    public function getChangedDaysCount(): int
    {
        return count($this->operatingHoursData['changes_summary']);
    }
}
