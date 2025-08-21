<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Support\Facades\Log;
use Fereydooni\Shopping\app\DTOs\CustomerWishlistDTO;
use Fereydooni\Shopping\app\Models\CustomerWishlist;
use Fereydooni\Shopping\app\Enums\WishlistPriority;

trait HasCustomerWishlistStatusManagement
{
    /**
     * Make wishlist item public
     */
    public function makeWishlistPublic(CustomerWishlist $wishlist): bool
    {
        try {
            $result = $this->repository->makePublic($wishlist);
            
            if ($result) {
                // Trigger event for wishlist made public
                event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\WishlistMadePublic($wishlist));
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to make wishlist public', [
                'wishlist_id' => $wishlist->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Make wishlist item private
     */
    public function makeWishlistPrivate(CustomerWishlist $wishlist): bool
    {
        try {
            $result = $this->repository->makePrivate($wishlist);
            
            if ($result) {
                // Trigger event for wishlist made private
                event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\WishlistMadePrivate($wishlist));
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to make wishlist private', [
                'wishlist_id' => $wishlist->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Set wishlist priority
     */
    public function setWishlistPriority(CustomerWishlist $wishlist, WishlistPriority $priority): bool
    {
        try {
            $oldPriority = $wishlist->priority;
            $result = $this->repository->setPriority($wishlist, $priority->value);
            
            if ($result && $oldPriority !== $priority) {
                // Trigger event for priority change
                event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\WishlistPriorityChanged($wishlist, $oldPriority, $priority));
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to set wishlist priority', [
                'wishlist_id' => $wishlist->id,
                'priority' => $priority->value,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Mark wishlist item as notified
     */
    public function markWishlistAsNotified(CustomerWishlist $wishlist): bool
    {
        try {
            $result = $this->repository->markAsNotified($wishlist);
            
            if ($result) {
                // Trigger event for wishlist marked as notified
                event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\WishlistMarkedAsNotified($wishlist));
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to mark wishlist as notified', [
                'wishlist_id' => $wishlist->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Mark wishlist item as not notified
     */
    public function markWishlistAsNotNotified(CustomerWishlist $wishlist): bool
    {
        try {
            return $this->repository->markAsNotNotified($wishlist);
        } catch (\Exception $e) {
            Log::error('Failed to mark wishlist as not notified', [
                'wishlist_id' => $wishlist->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Update current price for wishlist item
     */
    public function updateWishlistCurrentPrice(CustomerWishlist $wishlist, float $currentPrice): bool
    {
        try {
            $oldPrice = $wishlist->current_price;
            $result = $this->repository->updateCurrentPrice($wishlist, $currentPrice);
            
            if ($result) {
                // Trigger event for price update
                event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\WishlistPriceUpdated($wishlist, $oldPrice, $currentPrice));
                
                // Check for price drop
                if ($this->checkPriceDrop($wishlist)) {
                    event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\WishlistPriceDropDetected($wishlist));
                }
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to update wishlist current price', [
                'wishlist_id' => $wishlist->id,
                'current_price' => $currentPrice,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check for price drop in wishlist item
     */
    public function checkPriceDrop(CustomerWishlist $wishlist): bool
    {
        try {
            $hasPriceDrop = $this->repository->checkPriceDrop($wishlist);
            
            if ($hasPriceDrop && $wishlist->price_drop_notification && !$wishlist->is_notified) {
                // Trigger price drop event
                event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\WishlistPriceDropDetected($wishlist));
            }
            
            return $hasPriceDrop;
        } catch (\Exception $e) {
            Log::error('Failed to check price drop', [
                'wishlist_id' => $wishlist->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Validate wishlist status change
     */
    public function validateWishlistStatusChange(CustomerWishlist $wishlist, array $changes): bool
    {
        try {
            // Validate priority changes
            if (isset($changes['priority'])) {
                $priority = WishlistPriority::tryFrom($changes['priority']);
                if (!$priority) {
                    return false;
                }
            }

            // Validate boolean fields
            $booleanFields = ['is_public', 'is_notified', 'price_drop_notification'];
            foreach ($booleanFields as $field) {
                if (isset($changes[$field]) && !is_bool($changes[$field])) {
                    return false;
                }
            }

            // Validate price fields
            if (isset($changes['current_price']) && $changes['current_price'] < 0) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to validate wishlist status change', [
                'wishlist_id' => $wishlist->id,
                'changes' => $changes,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get wishlist status summary
     */
    public function getWishlistStatusSummary(CustomerWishlist $wishlist): array
    {
        return [
            'id' => $wishlist->id,
            'is_public' => $wishlist->is_public,
            'priority' => $wishlist->priority?->value,
            'priority_level' => $wishlist->priority?->level(),
            'is_notified' => $wishlist->is_notified,
            'notification_sent_at' => $wishlist->notification_sent_at,
            'price_drop_notification' => $wishlist->price_drop_notification,
            'has_price_drop' => $wishlist->has_price_drop,
            'price_drop_percentage' => $wishlist->price_drop_percentage,
            'price_drop_amount' => $wishlist->price_drop_amount,
            'should_send_notification' => $wishlist->should_send_notification,
        ];
    }

    /**
     * Get wishlist status changes history
     */
    public function getWishlistStatusChanges(CustomerWishlist $wishlist): array
    {
        // This would typically query a status changes log table
        // For now, return basic information
        return [
            'created_at' => $wishlist->created_at,
            'updated_at' => $wishlist->updated_at,
            'added_at' => $wishlist->added_at,
            'notification_sent_at' => $wishlist->notification_sent_at,
        ];
    }

    /**
     * Reset wishlist notification status
     */
    public function resetWishlistNotificationStatus(CustomerWishlist $wishlist): bool
    {
        try {
            return $this->repository->markAsNotNotified($wishlist);
        } catch (\Exception $e) {
            Log::error('Failed to reset wishlist notification status', [
                'wishlist_id' => $wishlist->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Enable price drop notifications for wishlist item
     */
    public function enablePriceDropNotifications(CustomerWishlist $wishlist): bool
    {
        try {
            $result = $this->repository->update($wishlist, [
                'price_drop_notification' => true,
                'is_notified' => false,
                'notification_sent_at' => null,
            ]);
            
            if ($result) {
                // Trigger event for notification settings changed
                event(new \Fereydooni\Shopping\app\Events\CustomerWishlist\WishlistPriceUpdated($wishlist, null, null));
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to enable price drop notifications', [
                'wishlist_id' => $wishlist->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Disable price drop notifications for wishlist item
     */
    public function disablePriceDropNotifications(CustomerWishlist $wishlist): bool
    {
        try {
            return $this->repository->update($wishlist, [
                'price_drop_notification' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to disable price drop notifications', [
                'wishlist_id' => $wishlist->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get wishlist items that need price drop notifications
     */
    public function getWishlistItemsNeedingNotifications(): array
    {
        try {
            $wishlists = $this->repository->findNotNotified();
            
            return $wishlists->filter(function ($wishlist) {
                return $wishlist->should_send_notification;
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get wishlist items needing notifications', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Process price drop notifications for all wishlist items
     */
    public function processPriceDropNotifications(): array
    {
        try {
            $results = [];
            $wishlists = $this->repository->findNotNotified();
            
            foreach ($wishlists as $wishlist) {
                if ($wishlist->should_send_notification) {
                    $success = $this->markWishlistAsNotified($wishlist);
                    $results[] = [
                        'wishlist_id' => $wishlist->id,
                        'customer_id' => $wishlist->customer_id,
                        'product_id' => $wishlist->product_id,
                        'price_drop_percentage' => $wishlist->price_drop_percentage,
                        'notification_sent' => $success,
                    ];
                }
            }
            
            return $results;
        } catch (\Exception $e) {
            Log::error('Failed to process price drop notifications', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
