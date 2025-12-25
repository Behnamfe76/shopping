<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\ProviderCommunication;
use Illuminate\Support\Str;

trait HasProviderCommunicationThreadManagement
{
    /**
     * Create a new thread
     */
    public function createThread(ProviderCommunication $communication): string
    {
        $threadId = Str::uuid()->toString();

        $communication->update(['thread_id' => $threadId]);

        return $threadId;
    }

    /**
     * Add communication to existing thread
     */
    public function addToThread(ProviderCommunication $communication, string $threadId): bool
    {
        try {
            $communication->update(['thread_id' => $threadId]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get thread communications
     */
    public function getThreadCommunications(string $threadId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return ProviderCommunication::where('thread_id', $threadId)
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get thread summary
     */
    public function getThreadSummary(string $threadId): array
    {
        $communications = $this->getThreadCommunications($threadId);

        if ($communications->isEmpty()) {
            return [];
        }

        $firstMessage = $communications->first();
        $lastMessage = $communications->last();

        return [
            'thread_id' => $threadId,
            'provider_id' => $firstMessage->provider_id,
            'total_messages' => $communications->count(),
            'started_at' => $firstMessage->created_at,
            'last_activity' => $lastMessage->updated_at,
            'status' => $lastMessage->status,
            'participants' => $communications->pluck('user_id')->unique()->count(),
            'communication_types' => $communications->pluck('communication_type')->unique()->values(),
        ];
    }

    /**
     * Merge threads
     */
    public function mergeThreads(string $sourceThreadId, string $targetThreadId): bool
    {
        try {
            ProviderCommunication::where('thread_id', $sourceThreadId)
                ->update(['thread_id' => $targetThreadId]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Split thread
     */
    public function splitThread(string $threadId, array $communicationIds, string $newThreadId): bool
    {
        try {
            ProviderCommunication::whereIn('id', $communicationIds)
                ->where('thread_id', $threadId)
                ->update(['thread_id' => $newThreadId]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Close thread
     */
    public function closeThread(string $threadId): bool
    {
        try {
            ProviderCommunication::where('thread_id', $threadId)
                ->update(['status' => 'closed']);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Archive thread
     */
    public function archiveThread(string $threadId): bool
    {
        try {
            ProviderCommunication::where('thread_id', $threadId)
                ->update([
                    'is_archived' => true,
                    'status' => 'archived',
                ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get thread participants
     */
    public function getThreadParticipants(string $threadId): \Illuminate\Database\Eloquent\Collection
    {
        return ProviderCommunication::where('thread_id', $threadId)
            ->with('user:id,name,email')
            ->get()
            ->pluck('user')
            ->unique('id');
    }

    /**
     * Get thread timeline
     */
    public function getThreadTimeline(string $threadId): array
    {
        $communications = $this->getThreadCommunications($threadId);

        return $communications->map(function ($communication) {
            return [
                'id' => $communication->id,
                'type' => $communication->communication_type,
                'direction' => $communication->direction,
                'status' => $communication->status,
                'subject' => $communication->subject,
                'message' => $communication->message,
                'user_id' => $communication->user_id,
                'created_at' => $communication->created_at,
                'read_at' => $communication->read_at,
                'replied_at' => $communication->replied_at,
            ];
        })->toArray();
    }

    /**
     * Get thread statistics
     */
    public function getThreadStatistics(string $threadId): array
    {
        $communications = $this->getThreadCommunications($threadId);

        return [
            'total_messages' => $communications->count(),
            'inbound_messages' => $communications->where('direction', 'inbound')->count(),
            'outbound_messages' => $communications->where('direction', 'outbound')->count(),
            'unread_messages' => $communications->where('status', 'delivered')->count(),
            'average_response_time' => $communications->whereNotNull('response_time')->avg('response_time'),
            'satisfaction_rating' => $communications->whereNotNull('satisfaction_rating')->avg('satisfaction_rating'),
            'communication_types' => $communications->pluck('communication_type')->countBy()->toArray(),
        ];
    }
}
