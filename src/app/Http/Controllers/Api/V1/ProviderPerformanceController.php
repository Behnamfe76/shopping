<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProviderPerformanceRequest;
use App\Http\Requests\UpdateProviderPerformanceRequest;
use App\Http\Requests\VerifyPerformanceRequest;
use App\Http\Requests\CalculatePerformanceRequest;
use App\Http\Requests\GenerateReportRequest;
use App\Http\Requests\SearchPerformanceRequest;
use App\Http\Resources\ProviderPerformanceResource;
use App\Http\Resources\ProviderPerformanceCollection;
use App\Http\Resources\ProviderPerformanceSearchResource;
use App\Http\Resources\ProviderPerformanceStatisticsResource;
use App\Http\Resources\ProviderPerformanceReportResource;
use App\Models\ProviderPerformance;
use App\Services\ProviderPerformanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;

class ProviderPerformanceController extends Controller
{
    protected ProviderPerformanceService $providerPerformanceService;

    public function __construct(ProviderPerformanceService $providerPerformanceService)
    {
        $this->providerPerformanceService = $providerPerformanceService;
    }

    /**
     * Display a listing of provider performances.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $providerPerformances = $this->providerPerformanceService->paginate($perPage);

            return response()->json([
                'data' => ProviderPerformanceResource::collection($providerPerformances->items()),
                'pagination' => [
                    'current_page' => $providerPerformances->currentPage(),
                    'last_page' => $providerPerformances->lastPage(),
                    'per_page' => $providerPerformances->perPage(),
                    'total' => $providerPerformances->total(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch provider performances', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to fetch provider performances',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created provider performance.
     */
    public function store(StoreProviderPerformanceRequest $request): JsonResponse
    {
        try {
            $providerPerformance = $this->providerPerformanceService->create($request->validated());

            return response()->json([
                'message' => 'Provider performance created successfully',
                'data' => new ProviderPerformanceResource($providerPerformance)
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create provider performance', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to create provider performance',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified provider performance.
     */
    public function show(ProviderPerformance $providerPerformance): JsonResponse
    {
        try {
            return response()->json([
                'data' => new ProviderPerformanceResource($providerPerformance)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch provider performance', [
                'error' => $e->getMessage(),
                'id' => $providerPerformance->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to fetch provider performance',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified provider performance.
     */
    public function update(UpdateProviderPerformanceRequest $request, ProviderPerformance $providerPerformance): JsonResponse
    {
        try {
            $updated = $this->providerPerformanceService->update($providerPerformance, $request->validated());

            if ($updated) {
                $providerPerformance->refresh();
                return response()->json([
                    'message' => 'Provider performance updated successfully',
                    'data' => new ProviderPerformanceResource($providerPerformance)
                ]);
            }

            return response()->json([
                'message' => 'Failed to update provider performance'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Failed to update provider performance', [
                'error' => $e->getMessage(),
                'id' => $providerPerformance->id,
                'data' => $request->validated(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to update provider performance',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified provider performance.
     */
    public function destroy(ProviderPerformance $providerPerformance): JsonResponse
    {
        try {
            $deleted = $this->providerPerformanceService->delete($providerPerformance);

            if ($deleted) {
                return response()->json([
                    'message' => 'Provider performance deleted successfully'
                ]);
            }

            return response()->json([
                'message' => 'Failed to delete provider performance'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Failed to delete provider performance', [
                'error' => $e->getMessage(),
                'id' => $providerPerformance->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to delete provider performance',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Verify provider performance.
     */
    public function verify(VerifyPerformanceRequest $request, ProviderPerformance $providerPerformance): JsonResponse
    {
        try {
            $verified = $this->providerPerformanceService->verify(
                $providerPerformance,
                auth()->id(),
                $request->get('notes')
            );

            if ($verified) {
                $providerPerformance->refresh();
                return response()->json([
                    'message' => 'Provider performance verified successfully',
                    'data' => new ProviderPerformanceResource($providerPerformance)
                ]);
            }

            return response()->json([
                'message' => 'Failed to verify provider performance'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Failed to verify provider performance', [
                'error' => $e->getMessage(),
                'id' => $providerPerformance->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to verify provider performance',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Unverify provider performance.
     */
    public function unverify(ProviderPerformance $providerPerformance): JsonResponse
    {
        try {
            $unverified = $this->providerPerformanceService->unverify($providerPerformance);

            if ($unverified) {
                $providerPerformance->refresh();
                return response()->json([
                    'message' => 'Provider performance unverified successfully',
                    'data' => new ProviderPerformanceResource($providerPerformance)
                ]);
            }

            return response()->json([
                'message' => 'Failed to unverify provider performance'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Failed to unverify provider performance', [
                'error' => $e->getMessage(),
                'id' => $providerPerformance->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to unverify provider performance',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Calculate performance metrics.
     */
    public function calculate(CalculatePerformanceRequest $request, ProviderPerformance $providerPerformance): JsonResponse
    {
        try {
            $calculated = $this->providerPerformanceService->calculatePerformance($providerPerformance);

            if ($calculated) {
                $providerPerformance->refresh();
                return response()->json([
                    'message' => 'Performance calculated successfully',
                    'data' => new ProviderPerformanceResource($providerPerformance)
                ]);
            }

            return response()->json([
                'message' => 'Failed to calculate performance'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Failed to calculate performance', [
                'error' => $e->getMessage(),
                'id' => $providerPerformance->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to calculate performance',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Search provider performances.
     */
    public function search(SearchPerformanceRequest $request): JsonResponse
    {
        try {
            $query = $request->get('query');
            $results = $this->providerPerformanceService->searchPerformance($query);

            return response()->json([
                'data' => ProviderPerformanceSearchResource::collection($results)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to search provider performances', [
                'error' => $e->getMessage(),
                'query' => $request->get('query'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to search provider performances',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get performance analytics.
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $providerId = $request->get('provider_id');
            $analytics = $providerId
                ? $this->providerPerformanceService->getPerformanceAnalytics($providerId)
                : $this->providerPerformanceService->getGlobalPerformanceAnalytics();

            return response()->json([
                'data' => new ProviderPerformanceStatisticsResource($analytics)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch performance analytics', [
                'error' => $e->getMessage(),
                'provider_id' => $request->get('provider_id'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to fetch performance analytics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Generate performance report.
     */
    public function generateReport(GenerateReportRequest $request): JsonResponse
    {
        try {
            $reportType = $request->get('report_type');
            $providerId = $request->get('provider_id');

            $report = $providerId
                ? $this->providerPerformanceService->getPerformanceReports($providerId, $reportType)
                : $this->providerPerformanceService->getGlobalPerformanceReports($reportType);

            return response()->json([
                'data' => new ProviderPerformanceReportResource($report)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate performance report', [
                'error' => $e->getMessage(),
                'report_type' => $request->get('report_type'),
                'provider_id' => $request->get('provider_id'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to generate performance report',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get top performers.
     */
    public function topPerformers(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $topPerformers = $this->providerPerformanceService->findTopPerformers($limit);

            return response()->json([
                'data' => ProviderPerformanceResource::collection($topPerformers)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch top performers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to fetch top performers',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get performance trends.
     */
    public function trends(Request $request): JsonResponse
    {
        try {
            $providerId = $request->get('provider_id');
            $periodType = $request->get('period_type', 'monthly');
            $periods = $request->get('periods', 12);

            if (!$providerId) {
                return response()->json([
                    'message' => 'Provider ID is required for trend analysis'
                ], 400);
            }

            $trends = $this->providerPerformanceService->getPerformanceTrend($providerId, $periodType, $periods);

            return response()->json([
                'data' => ProviderPerformanceResource::collection($trends)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch performance trends', [
                'error' => $e->getMessage(),
                'provider_id' => $request->get('provider_id'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to fetch performance trends',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get performance alerts.
     */
    public function alerts(Request $request): JsonResponse
    {
        try {
            $providerId = $request->get('provider_id');
            $alerts = $providerId
                ? $this->providerPerformanceService->getPerformanceAlerts($providerId)
                : $this->providerPerformanceService->getGlobalPerformanceAlerts();

            return response()->json([
                'data' => $alerts
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch performance alerts', [
                'error' => $e->getMessage(),
                'provider_id' => $request->get('provider_id'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to fetch performance alerts',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get performance by provider.
     */
    public function byProvider(Request $request, int $providerId): JsonResponse
    {
        try {
            $performances = $this->providerPerformanceService->findByProviderId($providerId);

            return response()->json([
                'data' => ProviderPerformanceResource::collection($performances)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch provider performances by provider', [
                'error' => $e->getMessage(),
                'provider_id' => $providerId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to fetch provider performances',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get performance by grade.
     */
    public function byGrade(Request $request, string $grade): JsonResponse
    {
        try {
            $performances = $this->providerPerformanceService->findByPerformanceGrade($grade);

            return response()->json([
                'data' => ProviderPerformanceResource::collection($performances)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch provider performances by grade', [
                'error' => $e->getMessage(),
                'grade' => $grade,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to fetch provider performances',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get performance by period.
     */
    public function byPeriod(Request $request): JsonResponse
    {
        try {
            $periodStart = $request->get('period_start');
            $periodEnd = $request->get('period_end');

            if (!$periodStart || !$periodEnd) {
                return response()->json([
                    'message' => 'Period start and end dates are required'
                ], 400);
            }

            $performances = $this->providerPerformanceService->findByPeriod($periodStart, $periodEnd);

            return response()->json([
                'data' => ProviderPerformanceResource::collection($performances)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch provider performances by period', [
                'error' => $e->getMessage(),
                'period_start' => $request->get('period_start'),
                'period_end' => $request->get('period_end'),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to fetch provider performances',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
