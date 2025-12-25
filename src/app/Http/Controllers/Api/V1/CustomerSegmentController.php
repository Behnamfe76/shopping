<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\CustomerSegmentDTO;
use App\Http\Controllers\Controller;
use App\Models\CustomerSegment;
use App\Services\CustomerSegmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CustomerSegmentController extends Controller
{
    protected CustomerSegmentService $service;

    public function __construct(CustomerSegmentService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of customer segments.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', CustomerSegment::class);

        $perPage = $request->get('per_page', 15);
        $segments = $this->service->paginate($perPage);

        return response()->json([
            'data' => $segments->items(),
            'pagination' => [
                'current_page' => $segments->currentPage(),
                'last_page' => $segments->lastPage(),
                'per_page' => $segments->perPage(),
                'total' => $segments->total(),
            ],
        ]);
    }

    /**
     * Store a newly created customer segment.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', CustomerSegment::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:customer_segments,name',
            'description' => 'nullable|string',
            'type' => 'required|string|in:demographic,behavioral,geographic,psychographic,transactional,engagement,loyalty,custom',
            'status' => 'nullable|string|in:active,inactive,draft,archived',
            'priority' => 'nullable|string|in:low,normal,high,critical',
            'criteria' => 'nullable|array',
            'conditions' => 'nullable|array',
            'is_automatic' => 'boolean',
            'is_dynamic' => 'boolean',
            'metadata' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $segment = $this->service->createSegment($request->all());

        return response()->json([
            'message' => 'Customer segment created successfully',
            'data' => $segment,
        ], 201);
    }

    /**
     * Display the specified customer segment.
     */
    public function show(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('view', $customerSegment);

        $segment = $this->service->findDTO($customerSegment->id);

        return response()->json([
            'data' => $segment,
        ]);
    }

    /**
     * Update the specified customer segment.
     */
    public function update(Request $request, CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('update', $customerSegment);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:customer_segments,name,'.$customerSegment->id,
            'description' => 'nullable|string',
            'type' => 'sometimes|required|string|in:demographic,behavioral,geographic,psychographic,transactional,engagement,loyalty,custom',
            'status' => 'nullable|string|in:active,inactive,draft,archived',
            'priority' => 'nullable|string|in:low,normal,high,critical',
            'criteria' => 'nullable|array',
            'conditions' => 'nullable|array',
            'is_automatic' => 'boolean',
            'is_dynamic' => 'boolean',
            'metadata' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $segment = $this->service->updateSegment($customerSegment, $request->all());

        return response()->json([
            'message' => 'Customer segment updated successfully',
            'data' => $segment,
        ]);
    }

    /**
     * Remove the specified customer segment.
     */
    public function destroy(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('delete', $customerSegment);

        $deleted = $this->service->deleteSegment($customerSegment);

        return response()->json([
            'message' => 'Customer segment deleted successfully',
        ]);
    }

    /**
     * Activate a customer segment.
     */
    public function activate(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('activate', $customerSegment);

        $activated = $this->service->activateSegment($customerSegment);

        return response()->json([
            'message' => 'Customer segment activated successfully',
            'activated' => $activated,
        ]);
    }

    /**
     * Deactivate a customer segment.
     */
    public function deactivate(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('deactivate', $customerSegment);

        $deactivated = $this->service->deactivateSegment($customerSegment);

        return response()->json([
            'message' => 'Customer segment deactivated successfully',
            'deactivated' => $deactivated,
        ]);
    }

    /**
     * Make a customer segment automatic.
     */
    public function makeAutomatic(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('makeAutomatic', $customerSegment);

        $updated = $this->service->makeAutomatic($customerSegment);

        return response()->json([
            'message' => 'Customer segment made automatic successfully',
            'updated' => $updated,
        ]);
    }

    /**
     * Make a customer segment manual.
     */
    public function makeManual(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('makeManual', $customerSegment);

        $updated = $this->service->makeManual($customerSegment);

        return response()->json([
            'message' => 'Customer segment made manual successfully',
            'updated' => $updated,
        ]);
    }

    /**
     * Make a customer segment dynamic.
     */
    public function makeDynamic(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('makeDynamic', $customerSegment);

        $updated = $this->service->makeDynamic($customerSegment);

        return response()->json([
            'message' => 'Customer segment made dynamic successfully',
            'updated' => $updated,
        ]);
    }

    /**
     * Make a customer segment static.
     */
    public function makeStatic(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('makeStatic', $customerSegment);

        $updated = $this->service->makeStatic($customerSegment);

        return response()->json([
            'message' => 'Customer segment made static successfully',
            'updated' => $updated,
        ]);
    }

    /**
     * Set the priority of a customer segment.
     */
    public function setPriority(Request $request, CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('setPriority', $customerSegment);

        $validator = Validator::make($request->all(), [
            'priority' => 'required|string|in:low,normal,high,critical',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $updated = $this->service->setPriority($customerSegment, $request->priority);

        return response()->json([
            'message' => 'Customer segment priority updated successfully',
            'updated' => $updated,
        ]);
    }

    /**
     * Calculate customers for a customer segment.
     */
    public function calculate(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('calculateCustomers', $customerSegment);

        $count = $this->service->calculateSegmentCustomers($customerSegment);

        return response()->json([
            'message' => 'Customer segment calculated successfully',
            'customer_count' => $count,
        ]);
    }

    /**
     * Add a customer to a customer segment.
     */
    public function addCustomer(Request $request, CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('addCustomer', $customerSegment);

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $added = $this->service->addCustomerToSegment($customerSegment, $request->customer_id);

        return response()->json([
            'message' => 'Customer added to segment successfully',
            'added' => $added,
        ]);
    }

    /**
     * Remove a customer from a customer segment.
     */
    public function removeCustomer(Request $request, CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('removeCustomer', $customerSegment);

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $removed = $this->service->removeCustomerFromSegment($customerSegment, $request->customer_id);

        return response()->json([
            'message' => 'Customer removed from segment successfully',
            'removed' => $removed,
        ]);
    }

    /**
     * Update the criteria of a customer segment.
     */
    public function updateCriteria(Request $request, CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('updateCriteria', $customerSegment);

        $validator = Validator::make($request->all(), [
            'criteria' => 'required|array',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $updated = $this->service->updateSegmentCriteria($customerSegment, $request->criteria);

        return response()->json([
            'message' => 'Customer segment criteria updated successfully',
            'updated' => $updated,
        ]);
    }

    /**
     * Update the conditions of a customer segment.
     */
    public function updateConditions(Request $request, CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('updateConditions', $customerSegment);

        $validator = Validator::make($request->all(), [
            'conditions' => 'required|array',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $updated = $this->service->updateSegmentConditions($customerSegment, $request->conditions);

        return response()->json([
            'message' => 'Customer segment conditions updated successfully',
            'updated' => $updated,
        ]);
    }

    /**
     * Get customers in a customer segment.
     */
    public function customers(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('view', $customerSegment);

        $customers = $customerSegment->customers()->paginate(15);

        return response()->json([
            'data' => $customers->items(),
            'pagination' => [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
            ],
        ]);
    }

    /**
     * Get analytics for a customer segment.
     */
    public function analytics(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('viewAnalytics', $customerSegment);

        $analytics = $this->service->getSegmentAnalytics($customerSegment->id);

        return response()->json([
            'data' => $analytics,
        ]);
    }

    /**
     * Duplicate a customer segment.
     */
    public function duplicate(Request $request, CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('duplicate', $customerSegment);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:customer_segments,name',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $newSegment = $this->service->duplicateSegment($customerSegment, $request->name);

        return response()->json([
            'message' => 'Customer segment duplicated successfully',
            'data' => CustomerSegmentDTO::fromModel($newSegment),
        ], 201);
    }

    /**
     * Merge customer segments.
     */
    public function merge(Request $request): JsonResponse
    {
        $this->authorize('merge', CustomerSegment::class);

        $validator = Validator::make($request->all(), [
            'segment_ids' => 'required|array|min:2',
            'segment_ids.*' => 'integer|exists:customer_segments,id',
            'name' => 'required|string|max:255|unique:customer_segments,name',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $mergedSegment = $this->service->mergeSegments($request->segment_ids, $request->name);

        return response()->json([
            'message' => 'Customer segments merged successfully',
            'data' => CustomerSegmentDTO::fromModel($mergedSegment),
        ], 201);
    }

    /**
     * Split a customer segment.
     */
    public function split(Request $request, CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('split', $customerSegment);

        $validator = Validator::make($request->all(), [
            'criteria' => 'required|array|min:1',
            'criteria.*.name' => 'required|string|max:255',
            'criteria.*.conditions' => 'required|array',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $newSegments = $this->service->splitSegment($customerSegment, $request->criteria);

        return response()->json([
            'message' => 'Customer segment split successfully',
            'data' => $newSegments->map(fn ($segment) => CustomerSegmentDTO::fromModel($segment)),
        ], 201);
    }

    /**
     * Get segment statistics.
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('viewAnalytics', CustomerSegment::class);

        $statistics = $this->service->getSegmentStatistics();

        return response()->json([
            'data' => $statistics,
        ]);
    }

    /**
     * Get segment recommendations.
     */
    public function recommendations(): JsonResponse
    {
        $this->authorize('viewAnalytics', CustomerSegment::class);

        $recommendations = $this->service->getSegmentRecommendations();

        return response()->json([
            'data' => $recommendations,
        ]);
    }

    /**
     * Get segment insights.
     */
    public function insights(): JsonResponse
    {
        $this->authorize('viewAnalytics', CustomerSegment::class);

        $insights = $this->service->getSegmentInsights();

        return response()->json([
            'data' => $insights,
        ]);
    }

    /**
     * Get segment trends forecast.
     */
    public function trendsForecast(Request $request): JsonResponse
    {
        $this->authorize('viewAnalytics', CustomerSegment::class);

        $period = $request->get('period', 'monthly');
        $forecast = $this->service->getSegmentTrendsForecast($period);

        return response()->json([
            'data' => $forecast,
        ]);
    }

    /**
     * Compare two customer segments.
     */
    public function compare(Request $request): JsonResponse
    {
        $this->authorize('viewAnalytics', CustomerSegment::class);

        $validator = Validator::make($request->all(), [
            'segment_id_1' => 'required|integer|exists:customer_segments,id',
            'segment_id_2' => 'required|integer|exists:customer_segments,id|different:segment_id_1',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $comparison = $this->service->compareSegments($request->segment_id_1, $request->segment_id_2);

        return response()->json([
            'data' => $comparison,
        ]);
    }

    /**
     * Get forecast for a customer segment.
     */
    public function forecast(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('viewAnalytics', $customerSegment);

        $forecast = $this->service->getSegmentForecast($customerSegment->id);

        return response()->json([
            'data' => $forecast,
        ]);
    }

    /**
     * Export a customer segment.
     */
    public function export(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('exportData', $customerSegment);

        $exportData = $this->service->exportSegment($customerSegment);

        return response()->json([
            'data' => $exportData,
        ]);
    }

    /**
     * Import a customer segment.
     */
    public function import(Request $request): JsonResponse
    {
        $this->authorize('importData', CustomerSegment::class);

        $validator = Validator::make($request->all(), [
            'segment' => 'required|array',
            'segment.name' => 'required|string|max:255|unique:customer_segments,name',
            'segment.type' => 'required|string|in:demographic,behavioral,geographic,psychographic,transactional,engagement,loyalty,custom',
            'customers' => 'nullable|array',
            'customers.*' => 'integer|exists:customers,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $segment = $this->service->importSegment($request->all());

        return response()->json([
            'message' => 'Customer segment imported successfully',
            'data' => CustomerSegmentDTO::fromModel($segment),
        ], 201);
    }

    /**
     * Recalculate all automatic segments.
     */
    public function recalculateAll(): JsonResponse
    {
        $this->authorize('recalculateAll', CustomerSegment::class);

        $success = $this->service->recalculateAllSegments();

        return response()->json([
            'message' => 'All automatic segments recalculated successfully',
            'success' => $success,
        ]);
    }

    /**
     * Get segments needing recalculation.
     */
    public function needingRecalculation(): JsonResponse
    {
        $this->authorize('viewAnalytics', CustomerSegment::class);

        $segments = $this->service->getSegmentsNeedingRecalculation();

        return response()->json([
            'data' => $segments->map(fn ($segment) => CustomerSegmentDTO::fromModel($segment)),
        ]);
    }

    /**
     * Get overlapping segments.
     */
    public function overlapping(CustomerSegment $customerSegment): JsonResponse
    {
        $this->authorize('viewAnalytics', $customerSegment);

        $overlapping = $this->service->getOverlappingSegments($customerSegment);

        return response()->json([
            'data' => $overlapping->map(fn ($segment) => CustomerSegmentDTO::fromModel($segment)),
        ]);
    }
}
