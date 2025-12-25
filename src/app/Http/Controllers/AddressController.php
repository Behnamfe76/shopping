<?php

namespace Fereydooni\Shopping\app\Http\Controllers;

use Fereydooni\Shopping\app\Enums\AddressType;
use Fereydooni\Shopping\app\Facades\Address as AddressFacade;
use Fereydooni\Shopping\app\Http\Requests\SearchAddressRequest;
use Fereydooni\Shopping\app\Http\Requests\SetDefaultAddressRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreAddressRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateAddressRequest;
use Fereydooni\Shopping\app\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    /**
     * Display a listing of addresses.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Address::class);

        $perPage = $request->get('per_page', 15);
        $paginationType = $request->get('pagination', 'regular');
        $type = $request->get('type');

        $addresses = match ($paginationType) {
            'simplePaginate' => AddressFacade::simplePaginateByUser(auth()->id(), $perPage),
            'cursorPaginate' => AddressFacade::cursorPaginateByUser(auth()->id(), $perPage),
            default => AddressFacade::paginateByUser(auth()->id(), $perPage),
        };

        $stats = AddressFacade::getAddressStats(auth()->id());

        return view('shopping::addresses.index', compact('addresses', 'stats', 'type'));
    }

    /**
     * Show the form for creating a new address.
     */
    public function create(): View
    {
        $this->authorize('create', Address::class);

        return view('shopping::addresses.create');
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $this->authorize('create', Address::class);

        try {
            $addressDTO = AddressFacade::createDTO($request->validated());

            return redirect()
                ->route('shopping.addresses.show', $addressDTO->id)
                ->with('success', 'Address created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create address: '.$e->getMessage());
        }
    }

    /**
     * Display the specified address.
     */
    public function show(Address $address): View
    {
        $this->authorize('view', $address);

        $addressDTO = AddressFacade::findDTO($address->id);

        return view('shopping::addresses.show', compact('addressDTO'));
    }

    /**
     * Show the form for editing the specified address.
     */
    public function edit(Address $address): View
    {
        $this->authorize('update', $address);

        $addressDTO = AddressFacade::findDTO($address->id);

        return view('shopping::addresses.edit', compact('addressDTO'));
    }

    /**
     * Update the specified address in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        $this->authorize('update', $address);

        try {
            $addressDTO = AddressFacade::updateDTO($address, $request->validated());

            if (! $addressDTO) {
                throw new \Exception('Failed to update address.');
            }

            return redirect()
                ->route('shopping.addresses.show', $addressDTO->id)
                ->with('success', 'Address updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update address: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Address $address): RedirectResponse
    {
        $this->authorize('delete', $address);

        try {
            $deleted = AddressFacade::delete($address);

            if (! $deleted) {
                throw new \Exception('Failed to delete address.');
            }

            return redirect()
                ->route('shopping.addresses.index')
                ->with('success', 'Address deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete address: '.$e->getMessage());
        }
    }

    /**
     * Set the address as default.
     */
    public function setDefault(SetDefaultAddressRequest $request, Address $address): RedirectResponse
    {
        $this->authorize('setDefault', $address);

        try {
            $addressDTO = AddressFacade::setDefaultDTO($address);

            if (! $addressDTO) {
                throw new \Exception('Failed to set address as default.');
            }

            return back()->with('success', 'Address set as default successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to set address as default: '.$e->getMessage());
        }
    }

    /**
     * Search addresses.
     */
    public function search(SearchAddressRequest $request): View|JsonResponse
    {
        $this->authorize('search', Address::class);

        $query = $request->get('query');
        $type = $request->get('type');
        $paginationType = $request->get('pagination', 'regular');
        $perPage = $request->get('per_page', 15);

        try {
            if ($request->expectsJson()) {
                $addresses = AddressFacade::search($query, auth()->id(), $type);

                return response()->json([
                    'addresses' => $addresses,
                    'count' => $addresses->count(),
                ]);
            }

            $addresses = AddressFacade::searchWithPagination(
                $query,
                $perPage,
                auth()->id(),
                $type,
                $paginationType
            );

            return view('shopping::addresses.search', compact('addresses', 'query', 'type'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Search failed: '.$e->getMessage());
        }
    }

    /**
     * Get address statistics.
     */
    public function stats(): JsonResponse
    {
        $this->authorize('viewStats', Address::class);

        try {
            $stats = AddressFacade::getAddressStats(auth()->id());
            $statsByType = AddressFacade::getAddressStatsByType(auth()->id());

            return response()->json([
                'stats' => $stats,
                'stats_by_type' => $statsByType,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get default addresses.
     */
    public function defaults(): JsonResponse
    {
        $this->authorize('viewAny', Address::class);

        try {
            $defaultAddresses = AddressFacade::getDefaultItemsDTO(auth()->id());

            return response()->json([
                'default_addresses' => $defaultAddresses,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get addresses by type.
     */
    public function byType(Request $request, string $type): View|JsonResponse
    {
        $addressType = AddressType::tryFrom($type);

        if (! $addressType) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Invalid address type.'], 400);
            }

            return back()->with('error', 'Invalid address type.');
        }

        $this->authorize('accessByType', $addressType);

        try {
            $addresses = AddressFacade::findByUserAndTypeDTO(auth()->id(), $addressType);
            $stats = AddressFacade::getAddressStatsByType(auth()->id());

            if ($request->expectsJson()) {
                return response()->json([
                    'addresses' => $addresses,
                    'type' => $type,
                    'stats' => $stats[$type] ?? null,
                ]);
            }

            return view('shopping::addresses.by-type', compact('addresses', 'type', 'stats'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Failed to load addresses: '.$e->getMessage());
        }
    }
}
