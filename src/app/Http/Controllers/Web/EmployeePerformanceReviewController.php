<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Web;

use Fereydooni\Shopping\app\Services\EmployeePerformanceReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class EmployeePerformanceReviewController extends Controller
{
    public function __construct(
        private EmployeePerformanceReviewService $service
    ) {}

    /**
     * Display a listing of performance reviews
     */
    public function index(): View
    {
        $reviews = $this->service->getCompanyReviews();

        return view('shopping::employee-performance-reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new performance review
     */
    public function create(): View
    {
        return view('shopping::employee-performance-reviews.create');
    }

    /**
     * Store a newly created performance review
     */
    public function store(Request $request): RedirectResponse
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $review = $this->service->createReview($request->all());

            Session::flash('success', 'Performance review created successfully');

            return redirect()->route('employee-performance-reviews.show', $review->id);
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to create performance review: '.$e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified performance review
     */
    public function show(int $id): View
    {
        $review = $this->service->find($id);

        if (! $review) {
            abort(404, 'Performance review not found');
        }

        return view('shopping::employee-performance-reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified performance review
     */
    public function edit(int $id): View
    {
        $review = $this->service->find($id);

        if (! $review) {
            abort(404, 'Performance review not found');
        }

        return view('shopping::employee-performance-reviews.edit', compact('review'));
    }

    /**
     * Update the specified performance review
     */
    public function update(Request $request, int $id): RedirectResponse
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $review = $this->service->updateReview($id, $request->all());

            Session::flash('success', 'Performance review updated successfully');

            return redirect()->route('employee-performance-reviews.show', $review->id);
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to update performance review: '.$e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified performance review
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $result = $this->service->deleteReview($id);

            if ($result) {
                Session::flash('success', 'Performance review deleted successfully');

                return redirect()->route('employee-performance-reviews.index');
            } else {
                Session::flash('error', 'Failed to delete performance review');

                return redirect()->back();
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to delete performance review: '.$e->getMessage());

            return redirect()->back();
        }
    }

    /**
     * Show performance reviews for a specific employee
     */
    public function employeeReviews(int $employeeId): View
    {
        $reviews = $this->service->getEmployeeReviews($employeeId);

        return view('shopping::employee-performance-reviews.employee-reviews', compact('reviews', 'employeeId'));
    }

    /**
     * Show create form for a specific employee
     */
    public function createForEmployee(int $employeeId): View
    {
        return view('shopping::employee-performance-reviews.create-for-employee', compact('employeeId'));
    }

    /**
     * Store performance review for a specific employee
     */
    public function storeForEmployee(Request $request, int $employeeId): RedirectResponse
    {
        $request->merge(['employee_id' => $employeeId]);

        return $this->store($request);
    }

    /**
     * Search performance reviews
     */
    public function search(Request $request): View
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
            'filters' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $filters = $request->input('filters', []);
        $reviews = $this->service->searchReviews($request->input('query'), $filters);

        return view('shopping::employee-performance-reviews.search-results', compact('reviews', 'query', 'filters'));
    }

    /**
     * Submit review for approval
     */
    public function submit(int $id): RedirectResponse
    {
        try {
            $result = $this->service->submitReview($id);

            if ($result) {
                Session::flash('success', 'Performance review submitted for approval successfully');
            } else {
                Session::flash('error', 'Failed to submit performance review for approval');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to submit performance review: '.$e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Approve performance review
     */
    public function approve(int $id): RedirectResponse
    {
        try {
            $result = $this->service->approveReview($id, auth()->id());

            if ($result) {
                Session::flash('success', 'Performance review approved successfully');
            } else {
                Session::flash('error', 'Failed to approve performance review');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to approve performance review: '.$e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Reject performance review
     */
    public function reject(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $result = $this->service->rejectReview(
                $id,
                auth()->id(),
                $request->input('reason')
            );

            if ($result) {
                Session::flash('success', 'Performance review rejected successfully');
            } else {
                Session::flash('error', 'Failed to reject performance review');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to reject performance review: '.$e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Assign reviewer to performance review
     */
    public function assignReviewer(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'reviewer_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $result = $this->service->assignReviewer($id, $request->input('reviewer_id'));

            if ($result) {
                Session::flash('success', 'Reviewer assigned successfully');
            } else {
                Session::flash('error', 'Failed to assign reviewer');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to assign reviewer: '.$e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Schedule performance review
     */
    public function schedule(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'review_date' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $result = $this->service->scheduleReview($id, $request->input('review_date'));

            if ($result) {
                Session::flash('success', 'Performance review scheduled successfully');
            } else {
                Session::flash('error', 'Failed to schedule performance review');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to schedule performance review: '.$e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Show pending approval reviews
     */
    public function pendingApproval(): View
    {
        $reviews = $this->service->getPendingApprovals();

        return view('shopping::employee-performance-reviews.pending-approval', compact('reviews'));
    }

    /**
     * Show overdue reviews
     */
    public function overdue(): View
    {
        $reviews = $this->service->getOverdueReviews();

        return view('shopping::employee-performance-reviews.overdue', compact('reviews'));
    }

    /**
     * Show upcoming reviews
     */
    public function upcoming(Request $request): View
    {
        $date = $request->input('date');
        $reviews = $this->service->getUpcomingReviews($date);

        return view('shopping::employee-performance-reviews.upcoming', compact('reviews', 'date'));
    }

    /**
     * Show review statistics
     */
    public function statistics(Request $request): View
    {
        $filters = $request->only(['date_from', 'date_to', 'department_id', 'employee_id']);
        $statistics = $this->service->getReviewStatistics($filters);

        return view('shopping::employee-performance-reviews.statistics', compact('statistics', 'filters'));
    }

    /**
     * Show employee review statistics
     */
    public function employeeStatistics(int $employeeId): View
    {
        $statistics = $this->service->getEmployeeReviewStatistics($employeeId);

        return view('shopping::employee-performance-reviews.employee-statistics', compact('statistics', 'employeeId'));
    }

    /**
     * Show department review statistics
     */
    public function departmentStatistics(int $departmentId): View
    {
        $statistics = $this->service->getDepartmentReviewStatistics($departmentId);

        return view('shopping::employee-performance-reviews.department-statistics', compact('statistics', 'departmentId'));
    }

    /**
     * Generate performance reports
     */
    public function reports(Request $request): View
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:employee,department,company',
            'id' => 'required_if:type,employee,department|integer',
            'period' => 'required|in:month,quarter,year',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
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

            return view('shopping::employee-performance-reviews.reports', compact('report', 'type', 'period'));
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to generate report: '.$e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Export performance reviews
     */
    public function export(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'filters' => 'nullable|array',
            'format' => 'required|in:json,csv,xlsx,pdf',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $filters = $request->input('filters', []);
            $format = $request->input('format');
            $exportData = $this->service->exportReviews($filters, $format);

            Session::flash('success', 'Performance reviews exported successfully');

            return redirect()->back()->with('export_url', $exportData);
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to export reviews: '.$e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Import performance reviews
     */
    public function import(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|string',
            'format' => 'required|in:json,csv,xlsx',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $result = $this->service->importReviews(
                $request->input('data'),
                $request->input('format')
            );

            if ($result) {
                Session::flash('success', 'Performance reviews imported successfully');
            } else {
                Session::flash('error', 'Failed to import performance reviews');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to import reviews: '.$e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Bulk approve performance reviews
     */
    public function bulkApprove(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'integer|exists:employee_performance_reviews,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $result = $this->service->bulkApproveReviews($request->input('review_ids'));

            if ($result) {
                Session::flash('success', 'Performance reviews bulk approved successfully');
            } else {
                Session::flash('error', 'Failed to bulk approve performance reviews');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to bulk approve reviews: '.$e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Bulk reject performance reviews
     */
    public function bulkReject(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'integer|exists:employee_performance_reviews,id',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $result = $this->service->bulkRejectReviews(
                $request->input('review_ids'),
                $request->input('reason')
            );

            if ($result) {
                Session::flash('success', 'Performance reviews bulk rejected successfully');
            } else {
                Session::flash('error', 'Failed to bulk reject performance reviews');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to bulk reject reviews: '.$e->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Send review reminders
     */
    public function sendReminders(): RedirectResponse
    {
        try {
            $result = $this->service->sendReviewReminders();

            if ($result) {
                Session::flash('success', 'Review reminders sent successfully');
            } else {
                Session::flash('error', 'Failed to send review reminders');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to send reminders: '.$e->getMessage());
        }

        return redirect()->back();
    }
}
