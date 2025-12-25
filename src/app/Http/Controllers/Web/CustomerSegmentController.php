<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CustomerSegment;
use App\Services\CustomerSegmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerSegmentController extends Controller
{
    protected CustomerSegmentService $service;

    public function __construct(CustomerSegmentService $service)
    {
        $this->service = $service;
    }

    /**
     * Display customer segment dashboard.
     */
    public function dashboard()
    {
        $this->authorize('viewAny', CustomerSegment::class);

        $statistics = $this->service->getSegmentStatistics();
        $recentSegments = $this->service->getRecentSegmentsDTO(5);
        $segmentsNeedingRecalculation = $this->service->getSegmentsNeedingRecalculation();
        $recommendations = $this->service->getSegmentRecommendations();

        return view('customer-segments.dashboard', compact(
            'statistics',
            'recentSegments',
            'segmentsNeedingRecalculation',
            'recommendations'
        ));
    }

    /**
     * Display a listing of customer segments.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CustomerSegment::class);

        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $type = $request->get('type');
        $status = $request->get('status');
        $priority = $request->get('priority');

        $query = CustomerSegment::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        $segments = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('customer-segments.index', compact('segments', 'search', 'type', 'status', 'priority'));
    }

    /**
     * Show the form for creating a new customer segment.
     */
    public function create()
    {
        $this->authorize('create', CustomerSegment::class);

        return view('customer-segments.create');
    }

    /**
     * Store a newly created customer segment.
     */
    public function store(Request $request)
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $segment = $this->service->createSegment($request->all());

            return redirect()->route('customer-segments.show', $segment->id)
                ->with('success', 'Customer segment created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create customer segment: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified customer segment.
     */
    public function show(CustomerSegment $customerSegment)
    {
        $this->authorize('view', $customerSegment);

        $segment = $this->service->findDTO($customerSegment->id);
        $analytics = $this->service->getSegmentAnalytics($customerSegment->id);
        $customers = $customerSegment->customers()->paginate(15);
        $overlappingSegments = $this->service->getOverlappingSegments($customerSegment);

        return view('customer-segments.show', compact('segment', 'analytics', 'customers', 'overlappingSegments'));
    }

    /**
     * Show the form for editing the specified customer segment.
     */
    public function edit(CustomerSegment $customerSegment)
    {
        $this->authorize('update', $customerSegment);

        return view('customer-segments.edit', compact('customerSegment'));
    }

    /**
     * Update the specified customer segment.
     */
    public function update(Request $request, CustomerSegment $customerSegment)
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $segment = $this->service->updateSegment($customerSegment, $request->all());

            return redirect()->route('customer-segments.show', $customerSegment)
                ->with('success', 'Customer segment updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update customer segment: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified customer segment.
     */
    public function destroy(CustomerSegment $customerSegment)
    {
        $this->authorize('delete', $customerSegment);

        try {
            $this->service->deleteSegment($customerSegment);

            return redirect()->route('customer-segments.index')
                ->with('success', 'Customer segment deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete customer segment: '.$e->getMessage()]);
        }
    }

    /**
     * Show segment criteria builder interface.
     */
    public function criteriaBuilder(CustomerSegment $customerSegment)
    {
        $this->authorize('updateCriteria', $customerSegment);

        return view('customer-segments.criteria-builder', compact('customerSegment'));
    }

    /**
     * Update segment criteria.
     */
    public function updateCriteria(Request $request, CustomerSegment $customerSegment)
    {
        $this->authorize('updateCriteria', $customerSegment);

        $validator = Validator::make($request->all(), [
            'criteria' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updated = $this->service->updateSegmentCriteria($customerSegment, $request->criteria);

            return redirect()->route('customer-segments.show', $customerSegment)
                ->with('success', 'Segment criteria updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update criteria: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show segment calculation interface.
     */
    public function calculate(CustomerSegment $customerSegment)
    {
        $this->authorize('calculateCustomers', $customerSegment);

        try {
            $count = $this->service->calculateSegmentCustomers($customerSegment);

            return redirect()->route('customer-segments.show', $customerSegment)
                ->with('success', "Segment calculated successfully. Found {$count} customers.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to calculate segment: '.$e->getMessage()]);
        }
    }

    /**
     * Show segment analytics and reporting.
     */
    public function analytics()
    {
        $this->authorize('viewAnalytics', CustomerSegment::class);

        $statistics = $this->service->getSegmentStatistics();
        $insights = $this->service->getSegmentInsights();
        $recommendations = $this->service->getSegmentRecommendations();
        $trends = $this->service->getSegmentTrendsForecast();

        return view('customer-segments.analytics', compact('statistics', 'insights', 'recommendations', 'trends'));
    }

    /**
     * Show segment comparison interface.
     */
    public function compare(Request $request)
    {
        $this->authorize('viewAnalytics', CustomerSegment::class);

        $segmentId1 = $request->get('segment_id_1');
        $segmentId2 = $request->get('segment_id_2');

        $comparison = null;
        if ($segmentId1 && $segmentId2) {
            $comparison = $this->service->compareSegments($segmentId1, $segmentId2);
        }

        $segments = CustomerSegment::orderBy('name')->get();

        return view('customer-segments.compare', compact('segments', 'comparison', 'segmentId1', 'segmentId2'));
    }

    /**
     * Show segment import/export interface.
     */
    public function importExport()
    {
        $this->authorize('viewAny', CustomerSegment::class);

        return view('customer-segments.import-export');
    }

    /**
     * Import segments.
     */
    public function import(Request $request)
    {
        $this->authorize('importData', CustomerSegment::class);

        $validator = Validator::make($request->all(), [
            'import_file' => 'required|file|mimes:json,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('import_file');
            $data = json_decode($file->getContents(), true);

            if (! $data) {
                throw new \Exception('Invalid file format');
            }

            $segment = $this->service->importSegment($data);

            return redirect()->route('customer-segments.show', $segment)
                ->with('success', 'Segment imported successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to import segment: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Export segment.
     */
    public function export(CustomerSegment $customerSegment)
    {
        $this->authorize('exportData', $customerSegment);

        try {
            $exportData = $this->service->exportSegment($customerSegment);

            return response()->json($exportData)
                ->header('Content-Disposition', 'attachment; filename="segment-'.$customerSegment->id.'.json"');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to export segment: '.$e->getMessage()]);
        }
    }

    /**
     * Show segment performance tracking.
     */
    public function performance(CustomerSegment $customerSegment)
    {
        $this->authorize('viewAnalytics', $customerSegment);

        $analytics = $this->service->getSegmentAnalytics($customerSegment->id);
        $forecast = $this->service->getSegmentForecast($customerSegment->id);

        return view('customer-segments.performance', compact('customerSegment', 'analytics', 'forecast'));
    }

    /**
     * Activate segment.
     */
    public function activate(CustomerSegment $customerSegment)
    {
        $this->authorize('activate', $customerSegment);

        try {
            $this->service->activateSegment($customerSegment);

            return redirect()->back()
                ->with('success', 'Segment activated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to activate segment: '.$e->getMessage()]);
        }
    }

    /**
     * Deactivate segment.
     */
    public function deactivate(CustomerSegment $customerSegment)
    {
        $this->authorize('deactivate', $customerSegment);

        try {
            $this->service->deactivateSegment($customerSegment);

            return redirect()->back()
                ->with('success', 'Segment deactivated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to deactivate segment: '.$e->getMessage()]);
        }
    }

    /**
     * Duplicate segment.
     */
    public function duplicate(Request $request, CustomerSegment $customerSegment)
    {
        $this->authorize('duplicate', $customerSegment);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:customer_segments,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $newSegment = $this->service->duplicateSegment($customerSegment, $request->name);

            return redirect()->route('customer-segments.show', $newSegment)
                ->with('success', 'Segment duplicated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to duplicate segment: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show merge segments interface.
     */
    public function mergeForm()
    {
        $this->authorize('merge', CustomerSegment::class);

        $segments = CustomerSegment::orderBy('name')->get();

        return view('customer-segments.merge', compact('segments'));
    }

    /**
     * Merge segments.
     */
    public function merge(Request $request)
    {
        $this->authorize('merge', CustomerSegment::class);

        $validator = Validator::make($request->all(), [
            'segment_ids' => 'required|array|min:2',
            'segment_ids.*' => 'integer|exists:customer_segments,id',
            'name' => 'required|string|max:255|unique:customer_segments,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $mergedSegment = $this->service->mergeSegments($request->segment_ids, $request->name);

            return redirect()->route('customer-segments.show', $mergedSegment)
                ->with('success', 'Segments merged successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to merge segments: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show split segment interface.
     */
    public function splitForm(CustomerSegment $customerSegment)
    {
        $this->authorize('split', $customerSegment);

        return view('customer-segments.split', compact('customerSegment'));
    }

    /**
     * Split segment.
     */
    public function split(Request $request, CustomerSegment $customerSegment)
    {
        $this->authorize('split', $customerSegment);

        $validator = Validator::make($request->all(), [
            'criteria' => 'required|array|min:1',
            'criteria.*.name' => 'required|string|max:255',
            'criteria.*.conditions' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $newSegments = $this->service->splitSegment($customerSegment, $request->criteria);

            return redirect()->route('customer-segments.index')
                ->with('success', 'Segment split successfully into '.count($newSegments).' new segments.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to split segment: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Recalculate all automatic segments.
     */
    public function recalculateAll()
    {
        $this->authorize('recalculateAll', CustomerSegment::class);

        try {
            $success = $this->service->recalculateAllSegments();

            return redirect()->back()
                ->with('success', 'All automatic segments recalculated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to recalculate segments: '.$e->getMessage()]);
        }
    }
}
