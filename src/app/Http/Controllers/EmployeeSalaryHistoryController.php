<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeSalaryHistory\StoreEmployeeSalaryHistoryRequest;
use App\Http\Requests\EmployeeSalaryHistory\UpdateEmployeeSalaryHistoryRequest;
use App\Http\Requests\EmployeeSalaryHistory\ApproveSalaryChangeRequest;
use App\Http\Requests\EmployeeSalaryHistory\RejectSalaryChangeRequest;
use App\Http\Requests\EmployeeSalaryHistory\ProcessSalaryChangeRequest;
use App\Services\EmployeeSalaryHistoryService;
use App\Models\EmployeeSalaryHistory;
use App\DTOs\EmployeeSalaryHistoryDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EmployeeSalaryHistoryController extends Controller
{
    protected $service;

    public function __construct(EmployeeSalaryHistoryService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
    }

    /**
     * Display a listing of salary history records
     */
    public function index(Request $request): View
    {
        Gate::authorize('employee-salary-history.view');

        $filters = $request->only(['employee_id', 'change_type', 'status', 'date_from', 'date_to']);
        $perPage = $request->get('per_page', 15);

        $salaryHistories = $this->service->getSalaryHistoryPaginated($filters, $perPage);

        return view('employee-salary-history.index', compact('salaryHistories', 'filters'));
    }

    /**
     * Show the form for creating a new salary history record
     */
    public function create(): View
    {
        Gate::authorize('employee-salary-history.create');

        return view('employee-salary-history.create');
    }

    /**
     * Store a newly created salary history record
     */
    public function store(StoreEmployeeSalaryHistoryRequest $request): RedirectResponse
    {
        Gate::authorize('employee-salary-history.create');

        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();

            $salaryHistory = $this->service->createSalaryHistory($data);

            return redirect()
                ->route('employee-salary-history.show', $salaryHistory)
                ->with('success', 'Salary change request created successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create salary change request: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified salary history record
     */
    public function show(EmployeeSalaryHistory $employeeSalaryHistory): View
    {
        Gate::authorize('employee-salary-history.view', $employeeSalaryHistory);

        return view('employee-salary-history.show', compact('employeeSalaryHistory'));
    }

    /**
     * Show the form for editing the specified salary history record
     */
    public function edit(EmployeeSalaryHistory $employeeSalaryHistory): View
    {
        Gate::authorize('employee-salary-history.edit', $employeeSalaryHistory);

        return view('employee-salary-history.edit', compact('employeeSalaryHistory'));
    }

    /**
     * Update the specified salary history record
     */
    public function update(UpdateEmployeeSalaryHistoryRequest $request, EmployeeSalaryHistory $employeeSalaryHistory): RedirectResponse
    {
        Gate::authorize('employee-salary-history.edit', $employeeSalaryHistory);

        try {
            $data = $request->validated();
            $data['updated_by'] = Auth::id();

            $updated = $this->service->updateSalaryHistory($employeeSalaryHistory->id, $data);

            if ($updated) {
                return redirect()
                    ->route('employee-salary-history.show', $employeeSalaryHistory)
                    ->with('success', 'Salary change request updated successfully.');
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update salary change request.']);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update salary change request: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified salary history record
     */
    public function destroy(EmployeeSalaryHistory $employeeSalaryHistory): RedirectResponse
    {
        Gate::authorize('employee-salary-history.delete', $employeeSalaryHistory);

        try {
            $deleted = $this->service->deleteSalaryHistory($employeeSalaryHistory->id);

            if ($deleted) {
                return redirect()
                    ->route('employee-salary-history.index')
                    ->with('success', 'Salary change request deleted successfully.');
            }

            return back()->withErrors(['error' => 'Failed to delete salary change request.']);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete salary change request: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve a salary change
     */
    public function approve(ApproveSalaryChangeRequest $request, EmployeeSalaryHistory $employeeSalaryHistory): JsonResponse
    {
        Gate::authorize('employee-salary-history.approve', $employeeSalaryHistory);

        try {
            $approved = $this->service->approveSalaryChange(
                $employeeSalaryHistory->id,
                Auth::id()
            );

            if ($approved) {
                return response()->json([
                    'success' => true,
                    'message' => 'Salary change approved successfully.',
                    'status' => 'approved'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve salary change.'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve salary change: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a salary change
     */
    public function reject(RejectSalaryChangeRequest $request, EmployeeSalaryHistory $employeeSalaryHistory): JsonResponse
    {
        Gate::authorize('employee-salary-history.reject', $employeeSalaryHistory);

        try {
            $rejected = $this->service->rejectSalaryChange(
                $employeeSalaryHistory->id,
                Auth::id(),
                $request->input('rejection_reason')
            );

            if ($rejected) {
                return response()->json([
                    'success' => true,
                    'message' => 'Salary change rejected successfully.',
                    'status' => 'rejected'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject salary change.'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject salary change: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process a salary change
     */
    public function process(ProcessSalaryChangeRequest $request, EmployeeSalaryHistory $employeeSalaryHistory): JsonResponse
    {
        Gate::authorize('employee-salary-history.approve', $employeeSalaryHistory);

        try {
            $processed = $this->service->processSalaryChange(
                $employeeSalaryHistory->id,
                Auth::id()
            );

            if ($processed) {
                return response()->json([
                    'success' => true,
                    'message' => 'Salary change processed successfully.',
                    'status' => 'processed'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to process salary change.'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process salary change: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get salary history for a specific employee
     */
    public function employeeHistory(int $employeeId, Request $request): View
    {
        Gate::authorize('employee-salary-history.view-employee', $employeeId);

        $filters = $request->only(['change_type', 'status', 'date_from', 'date_to']);
        $perPage = $request->get('per_page', 15);

        $salaryHistories = $this->service->getEmployeeSalaryHistory($employeeId, $filters);
        $employee = Employee::findOrFail($employeeId);

        return view('employee-salary-history.employee-history', compact('salaryHistories', 'employee', 'filters'));
    }

    /**
     * Export salary history data
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        Gate::authorize('employee-salary-history.export');

        $filters = $request->only(['employee_id', 'change_type', 'status', 'date_from', 'date_to']);

        $exportPath = $this->service->exportSalaryHistory($filters);

        return response()->download($exportPath, 'salary_history_export.csv');
    }

    /**
     * Show salary statistics
     */
    public function statistics(Request $request): View
    {
        Gate::authorize('employee-salary-history.statistics');

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $statistics = $this->service->getSalaryStatistics($startDate, $endDate);
        $trends = $this->service->getSalaryTrends($startDate, $endDate);

        return view('employee-salary-history.statistics', compact('statistics', 'trends', 'startDate', 'endDate'));
    }

    /**
     * Search salary history
     */
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('employee-salary-history.view');

        $query = $request->input('query');
        $filters = $request->only(['employee_id', 'change_type', 'status']);

        $results = $this->service->searchSalaryHistory($query, $filters);

        return response()->json([
            'success' => true,
            'data' => $results,
            'total' => $results->count()
        ]);
    }
}
