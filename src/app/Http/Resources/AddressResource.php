<?php

namespace Fereydooni\Shopping\app\Http\Resources;

use Fereydooni\Shopping\app\DTOs\AddressDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $address = $this->resource instanceof AddressDTO ? $this->resource : AddressDTO::fromModel($this->resource);

        $data = [
            'id' => $address->id,
            'user_id' => $address->user_id,
            'first_name' => $address->first_name,
            'last_name' => $address->last_name,
            'company_name' => $address->company_name,
            'address_line_1' => $address->address_line_1,
            'address_line_2' => $address->address_line_2,
            'city' => $address->city,
            'state' => $address->state,
            'postal_code' => $address->postal_code,
            'country' => $address->country,
            'phone' => $address->phone,
            'email' => $address->email,
            'type' => $address->type->value,
            'type_label' => $address->type->label(),
            'is_default' => $address->is_default,
            'created_at' => $address->created_at?->toISOString(),
            'updated_at' => $address->updated_at?->toISOString(),
        ];

        // Add computed fields
        $data['full_name'] = $address->full_name;
        $data['full_address'] = $address->full_address;
        $data['formatted_address'] = $address->formatted_address;

        // Add conditional fields based on user permissions
        if ($request->user() && $request->user()->can('address.view.any')) {
            $data['user'] = [
                'id' => $address->user_id,
                // Add more user fields if needed
            ];
        }

        // Add type-specific indicators
        $data['is_billing'] = $address->type->value === 'billing';
        $data['is_shipping'] = $address->type->value === 'shipping';

        // Add default address indicators
        if ($address->is_default) {
            $data['default_indicator'] = 'This is your default '.$address->type->value.' address';
        }

        // Add links for API navigation
        $data['links'] = [
            'self' => route('api.v1.addresses.show', $address->id),
            'edit' => route('api.v1.addresses.update', $address->id),
            'delete' => route('api.v1.addresses.destroy', $address->id),
        ];

        // Add actions based on user permissions
        $data['actions'] = $this->getAvailableActions($request, $address);

        return $data;
    }

    /**
     * Get available actions for the address based on user permissions.
     */
    private function getAvailableActions(Request $request, AddressDTO $address): array
    {
        $actions = [];

        if ($request->user()) {
            $user = $request->user();

            // Update action
            if ($user->can('update', $this->resource)) {
                $actions['update'] = [
                    'method' => 'PUT',
                    'url' => route('api.v1.addresses.update', $address->id),
                    'label' => 'Update Address',
                ];
            }

            // Delete action
            if ($user->can('delete', $this->resource)) {
                $actions['delete'] = [
                    'method' => 'DELETE',
                    'url' => route('api.v1.addresses.destroy', $address->id),
                    'label' => 'Delete Address',
                ];
            }

            // Set as default action
            if (! $address->is_default && $user->can('setDefault', $this->resource)) {
                $actions['set_default'] = [
                    'method' => 'POST',
                    'url' => route('api.v1.addresses.set-default', $address->id),
                    'label' => 'Set as Default',
                ];
            }

            // Unset as default action
            if ($address->is_default && $user->can('unsetDefault', $this->resource)) {
                $actions['unset_default'] = [
                    'method' => 'DELETE',
                    'url' => route('api.v1.addresses.unset-default', $address->id),
                    'label' => 'Remove Default Status',
                ];
            }
        }

        return $actions;
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'type' => 'address',
                'version' => '1.0',
                'timestamp' => now()->toISOString(),
            ],
        ];
    }
}
