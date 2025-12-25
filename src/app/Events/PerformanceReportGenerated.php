<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PerformanceReportGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reportData;

    public $reportType;

    public $generatedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(array $reportData, string $reportType, $generatedBy)
    {
        $this->reportData = $reportData;
        $this->reportType = $reportType;
        $this->generatedBy = $generatedBy;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('performance-reports'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'report_type' => $this->reportType,
            'generated_by' => $this->generatedBy->id ?? null,
            'generated_at' => now(),
            'report_summary' => [
                'total_providers' => $this->reportData['total_providers'] ?? 0,
                'period' => $this->reportData['period'] ?? null,
                'scope' => $this->reportData['scope'] ?? 'all',
            ],
        ];
    }
}
