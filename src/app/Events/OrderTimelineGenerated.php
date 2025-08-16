<?php

namespace Fereydooni\Shopping\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Collection;
use Fereydooni\Shopping\app\Models\Order;

class OrderTimelineGenerated
{
    use Dispatchable, SerializesModels;

    public Order $order;
    public Collection $timeline;
    public int $totalEvents;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, Collection $timeline)
    {
        $this->order = $order;
        $this->timeline = $timeline;
        $this->totalEvents = $timeline->count();
    }

    /**
     * Get the event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.timeline-generated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'total_events' => $this->totalEvents,
            'timeline_summary' => [
                'first_event_at' => $this->timeline->first()?->changed_at?->toISOString(),
                'last_event_at' => $this->timeline->last()?->changed_at?->toISOString(),
                'duration_days' => $this->timeline->first() && $this->timeline->last()
                    ? $this->timeline->first()->changed_at->diffInDays($this->timeline->last()->changed_at)
                    : 0,
                'system_changes_count' => $this->timeline->where('is_system_change', true)->count(),
                'user_changes_count' => $this->timeline->where('is_system_change', false)->count(),
                'unique_statuses' => $this->timeline->pluck('new_status')->unique()->count(),
            ],
        ];
    }
}
