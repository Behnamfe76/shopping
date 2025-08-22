<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Web;

use Illuminate\Routing\Controller;
use Fereydooni\Shopping\app\Services\EmployeeService;
use Fereydooni\Shopping\app\DTOs\EmployeeDTO;
use Fereydooni\Shopping\app\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
        $this->middleware('auth');
        $this->middleware('permission:employees.view');
    }

    /**
     * Display employee dashboard
     */
    public function dashboard(): View
    {
        Gate::authorize('viewAny', Employee::class);

        $stats = $this->employeeService->getEmployeeStats();
        $recentHires = $this->employeeService->getNewestHires(5);
        $topPerformers = $this->employeeService->getTopPerformers(5);
        $upcomingReviews = $this->employeeService->getEmployeesWithUpcomingReviews(30);

        return view('employees.dashboard', compact('stats', 'recentHires', 'topPerformers', 'upcomingReviews'));
    }

    /**
     * Display employee directory
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Employee::class);

        $query = $request->get('search');
        $status = $request->get('status');
        $department = $request->get('department');
        $employmentType = $request->get('employment_type');

        $employees = $this->employeeService->searchEmployees($query, $status, $department, $employmentType);
        $departments = $this->employeeService->getAllDepartments();
        $employmentTypes = $this->employeeService->getAllEmploymentTypes();

        return view('employees.index', compact('employees', 'departments', 'employmentTypes'));
    }

    /**
     * Show employee profile
     */
    public function show(int $id): View
    {
        $employee = $this->employeeService->findById($id);

        if (!$employee) {
            abort(404);
        }

        Gate::authorize('view', $employee);

        $subordinates = $this->employeeService->getEmployeeSubordinates($id);
        $managers = $this->employeeService->getEmployeeManagers($id);
        $notes = $this->employeeService->getEmployeeNotes($id);
        $analytics = $this->employeeService->getEmployeeAnalytics($id);

        return view('employees.show', compact('employee', 'subordinates', 'managers', 'notes', 'analytics'));
    }

    /**
     * Show employee creation form
     */
    public function create(): View
    {
        Gate::authorize('create', Employee::class);

        $departments = $this->employeeService->getAllDepartments();
        $positions = $this->employeeService->getAllPositions();
        $managers = $this->employeeService->getAllManagers();

        return view('employees.create', compact('departments', 'positions', 'managers'));
    }

    /**
     * Store new employee
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Employee::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:employees,user_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other,prefer_not_to_say',
            'hire_date' => 'required|date',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:employees,id',
            'salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'employment_type' => 'required|string|in:full_time,part_time,contract,temporary,intern,freelance',
            'status' => 'required|string|in:active,inactive,terminated,pending,on_leave',
        ]);

        $employee = $this->employeeService->createEmployee($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully',
            'data' => $employee,
            'redirect' => route('employees.show', $employee->id)
        ]);
    }

    /**
     * Show employee edit form
     */
    public function edit(int $id): View
    {
        $employee = $this->employeeService->findById($id);

        if (!$employee) {
            abort(404);
        }

        Gate::authorize('update', $employee);

        $departments = $this->employeeService->getAllDepartments();
        $positions = $this->employeeService->getAllPositions();
        $managers = $this->employeeService->getAllManagers();

        return view('employees.edit', compact('employee', 'departments', 'positions', 'managers'));
    }

    /**
     * Update employee
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $employee = $this->employeeService->findById($id);

        if (!$employee) {
            abort(404);
        }

        Gate::authorize('update', $employee);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other,prefer_not_to_say',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'manager_id' => 'nullable|exists:employees,id',
            'salary' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'employment_type' => 'required|string|in:full_time,part_time,contract,temporary,intern,freelance',
            'status' => 'required|string|in:active,inactive,terminated,pending,on_leave',
        ]);

        $updated = $this->employeeService->updateEmployee($employee, $validated);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Employee updated successfully' : 'Failed to update employee',
            'data' => $updated ? $this->employeeService->findById($id) : null
        ]);
    }

    /**
     * Delete employee
     */
    public function destroy(int $id): JsonResponse
    {
        $employee = $this->employeeService->findById($id);

        if (!$employee) {
            abort(404);
        }

        Gate::authorize('delete', $employee);

        $deleted = $this->employeeService->deleteEmployee($employee);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Employee deleted successfully' : 'Failed to delete employee'
        ]);
    }

    /**
     * Display employee analytics dashboard
     */
    public function analytics(): View
    {
        Gate::authorize('viewAnalytics', Employee::class);

        $stats = $this->employeeService->getEmployeeStats();
        $statusStats = $this->employeeService->getEmployeeStatsByStatus();
        $departmentStats = $this->employeeService->getEmployeeStatsByDepartment();
        $employmentTypeStats = $this->employeeService->getEmployeeStatsByEmploymentType();
        $growthStats = $this->employeeService->getEmployeeGrowthStats('monthly');
        $turnoverStats = $this->employeeService->getEmployeeTurnoverStats();
        $performanceStats = $this->employeeService->getEmployeePerformanceStats();
        $salaryStats = $this->employeeService->getEmployeeSalaryStats();

        return view('employees.analytics', compact(
            'stats', 'statusStats', 'departmentStats', 'employmentTypeStats',
            'growthStats', 'turnoverStats', 'performanceStats', 'salaryStats'
        ));
    }

    /**
     * Display employee import/export interface
     */
    public function importExport(): View
    {
        Gate::authorize('importData', Employee::class);
        Gate::authorize('exportData', Employee::class);

        return view('employees.import-export');
    }

    /**
     * Import employees from file
     */
    public function import(Request $request): JsonResponse
    {
        Gate::authorize('importData', Employee::class);

        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240'
        ]);

        $result = $this->employeeService->importEmployees($request->file('file'));

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null
        ]);
    }

    /**
     * Export employees to file
     */
    public function export(Request $request): JsonResponse
    {
        Gate::authorize('exportData', Employee::class);

        $format = $request->get('format', 'csv');
        $filters = $request->only(['status', 'department', 'employment_type']);

        $result = $this->employeeService->exportEmployees($format, $filters);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'download_url' => $result['download_url'] ?? null
        ]);
    }

    /**
     * Display employee performance management interface
     */
    public function performance(): View
    {
        Gate::authorize('managePerformance', Employee::class);

        $employees = $this->employeeService->getAllEmployees();
        $performanceStats = $this->employeeService->getEmployeePerformanceStats();
        $upcomingReviews = $this->employeeService->getEmployeesWithUpcomingReviews(30);

        return view('employees.performance', compact('employees', 'performanceStats', 'upcomingReviews'));
    }

    /**
     * Display employee time-off management interface
     */
    public function timeOff(): View
    {
        Gate::authorize('manageTimeOff', Employee::class);

        $employees = $this->employeeService->getAllEmployees();
        $timeOffStats = $this->employeeService->getEmployeeTimeOffStats();
        $lowVacationEmployees = $this->employeeService->getEmployeesWithLowVacationDays(5);

        return view('employees.time-off', compact('employees', 'timeOffStats', 'lowVacationEmployees'));
    }

    /**
     * Display employee benefits administration interface
     */
    public function benefits(): View
    {
        Gate::authorize('manageBenefits', Employee::class);

        $employees = $this->employeeService->getAllEmployees();
        $benefitsStats = $this->employeeService->getEmployeeBenefitsStats();

        return view('employees.benefits', compact('employees', 'benefitsStats'));
    }

    /**
     * Display employee hierarchy visualization
     */
    public function hierarchy(): View
    {
        Gate::authorize('viewAny', Employee::class);

        $hierarchy = $this->employeeService->getEmployeeHierarchy();
        $departments = $this->employeeService->getAllDepartments();

        return view('employees.hierarchy', compact('hierarchy', 'departments'));
    }

    /**
     * Display employee training management interface
     */
    public function training(): View
    {
        Gate::authorize('manageTraining', Employee::class);

        $employees = $this->employeeService->getAllEmployees();
        $certifications = $this->employeeService->getAllCertifications();
        $skills = $this->employeeService->getAllSkills();

        return view('employees.training', compact('employees', 'certifications', 'skills'));
    }

    /**
     * Get employee data for AJAX requests
     */
    public function getEmployeeData(int $id): JsonResponse
    {
        $employee = $this->employeeService->findById($id);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        Gate::authorize('view', $employee);

        return response()->json([
            'success' => true,
            'data' => $employee
        ]);
    }

    /**
     * Get employee statistics for AJAX requests
     */
    public function getEmployeeStats(): JsonResponse
    {
        Gate::authorize('viewAnalytics', Employee::class);

        $stats = $this->employeeService->getEmployeeStats();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Search employees for AJAX requests
     */
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Employee::class);

        $query = $request->get('q');
        $limit = $request->get('limit', 10);

        $employees = $this->employeeService->searchEmployees($query, null, null, null, $limit);

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }
}
