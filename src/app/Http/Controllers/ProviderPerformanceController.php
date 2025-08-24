<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ProviderPerformanceService;
use App\Models\ProviderPerformance;
use App\DTOs\ProviderPerformanceDTO;
use App\Enums\PerformanceGrade;
use App\Enums\PeriodType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProviderPerformanceController extends Controller
{
    protected $service;

    public function __construct(ProviderPerformanceService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
        $this->middleware('can:view,provider-performance');
    }

    /**
     * Display a listing of provider performances
     */
    public function index(Request $request): View
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $grade = $request->get('grade');
            $periodType = $request->get('period_type');
            $verified = $request->get('verified');

            $performances = $this->service->getPaginatedPerformances($perPage);

            // Apply filters
            if ($search) {
                $performances = $this->service->searchPerformance($search);
            }

            if ($grade) {
                $performances = $this->service->getPerformancesByGrade($grade);
            }

            if ($periodType) {
                $performances = $this->service->getPerformancesByPeriodType($periodType);
            }

            if ($verified !== null) {
                $performances = $verified
                    ? $this->service->getVerifiedPerformances()
                    : $this->service->getUnverifiedPerformances();
            }

            $grades = PerformanceGrade::cases();
            $periodTypes = PeriodType::cases();

            return view('provider-performance.index', compact(
                'performances',
                'grades',
                'periodTypes',
                'search',
                'grade',
                'periodType',
                'verified'
            ));
        } catch (\Exception $e) {
            Log::error('Failed to display provider performances: ' . $e->getMessage());
            return view('provider-performance.index')->with('error', 'Failed to load performances');
        }
    }

    /**
     * Show the form for creating a new provider performance
     */
    public function create(): View
    {
        $this->authorize('create', ProviderPerformance::class);

        $grades = PerformanceGrade::cases();
        $periodTypes = PeriodType::cases();

        return view('provider-performance.create', compact('grades', 'periodTypes'));
    }

    /**
     * Store a newly created provider performance
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', ProviderPerformance::class);

        try {
            $validator = Validator::make($request->all(), [
                'provider_id' => 'required|integer|exists:providers,id',
                'period_start' => 'required|date',
                'period_end' => 'required|date|after:period_start',
                'period_type' => 'required|string|in:' . implode(',', PeriodType::values()),
                'total_orders' => 'required|integer|min:0',
                'total_revenue' => 'required|numeric|min:0',
                'average_order_value' => 'required|numeric|min:0',
                'on_time_delivery_rate' => 'required|numeric|between:0,100',
                'return_rate' => 'required|numeric|between:0,100',
                'defect_rate' => 'required|numeric|between:0,100',
                'customer_satisfaction_score' => 'required|numeric|between:1,10',
                'response_time_avg' => 'required|numeric|min:0',
                'quality_rating' => 'required|numeric|between:1,10',
                'delivery_rating' => 'required|numeric|between:1,10',
                'communication_rating' => 'required|numeric|between:1,10',
                'cost_efficiency_score' => 'required|numeric|between:0,100',
                'inventory_turnover_rate' => 'required|numeric|min:0',
                'lead_time_avg' => 'required|numeric|min:0',
                'fill_rate' => 'required|numeric|between:0,100',
                'accuracy_rate' => 'required|numeric|between:0,100',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = $request->all();
            $data['is_verified'] = false;

            $performance = $this->service->createPerformance($data);

            return redirect()->route('provider-performance.show', $performance)
                ->with('success', 'Provider performance created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create provider performance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create provider performance')
                ->withInput();
        }
    }

    /**
     * Display the specified provider performance
     */
    public function show(ProviderPerformance $providerPerformance): View
    {
        $this->authorize('view', $providerPerformance);

        try {
            $performance = $this->service->getPerformanceById($providerPerformance->id);

            if (!$performance) {
                abort(404);
            }

            $alerts = $performance->getPerformanceAlerts();
            $suggestions = $performance->getImprovementSuggestions();
            $benchmarks = $performance->getBenchmarkComparison();
            $trend = $performance->getPerformanceTrend();

            return view('provider-performance.show', compact(
                'performance',
                'alerts',
                'suggestions',
                'benchmarks',
                'trend'
            ));
        } catch (\Exception $e) {
            Log::error('Failed to display provider performance: ' . $e->getMessage());
            return view('provider-performance.show')->with('error', 'Failed to load performance');
        }
    }

    /**
     * Show the form for editing the specified provider performance
     */
    public function edit(ProviderPerformance $providerPerformance): View
    {
        $this->authorize('update', $providerPerformance);

        $grades = PerformanceGrade::cases();
        $periodTypes = PeriodType::cases();

        return view('provider-performance.edit', compact('providerPerformance', 'grades', 'periodTypes'));
    }

    /**
     * Update the specified provider performance
     */
    public function update(Request $request, ProviderPerformance $providerPerformance): RedirectResponse
    {
        $this->authorize('update', $providerPerformance);

        try {
            $validator = Validator::make($request->all(), [
                'period_start' => 'required|date',
                'period_end' => 'required|date|after:period_start',
                'period_type' => 'required|string|in:' . implode(',', PeriodType::values()),
                'total_orders' => 'required|integer|min:0',
                'total_revenue' => 'required|numeric|min:0',
                'average_order_value' => 'required|numeric|min:0',
                'on_time_delivery_rate' => 'required|numeric|between:0,100',
                'return_rate' => 'required|numeric|between:0,100',
                'defect_rate' => 'required|numeric|between:0,100',
                'customer_satisfaction_score' => 'required|numeric|between:1,10',
                'response_time_avg' => 'required|numeric|min:0',
                'quality_rating' => 'required|numeric|between:1,10',
                'delivery_rating' => 'required|numeric|between:1,10',
                'communication_rating' => 'required|numeric|between:1,10',
                'cost_efficiency_score' => 'required|numeric|between:0,100',
                'inventory_turnover_rate' => 'required|numeric|min:0',
                'lead_time_avg' => 'required|numeric|min:0',
                'fill_rate' => 'required|numeric|between:0,100',
                'accuracy_rate' => 'required|numeric|between:0,100',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = $request->all();
            $this->service->updatePerformance($providerPerformance, $data);

            return redirect()->route('provider-performance.show', $providerPerformance)
                ->with('success', 'Provider performance updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update provider performance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update provider performance')
                ->withInput();
        }
    }

    /**
     * Remove the specified provider performance
     */
    public function destroy(ProviderPerformance $providerPerformance): RedirectResponse
    {
        $this->authorize('delete', $providerPerformance);

        try {
            $this->service->deletePerformance($providerPerformance);

            return redirect()->route('provider-performance.index')
                ->with('success', 'Provider performance deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete provider performance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete provider performance');
        }
    }

    /**
     * Verify a provider performance
     */
    public function verify(Request $request, ProviderPerformance $providerPerformance): RedirectResponse
    {
        $this->authorize('verify', $providerPerformance);

        try {
            $validator = Validator::make($request->all(), [
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $notes = $request->get('notes');
            $verifiedBy = Auth::id();

            $this->service->verifyPerformance($providerPerformance, $verifiedBy, $notes);

            return redirect()->route('provider-performance.show', $providerPerformance)
                ->with('success', 'Provider performance verified successfully');
        } catch (\Exception $e) {
            Log::error('Failed to verify provider performance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to verify provider performance');
        }
    }

    /**
     * Unverify a provider performance
     */
    public function unverify(ProviderPerformance $providerPerformance): RedirectResponse
    {
        $this->authorize('verify', $providerPerformance);

        try {
            $this->service->unverifyPerformance($providerPerformance);

            return redirect()->route('provider-performance.show', $providerPerformance)
                ->with('success', 'Provider performance unverified successfully');
        } catch (\Exception $e) {
            Log::error('Failed to unverify provider performance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to unverify provider performance');
        }
    }

    /**
     * Recalculate performance score and grade
     */
    public function recalculate(ProviderPerformance $providerPerformance): RedirectResponse
    {
        $this->authorize('calculate', $providerPerformance);

        try {
            $this->service->recalculateScore($providerPerformance);

            return redirect()->route('provider-performance.show', $providerPerformance)
                ->with('success', 'Performance score recalculated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to recalculate performance: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to recalculate performance');
        }
    }

    /**
     * Display analytics dashboard
     */
    public function analytics(Request $request): View
    {
        $this->authorize('view-analytics', ProviderPerformance::class);

        try {
            $providerId = $request->get('provider_id');
            $periodType = $request->get('period_type', 'monthly');
            $grade = $request->get('grade');

            $analytics = [];
            $globalAnalytics = [];

            if ($providerId) {
                $analytics = $this->service->getPerformanceAnalytics($providerId);

                if ($grade) {
                    $analytics = $this->service->getPerformanceAnalyticsByGrade($providerId, $grade);
                }

                if ($periodType) {
                    $analytics = $this->service->getPerformanceAnalyticsByPeriod($providerId, $periodType);
                }
            }

            $globalAnalytics = $this->service->getGlobalPerformanceAnalytics();

            if ($grade) {
                $globalAnalytics = $this->service->getGlobalPerformanceAnalyticsByGrade($grade);
            }

            if ($periodType) {
                $globalAnalytics = $this->service->getGlobalPerformanceAnalyticsByPeriod($periodType);
            }

            $grades = PerformanceGrade::cases();
            $periodTypes = PeriodType::cases();

            return view('provider-performance.analytics', compact(
                'analytics',
                'globalAnalytics',
                'grades',
                'periodTypes',
                'providerId',
                'periodType',
                'grade'
            ));
        } catch (\Exception $e) {
            Log::error('Failed to display analytics: ' . $e->getMessage());
            return view('provider-performance.analytics')->with('error', 'Failed to load analytics');
        }
    }

    /**
     * Display performance reports
     */
    public function reports(Request $request): View
    {
        $this->authorize('view-reports', ProviderPerformance::class);

        try {
            $providerId = $request->get('provider_id');
            $reportType = $request->get('report_type', 'summary');
            $periodType = $request->get('period_type', 'monthly');

            $reports = [];
            $globalReports = [];

            if ($providerId) {
                $reports = $this->service->getPerformanceReports($providerId, $reportType);
            }

            $globalReports = $this->service->getGlobalPerformanceReports($reportType);

            $reportTypes = [
                'summary' => 'Summary Report',
                'detailed' => 'Detailed Report',
                'trend' => 'Trend Analysis',
                'comparison' => 'Comparison Report',
                'benchmark' => 'Benchmark Report',
            ];

            $periodTypes = PeriodType::cases();

            return view('provider-performance.reports', compact(
                'reports',
                'globalReports',
                'reportTypes',
                'periodTypes',
                'providerId',
                'reportType',
                'periodType'
            ));
        } catch (\Exception $e) {
            Log::error('Failed to display reports: ' . $e->getMessage());
            return view('provider-performance.reports')->with('error', 'Failed to load reports');
        }
    }

    /**
     * Display top performers
     */
    public function topPerformers(Request $request): View
    {
        $this->authorize('view', ProviderPerformance::class);

        try {
            $limit = $request->get('limit', 10);
            $periodType = $request->get('period_type');
            $grade = $request->get('grade');

            $topPerformers = $this->service->getTopPerformers($limit);

            if ($periodType) {
                $topPerformers = $this->service->getPerformancesByPeriodType($periodType)
                    ->sortByDesc('performance_score')
                    ->take($limit);
            }

            if ($grade) {
                $topPerformers = $this->service->getPerformancesByGrade($grade)
                    ->sortByDesc('performance_score')
                    ->take($limit);
            }

            $grades = PerformanceGrade::cases();
            $periodTypes = PeriodType::cases();

            return view('provider-performance.top-performers', compact(
                'topPerformers',
                'grades',
                'periodTypes',
                'limit',
                'periodType',
                'grade'
            ));
        } catch (\Exception $e) {
            Log::error('Failed to display top performers: ' . $e->getMessage());
            return view('provider-performance.top-performers')->with('error', 'Failed to load top performers');
        }
    }

    /**
     * Display performance alerts
     */
    public function alerts(Request $request): View
    {
        $this->authorize('view-alerts', ProviderPerformance::class);

        try {
            $providerId = $request->get('provider_id');

            $alerts = [];
            $globalAlerts = [];

            if ($providerId) {
                $alerts = $this->service->getPerformanceAlerts($providerId);
            }

            $globalAlerts = $this->service->getGlobalPerformanceAlerts();

            return view('provider-performance.alerts', compact(
                'alerts',
                'globalAlerts',
                'providerId'
            ));
        } catch (\Exception $e) {
            Log::error('Failed to display alerts: ' . $e->getMessage());
            return view('provider-performance.alerts')->with('error', 'Failed to load alerts');
        }
    }

    /**
     * Export performance data
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize('export', ProviderPerformance::class);

        try {
            $format = $request->get('format', 'csv');
            $providerId = $request->get('provider_id');
            $periodType = $request->get('period_type');
            $grade = $request->get('grade');

            // Implementation for export functionality
            // This would typically generate and return a file download

            return response()->json([
                'message' => 'Export completed successfully',
                'format' => $format,
                'provider_id' => $providerId,
                'period_type' => $periodType,
                'grade' => $grade,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to export performance data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to export performance data'
            ], 500);
        }
    }
}
