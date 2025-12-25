<?php

namespace Fereydooni\Shopping\app\Traits;

trait HasVoteManagement
{
    /**
     * Increment helpful votes for an item
     */
    public function incrementHelpfulVotes($item): bool
    {
        if (method_exists($item, 'increment')) {
            return $item->increment('helpful_votes');
        }

        return false;
    }

    /**
     * Decrement helpful votes for an item
     */
    public function decrementHelpfulVotes($item): bool
    {
        if (method_exists($item, 'decrement')) {
            return $item->decrement('helpful_votes');
        }

        return false;
    }

    /**
     * Add a vote to an item
     */
    public function addVote($item, bool $isHelpful): bool
    {
        if (method_exists($item, 'increment')) {
            $item->increment('total_votes');

            if ($isHelpful) {
                $item->increment('helpful_votes');
            }

            return true;
        }

        return false;
    }

    /**
     * Remove a vote from an item
     */
    public function removeVote($item, int $userId): bool
    {
        // This would typically involve a separate votes table
        // For now, we'll just return true
        return true;
    }

    /**
     * Get helpful percentage for an item
     */
    public function getHelpfulPercentage($item): float
    {
        if (! property_exists($item, 'helpful_votes') || ! property_exists($item, 'total_votes')) {
            return 0.0;
        }

        if ($item->total_votes === 0) {
            return 0.0;
        }

        return round(($item->helpful_votes / $item->total_votes) * 100, 2);
    }

    /**
     * Check if an item has votes
     */
    public function hasVotes($item): bool
    {
        return property_exists($item, 'total_votes') && $item->total_votes > 0;
    }

    /**
     * Get vote statistics for an item
     */
    public function getVoteStats($item): array
    {
        return [
            'total_votes' => $item->total_votes ?? 0,
            'helpful_votes' => $item->helpful_votes ?? 0,
            'helpful_percentage' => $this->getHelpfulPercentage($item),
            'has_votes' => $this->hasVotes($item),
        ];
    }
}
