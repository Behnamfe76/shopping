<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Fereydooni\Shopping\app\DTOs\UserSubscriptionDTO;

class UserSubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $userSubscription = $this->resource instanceof UserSubscriptionDTO ? $this->resource : UserSubscriptionDTO::fromModel($this->resource);

        return [
            'id' => $userSubscription->id,
            'user_id' => $userSubscription->user_id,
            'subscription_id' => $userSubscription->subscription_id,
            'order_id' => $userSubscription->order_id,
            'start_date' => $userSubscription->start_date,
            'end_date' => $userSubscription->end_date,
            'status' => $userSubscription->status,
            'status_label' => $userSubscription->getStatusLabel(),
            'status_color' => $userSubscription->getStatusColor(),
            'next_billing_date' => $userSubscription->next_billing_date,
            'created_at' => $userSubscription->created_at?->toISOString(),
            'updated_at' => $userSubscription->updated_at?->toISOString(),

            // Calculated fields
            'is_active' => $userSubscription->is_active,
            'is_trial' => $userSubscription->is_trial,
            'is_expired' => $userSubscription->is_expired,
            'is_cancelled' => $userSubscription->is_cancelled,
            'days_remaining' => $userSubscription->days_remaining,
            'days_until_next_billing' => $userSubscription->days_until_next_billing,

            // Formatted dates
            'formatted_start_date' => $userSubscription->getFormattedStartDate(),
            'formatted_end_date' => $userSubscription->getFormattedEndDate(),
            'formatted_next_billing_date' => $userSubscription->getFormattedNextBillingDate(),

            // Relationships (conditional inclusion)
            'user' => $this->when($userSubscription->user, function () use ($userSubscription) {
                return [
                    'id' => $userSubscription->user->id,
                    'name' => $userSubscription->user->name,
                    'email' => $userSubscription->user->email,
                ];
            }),

            'subscription' => $this->when($userSubscription->subscription, function () use ($userSubscription) {
                return [
                    'id' => $userSubscription->subscription->id,
                    'name' => $userSubscription->subscription->name,
                    'description' => $userSubscription->subscription->description,
                    'price' => $userSubscription->subscription->price,
                    'billing_cycle' => $userSubscription->subscription->billing_cycle,
                    'trial_days' => $userSubscription->subscription->trial_days,
                ];
            }),

            'order' => $this->when($userSubscription->order, function () use ($userSubscription) {
                return [
                    'id' => $userSubscription->order->id,
                    'order_number' => $userSubscription->order->order_number,
                    'total_amount' => $userSubscription->order->total_amount,
                    'status' => $userSubscription->order->status,
                ];
            }),

            // Links
            'links' => [
                'self' => route('api.v1.user-subscriptions.show', $userSubscription->id),
                'edit' => route('api.v1.user-subscriptions.update', $userSubscription->id),
                'delete' => route('api.v1.user-subscriptions.destroy', $userSubscription->id),
                'activate' => route('api.v1.user-subscriptions.activate', $userSubscription->id),
                'cancel' => route('api.v1.user-subscriptions.cancel', $userSubscription->id),
                'renew' => route('api.v1.user-subscriptions.renew', $userSubscription->id),
                'pause' => route('api.v1.user-subscriptions.pause', $userSubscription->id),
                'resume' => route('api.v1.user-subscriptions.resume', $userSubscription->id),
            ],
        ];
    }
}
