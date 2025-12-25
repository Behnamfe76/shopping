<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api;

use Fereydooni\Shopping\app\Services\EmployeePerformanceReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class EmployeePerformanceReviewController extends Controller
{
    public function __construct(
        private EmployeePerformanceReviewService $service
    ) {}

    /**
     * Display a listing of performance reviews
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'rating', 'employee_id', 'reviewer_id', 'date_from', 'date_to']);
        $reviews = $this->service->getCompanyReviews($filters);

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'Performance reviews retrieved successfully',
        ]);
    }

    /**
     * Store a newly created performance review
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'reviewer_id' => 'required|exists:users,id',
            'review_period_start' => 'required|date',
            'review_period_end' => 'required|date|after:review_period_start',
            'review_date' => 'required|date',
            'next_review_date' => 'required|date|after:review_date',
            'overall_rating' => 'required|numeric|between:1,5',
            'performance_score' => 'required|numeric|between:0,100',
            'goals_achieved' => 'nullable|array',
            'goals_missed' => 'nullable|array',
            'strengths' => 'nullable|string|max:1000',
            'areas_for_improvement' => 'nullable|string|max:1000',
            'recommendations' => 'nullable|string|max:1000',
            'employee_comments' => 'nullable|string|max:1000',
            'reviewer_comments' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $review = $this->service->createReview($request->all());

            return response()->json([
                'success' => true,
                'data' => $review,
                'message' => 'Performance review created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create performance review: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified performance review
     */
    public function show(int $id): JsonResponse
    {
        $review = $this->service->find($id);

        if (! $review) {
            return response()->json([
                'success' => false,
                'message' => 'Performance review not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $review,
            'message' => 'Performance review retrieved successfully',
        ]);
    }

    /**
     * Update the specified performance review
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'review_period_start' => 'sometimes|date',
            'review_period_end' => 'sometimes|date|after:review_period_start',
            'review_date' => 'sometimes|date',
            'next_review_date' => 'sometimes|date|after:review_date',
            'overall_rating' => 'sometimes|numeric|between:1,5',
            'performance_score' => 'sometimes|numeric|between:0,100',
            'goals_achieved' => 'nullable|array',
            'goals_missed' => 'nullable|array',
            'strengths' => 'nullable|string|max:1000',
            'areas_for_improvement' => 'nullable|string|max:1000',
            'recommendations' => 'nullable|string|max:1000',
            'employee_comments' => 'nullable|string|max:1000',
            'reviewer_comments' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $review = $this->service->updateReview($id, $request->all());

            return response()->json([
                'success' => true,
                'data' => $review,
                'message' => 'Performance review updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update performance review: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified performance review
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->service->deleteReview($id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Performance review deleted successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete performance review',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete performance review: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get performance reviews for a specific employee
     */
    public function employeeReviews(int $employeeId): JsonResponse
    {
        $reviews = $this->service->getEmployeeReviews($employeeId);

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'Employee performance reviews retrieved successfully',
        ]);
    }

    /**
     * Create performance review for a specific employee
     */
    public function storeForEmployee(Request $request, int $employeeId): JsonResponse
    {
        $request->merge(['employee_id' => $employeeId]);

        return $this->store($request);
    }

    /**
     * Search performance reviews
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
            'filters' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        $filters = $request->input('filters', []);
        $reviews = $this->service->searchReviews($request->input('query'), $filters);

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'Search results retrieved successfully',
        ]);
    }

    /**
     * Submit review for approval
     */
    public function submit(int $id): JsonResponse
    {
        try {
            $result = $this->service->submitReview($id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Performance review submitted for approval successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit performance review for approval',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit performance review: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve performance review
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'approved_by' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $result = $this->service->approveReview($id, $request->input('approved_by'));

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Performance review approved successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve performance review',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve performance review: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject performance review
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rejected_by' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $result = $this->service->rejectReview(
                $id,
                $request->input('rejected_by'),
                $request->input('reason')
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Performance review rejected successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reject performance review',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject performance review: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign reviewer to performance review
     */
    public function assignReviewer(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reviewer_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $result = $this->service->assignReviewer($id, $request->input('reviewer_id'));

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reviewer assigned successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to assign reviewer',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign reviewer: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Schedule performance review
     */
    public function schedule(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'review_date' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $result = $this->service->scheduleReview($id, $request->input('review_date'));

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Performance review scheduled successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to schedule performance review',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule performance review: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get pending approval reviews
     */
    public function pendingApproval(): JsonResponse
    {
        $reviews = $this->service->getPendingApprovals();

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'Pending approval reviews retrieved successfully',
        ]);
    }

    /**
     * Get overdue reviews
     */
    public function overdue(): JsonResponse
    {
        $reviews = $this->service->getOverdueReviews();

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'Overdue reviews retrieved successfully',
        ]);
    }

    /**
     * Get upcoming reviews
     */
    public function upcoming(Request $request): JsonResponse
    {
        $date = $request->input('date');
        $reviews = $this->service->getUpcomingReviews($date);

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'message' => 'Upcoming reviews retrieved successfully',
        ]);
    }

    /**
     * Get review statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $filters = $request->only(['date_from', 'date_to', 'department_id', 'employee_id']);
        $statistics = $this->service->getReviewStatistics($filters);

        return response()->json([
            'success' => true,
            'data' => $statistics,
            'message' => 'Review statistics retrieved successfully',
        ]);
    }

    /**
     * Get employee review statistics
     */
    public function employeeStatistics(int $employeeId): JsonResponse
    {
        $statistics = $this->service->getEmployeeReviewStatistics($employeeId);

        return response()->json([
            'success' => true,
            'data' => $statistics,
            'message' => 'Employee review statistics retrieved successfully',
        ]);
    }

    /**
     * Get department review statistics
     */
    public function departmentStatistics(int $departmentId): JsonResponse
    {
        $statistics = $this->service->getDepartmentReviewStatistics($departmentId);

        return response()->json([
            'success' => true,
            'data' => $statistics,
            'message' => 'Department review statistics retrieved successfully',
        ]);
    }

    /**
     * Generate performance reports
     */
    public function reports(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:employee,department,company',
            'id' => 'required_if:type,employee,department|integer',
            'period' => 'required|in:month,quarter,year',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $type = $request->input('type');
            $period = $request->input('period');

            switch ($type) {
                case 'employee':
                    $report = $this->service->generatePerformanceReport(
                        $request->input('id'),
                        $period
                    );
                    break;
                case 'department':
                    $report = $this->service->generateDepartmentReport(
                        $request->input('id'),
                        $period
                    );
                    break;
                case 'company':
                    $report = $this->service->generateCompanyReport($period);
                    break;
                default:
                    throw new \InvalidArgumentException('Invalid report type');
            }

            return response()->json([
                'success' => true,
                'data' => $report,
                'message' => 'Performance report generated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export performance reviews
     */
    public function export(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'filters' => 'nullable|array',
            'format' => 'required|in:json,csv,xlsx,pdf',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $filters = $request->input('filters', []);
            $format = $request->input('format');
            $exportData = $this->service->exportReviews($filters, $format);

            return response()->json([
                'success' => true,
                'data' => [
                    'export_url' => $exportData,
                    'format' => $format,
                ],
                'message' => 'Performance reviews exported successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export reviews: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import performance reviews
     */
    public function import(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|string',
            'format' => 'required|in:json,csv,xlsx',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $result = $this->service->importReviews(
                $request->input('data'),
                $request->input('format')
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Performance reviews imported successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to import performance reviews',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to import reviews: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk approve performance reviews
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'integer|exists:employee_performance_reviews,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $result = $this->service->bulkApproveReviews($request->input('review_ids'));

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Performance reviews bulk approved successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to bulk approve performance reviews',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk approve reviews: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk reject performance reviews
     */
    public function bulkReject(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'integer|exists:employee_performance_reviews,id',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        try {
            $result = $this->service->bulkRejectReviews(
                $request->input('review_ids'),
                $request->input('reason')
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Performance reviews bulk rejected successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to bulk reject performance reviews',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk reject reviews: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send review reminders
     */
    public function sendReminders(): JsonResponse
    {
        try {
            $result = $this->service->sendReviewReminders();

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Review reminders sent successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send review reminders',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reminders: '.$e->getMessage(),
            ], 500);
        }
    }
}
