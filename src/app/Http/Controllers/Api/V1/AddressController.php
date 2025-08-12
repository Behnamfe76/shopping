<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Fereydooni\Shopping\app\Models\Address;
use Fereydooni\Shopping\app\DTOs\AddressDTO;
use Fereydooni\Shopping\app\Enums\AddressType;
use Fereydooni\Shopping\app\Facades\Address as AddressFacade;
use Fereydooni\Shopping\app\Http\Requests\StoreAddressRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateAddressRequest;
use Fereydooni\Shopping\app\Http\Requests\SetDefaultAddressRequest;
use Fereydooni\Shopping\app\Http\Requests\SearchAddressRequest;
use Fereydooni\Shopping\app\Http\Resources\AddressResource;
use Fereydooni\Shopping\app\Http\Resources\AddressCollection;
use Fereydooni\Shopping\app\Http\Resources\AddressSearchResource;

class AddressController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of addresses.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Address::class);

        try {
            $perPage = $request->get('per_page', 15);
            $paginationType = $request->get('pagination', 'regular');
            $type = $request->get('type');

            $addresses = match($paginationType) {
                'simple' => AddressFacade::simplePaginateByUser(auth()->id(), $perPage),
                'cursor' => AddressFacade::cursorPaginateByUser(auth()->id(), $perPage),
                default => AddressFacade::paginateByUser(auth()->id(), $perPage),
            };

            return (new AddressCollection($addresses))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve addresses',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $this->authorize('create', Address::class);

        try {
            $addressDTO = AddressFacade::createDTO($request->validated());

            return (new AddressResource($addressDTO))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create address',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified address.
     */
    public function show(Address $address): JsonResponse
    {
        $this->authorize('view', $address);

        try {
            $addressDTO = AddressFacade::findDTO($address->id);

            if (!$addressDTO) {
                return response()->json([
                    'error' => 'Address not found',
                ], 404);
            }

            return (new AddressResource($addressDTO))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve address',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified address in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        $this->authorize('update', $address);

        try {
            $addressDTO = AddressFacade::updateDTO($address, $request->validated());

            if (!$addressDTO) {
                return response()->json([
                    'error' => 'Failed to update address',
                ], 500);
            }

            return (new AddressResource($addressDTO))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update address',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Address $address): JsonResponse
    {
        $this->authorize('delete', $address);

        try {
            $deleted = AddressFacade::delete($address);

            if (!$deleted) {
                return response()->json([
                    'error' => 'Failed to delete address',
                ], 500);
            }

            return response()->json([
                'message' => 'Address deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete address',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set the address as default.
     */
    public function setDefault(SetDefaultAddressRequest $request, Address $address): JsonResponse
    {
        $this->authorize('setDefault', $address);

        try {
            $addressDTO = AddressFacade::setDefaultDTO($address);

            if (!$addressDTO) {
                return response()->json([
                    'error' => 'Failed to set address as default',
                ], 500);
            }

            return (new AddressResource($addressDTO))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to set address as default',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search addresses.
     */
    public function search(SearchAddressRequest $request): JsonResponse
    {
        $this->authorize('search', Address::class);

        try {
            $query = $request->get('query');
            $type = $request->get('type');
            $paginationType = $request->get('pagination', 'regular');
            $perPage = $request->get('per_page', 15);

            $startTime = microtime(true);

            $addresses = AddressFacade::searchWithPagination(
                $query,
                $perPage,
                auth()->id(),
                $type,
                $paginationType
            );

            $searchTime = microtime(true) - $startTime;

            $searchMetadata = [
                'search_time' => round($searchTime, 4),
                'filters' => [
                    'type' => $type,
                    'pagination' => $paginationType,
                    'per_page' => $perPage,
                ],
            ];

            return (new AddressSearchResource($addresses, $query, $searchMetadata))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Search failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get default address by type.
     */
    public function getDefault(Request $request, string $type): JsonResponse
    {
        $addressType = AddressType::tryFrom($type);

        if (!$addressType) {
            return response()->json([
                'error' => 'Invalid address type',
                'message' => 'Address type must be either "billing" or "shipping"',
            ], 400);
        }

        $this->authorize('viewAny', Address::class);

        try {
            $addressDTO = AddressFacade::getDefaultDTOByUser(auth()->id(), $addressType);

            if (!$addressDTO) {
                return response()->json([
                    'error' => 'No default address found',
                    'message' => "No default {$type} address found for this user",
                ], 404);
            }

            return (new AddressResource($addressDTO))->response();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve default address',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get address count.
     */
    public function getCount(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Address::class);

        try {
            $stats = AddressFacade::getAddressStats(auth()->id());
            $statsByType = AddressFacade::getAddressStatsByType(auth()->id());

            return response()->json([
                'data' => [
                    'total_addresses' => $stats['total_addresses'],
                    'default_addresses' => $stats['default_addresses'],
                    'billing_addresses' => $stats['billing_addresses'],
                    'shipping_addresses' => $stats['shipping_addresses'],
                    'by_type' => $statsByType,
                ],
                'meta' => [
                    'type' => 'address_count',
                    'version' => '1.0',
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve address count',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
