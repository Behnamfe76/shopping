<?php

namespace App\Events;

use App\Models\ProviderPerformance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PerformanceGradeChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $providerPerformance;
    public $oldGrade;
    public $newGrade;
    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(ProviderPerformance $providerPerformance, string $oldGrade, string $newGrade, string $reason = '')
    {
        $this->providerPerformance = $providerPerformance;
        $this->oldGrade = $oldGrade;
        $this->newGrade = $newGrade;
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('provider-performance.' . $this->providerPerformance->id),
            new Channel('performance-grade-changes'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->providerPerformance->id,
            'provider_id' => $this->providerPerformance->provider_id,
            'old_grade' => $this->oldGrade,
            'new_grade' => $this->newGrade,
            'reason' => $this->reason,
            'changed_at' => now(),
            'performance_score' => $this->providerPerformance->performance_score,
        ];
    }
}
