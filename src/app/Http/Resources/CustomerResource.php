<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Fereydooni\Shopping\app\DTOs\CustomerDTO;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // $customer = $this->resource instanceof CustomerDTO ? $this->resource : CustomerDTO::fromModel($this->resource);
        $customer = $this->resource;

        return [
            'id' => $customer->id,
            'user_id' => $customer->user_id,
            'customer_number' => $customer->customer_number,
            'customer_number_formatted' => $customer->customer_number_formatted,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'full_name' => $customer->full_name,
            'display_name' => $customer->display_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'date_of_birth' => Carbon::parse($customer->date_of_birth_unix),
            'age' => $customer->age,
            'gender' => $customer->gender,
            'gender_label' => $customer->gender?->label(),
            'company_name' => $customer->company_name,
            'tax_id' => $customer->tax_id,
            'customer_type' => $customer->customer_type,
            'customer_type_label' => __('customers.customer_types.' . $customer->customer_type->value),
            'status' => $customer->status,
            'status_label' => __('customers.statuses.' . $customer->status->value),
            'loyalty_points' => $customer->loyalty_points,
            'total_orders' => $customer->total_orders,
            'total_spent' => $customer->total_spent,
            'total_spent_formatted' => '$' . number_format($customer->total_spent, 2),
            'average_order_value' => $customer->average_order_value,
            'average_order_value_formatted' => '$' . number_format($customer->average_order_value, 2),
            'last_order_date' => $customer->last_order_date?->toISOString(),
            'first_order_date' => $customer->first_order_date?->toISOString(),
            'preferred_payment_method' => $customer->preferred_payment_method,
            'preferred_shipping_method' => $customer->preferred_shipping_method,
            'marketing_consent' => $customer->marketing_consent,
            'newsletter_subscription' => $customer->newsletter_subscription,
            'notes' => $customer->notes,
            'tags' => $customer->tags,
            'address_count' => $customer->address_count,
            'order_count' => $customer->order_count,
            'review_count' => $customer->review_count,
            'wishlist_count' => $customer->wishlist_count,
            'created_at' => $customer->created_at?->toISOString(),
            'updated_at' => $customer->updated_at?->toISOString(),

            // // Calculated fields
            // 'is_active' => $customer->is_active,
            // 'can_order' => $customer->can_order,
            // 'has_business_fields' => $customer->has_business_fields,
            // 'has_special_pricing' => $customer->has_special_pricing,
            // 'is_birthday_today' => $customer->is_birthday_today,
            // 'is_birthday_this_month' => $customer->is_birthday_this_month,
            // 'is_vip' => $customer->total_spent >= 10000, // Example VIP threshold
            // 'is_high_value' => $customer->total_spent >= 5000,
            // 'has_orders' => $customer->order_count > 0,
            // 'has_addresses' => $customer->address_count > 0,
            // 'has_reviews' => $customer->review_count > 0,
            // 'has_wishlist' => $customer->wishlist_count > 0,

            // // Formatted dates
            // 'formatted_date_of_birth' => $customer->date_of_birth?->format('Y-m-d'),
            // 'formatted_last_order_date' => $customer->last_order_date?->format('Y-m-d H:i:s'),
            // 'formatted_first_order_date' => $customer->first_order_date?->format('Y-m-d H:i:s'),
            // 'formatted_created_at' => $customer->created_at?->format('Y-m-d H:i:s'),

            // // Relationships (conditional inclusion)
            // 'user' => $this->when($customer->user, function () use ($customer) {
            //     return [
            //         'id' => $customer->user->id,
            //         'name' => $customer->user->name,
            //         'email' => $customer->user->email,
            //     ];
            // }),

            // 'addresses' => $this->when($customer->addresses, function () use ($customer) {
            //     return $customer->addresses->map(function ($address) {
            //         return [
            //             'id' => $address->id,
            //             'type' => $address->type,
            //             'street_address' => $address->street_address,
            //             'city' => $address->city,
            //             'state' => $address->state,
            //             'postal_code' => $address->postal_code,
            //             'country' => $address->country,
            //             'is_default' => $address->is_default,
            //         ];
            //     });
            // }),

            // 'orders' => $this->when($customer->orders, function () use ($customer) {
            //     return $customer->orders->map(function ($order) {
            //         return [
            //             'id' => $order->id,
            //             'order_number' => $order->order_number,
            //             'status' => $order->status,
            //             'total' => $order->total,
            //             'created_at' => $order->created_at?->toISOString(),
            //         ];
            //     });
            // }),

            // 'reviews' => $this->when($customer->reviews, function () use ($customer) {
            //     return $customer->reviews->map(function ($review) {
            //         return [
            //             'id' => $review->id,
            //             'product_id' => $review->product_id,
            //             'rating' => $review->rating,
            //             'comment' => $review->comment,
            //             'created_at' => $review->created_at?->toISOString(),
            //         ];
            //     });
            // }),

            // // Links
            // 'links' => [
            //     'self' => route('api.v1.customers.show', $customer->id),
            //     'edit' => route('api.v1.customers.update', $customer->id),
            //     'delete' => route('api.v1.customers.destroy', $customer->id),
            //     'activate' => route('api.v1.customers.activate', $customer->id),
            //     'deactivate' => route('api.v1.customers.deactivate', $customer->id),
            //     'suspend' => route('api.v1.customers.suspend', $customer->id),
            //     'loyalty_points' => route('api.v1.customers.loyalty-points', $customer->id),
            //     'orders' => route('api.v1.customers.orders', $customer->id),
            //     'addresses' => route('api.v1.customers.addresses', $customer->id),
            //     'reviews' => route('api.v1.customers.reviews', $customer->id),
            //     'wishlist' => route('api.v1.customers.wishlist', $customer->id),
            //     'analytics' => route('api.v1.customers.analytics', $customer->id),
            //     'add_note' => route('api.v1.customers.add-note', $customer->id),
            //     'notes' => route('api.v1.customers.notes', $customer->id),
            //     'update_preferences' => route('api.v1.customers.update-preferences', $customer->id),
            // ],

            // // Meta information
            // 'meta' => [
            //     'can_edit' => $request->user()?->can('update', $this->resource),
            //     'can_delete' => $request->user()?->can('delete', $this->resource),
            //     'can_activate' => $request->user()?->can('activate', $this->resource),
            //     'can_deactivate' => $request->user()?->can('deactivate', $this->resource),
            //     'can_suspend' => $request->user()?->can('suspend', $this->resource),
            //     'can_manage_loyalty_points' => $request->user()?->can('manageLoyaltyPoints', $this->resource),
            //     'can_view_analytics' => $request->user()?->can('viewAnalytics', $this->resource),
            //     'can_view_order_history' => $request->user()?->can('viewOrderHistory', $this->resource),
            //     'can_view_addresses' => $request->user()?->can('viewAddresses', $this->resource),
            //     'can_view_reviews' => $request->user()?->can('viewReviews', $this->resource),
            //     'can_view_wishlist' => $request->user()?->can('viewWishlist', $this->resource),
            //     'can_add_notes' => $request->user()?->can('addNotes', $this->resource),
            //     'can_view_notes' => $request->user()?->can('viewNotes', $this->resource),
            //     'can_update_preferences' => $request->user()?->can('updatePreferences', $this->resource),
            // ],
        ];
    }
}
