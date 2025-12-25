<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Http\Requests\SearchEmployeeRequest;
use Fereydooni\Shopping\app\Http\Requests\StoreEmployeeRequest;
use Fereydooni\Shopping\app\Http\Requests\UpdateEmployeeRequest;
use Fereydooni\Shopping\app\Http\Resources\EmployeeAnalyticsResource;
use Fereydooni\Shopping\app\Http\Resources\EmployeeCollection;
use Fereydooni\Shopping\app\Http\Resources\EmployeeResource;
use Fereydooni\Shopping\app\Http\Resources\EmployeeSearchResource;
use Fereydooni\Shopping\app\Models\Employee;
use Fereydooni\Shopping\app\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        private EmployeeService $employeeService
    ) {
        $this->authorizeResource(Employee::class, 'employee');
    }

    /**
     * Display a listing of employees.
     */
    public function index(Request $request): EmployeeCollection
    {
        $perPage = $request->get('per_page', 15);
        $employees = $this->employeeService->getPaginatedEmployees($perPage);

        return new EmployeeCollection($employees);
    }

    /**
     * Store a newly created employee.
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        try {
            $employee = $this->employeeService->onboardEmployee($request->validated());

            return response()->json([
                'message' => 'Employee created successfully',
                'data' => new EmployeeResource($employee),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee): JsonResponse
    {
        return response()->json([
            'data' => new EmployeeResource($employee),
        ]);
    }

    /**
     * Update the specified employee.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        try {
            $updatedEmployee = $this->employeeService->updateEmployeeProfile($employee, $request->validated());

            return response()->json([
                'message' => 'Employee updated successfully',
                'data' => new EmployeeResource($updatedEmployee),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee): JsonResponse
    {
        try {
            $this->employeeService->deleteEmployee($employee);

            return response()->json([
                'message' => 'Employee deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search employees.
     */
    public function search(SearchEmployeeRequest $request): EmployeeSearchResource
    {
        $query = $request->get('query', '');
        $department = $request->get('department');
        $position = $request->get('position');
        $status = $request->get('status');
        $employmentType = $request->get('employment_type');

        $employees = $this->employeeService->search($query);

        return new EmployeeSearchResource($employees, $query, $department, $position, $status, $employmentType);
    }

    /**
     * Activate employee.
     */
    public function activate(Employee $employee): JsonResponse
    {
        $this->authorize('activate', $employee);

        try {
            $success = $this->employeeService->activateEmployee($employee);

            if ($success) {
                return response()->json([
                    'message' => 'Employee activated successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to activate employee',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to activate employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deactivate employee.
     */
    public function deactivate(Employee $employee): JsonResponse
    {
        $this->authorize('deactivate', $employee);

        try {
            $success = $this->employeeService->deactivateEmployee($employee);

            if ($success) {
                return response()->json([
                    'message' => 'Employee deactivated successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to deactivate employee',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to deactivate employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Terminate employee.
     */
    public function terminate(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('terminate', $employee);

        $request->validate([
            'reason' => 'nullable|string|max:500',
            'termination_date' => 'nullable|date',
        ]);

        try {
            $success = $this->employeeService->terminateEmployee(
                $employee,
                $request->get('reason'),
                $request->get('termination_date')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Employee terminated successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to terminate employee',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to terminate employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Rehire employee.
     */
    public function rehire(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('rehire', $employee);

        $request->validate([
            'hire_date' => 'nullable|date',
        ]);

        try {
            $success = $this->employeeService->rehireEmployee(
                $employee,
                $request->get('hire_date')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Employee rehired successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to rehire employee',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to rehire employee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update employee salary.
     */
    public function updateSalary(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('manageSalary', $employee);

        $request->validate([
            'salary' => 'required|numeric|min:0',
            'effective_date' => 'nullable|date',
        ]);

        try {
            $success = $this->employeeService->updateSalary(
                $employee,
                $request->get('salary'),
                $request->get('effective_date')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Employee salary updated successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to update employee salary',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee salary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update employee position.
     */
    public function updatePosition(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('update', $employee);

        $request->validate([
            'position' => 'required|string|max:100',
            'effective_date' => 'nullable|date',
        ]);

        try {
            $success = $this->employeeService->updatePosition(
                $employee,
                $request->get('position'),
                $request->get('effective_date')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Employee position updated successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to update employee position',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee position',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update employee department.
     */
    public function updateDepartment(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('update', $employee);

        $request->validate([
            'department' => 'required|string|max:100',
            'effective_date' => 'nullable|date',
        ]);

        try {
            $success = $this->employeeService->updateDepartment(
                $employee,
                $request->get('department'),
                $request->get('effective_date')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Employee department updated successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to update employee department',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee department',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update employee performance rating.
     */
    public function updatePerformance(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('managePerformance', $employee);

        $request->validate([
            'performance_rating' => 'required|numeric|min:1.0|max:5.0',
            'review_date' => 'nullable|date',
        ]);

        try {
            $success = $this->employeeService->updatePerformanceRating(
                $employee,
                $request->get('performance_rating'),
                $request->get('review_date')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Employee performance rating updated successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to update employee performance rating',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee performance rating',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Schedule performance review.
     */
    public function scheduleReview(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('managePerformance', $employee);

        $request->validate([
            'review_date' => 'required|date|after:today',
        ]);

        try {
            $success = $this->employeeService->schedulePerformanceReview(
                $employee,
                $request->get('review_date')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Performance review scheduled successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to schedule performance review',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to schedule performance review',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Request time off.
     */
    public function requestTimeOff(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('manageTimeOff', $employee);

        $request->validate([
            'type' => 'required|string|in:vacation,sick',
            'days' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $success = $this->employeeService->requestTimeOff(
                $employee,
                $request->get('type'),
                $request->get('days'),
                $request->get('start_date'),
                $request->get('end_date'),
                $request->get('reason')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Time off request submitted successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to submit time off request',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to submit time off request',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enroll in benefits.
     */
    public function enrollBenefits(Employee $employee): JsonResponse
    {
        $this->authorize('manageBenefits', $employee);

        try {
            $result = $this->employeeService->enrollInBenefits($employee);

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message'],
                    'data' => new EmployeeResource($employee->fresh()),
                    'enrollment_date' => $result['enrollment_date'],
                    'coverage_start_date' => $result['coverage_start_date'],
                ]);
            }

            return response()->json([
                'message' => $result['message'],
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to enroll in benefits',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unenroll from benefits.
     */
    public function unenrollBenefits(Employee $employee): JsonResponse
    {
        $this->authorize('manageBenefits', $employee);

        try {
            $result = $this->employeeService->unenrollFromBenefits($employee);

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message'],
                    'data' => new EmployeeResource($employee->fresh()),
                    'unenrollment_date' => $result['unenrollment_date'],
                ]);
            }

            return response()->json([
                'message' => $result['message'],
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to unenroll from benefits',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign manager.
     */
    public function assignManager(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('manageHierarchy', $employee);

        $request->validate([
            'manager_id' => 'required|integer|exists:employees,id',
        ]);

        try {
            $success = $this->employeeService->assignManager(
                $employee,
                $request->get('manager_id')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Manager assigned successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to assign manager',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to assign manager',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove manager.
     */
    public function removeManager(Employee $employee): JsonResponse
    {
        $this->authorize('manageHierarchy', $employee);

        try {
            $success = $this->employeeService->removeManager($employee);

            if ($success) {
                return response()->json([
                    'message' => 'Manager removed successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to remove manager',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove manager',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get employee subordinates.
     */
    public function subordinates(Employee $employee): JsonResponse
    {
        $this->authorize('view', $employee);

        $subordinates = $this->employeeService->getEmployeeSubordinates($employee->id);

        return response()->json([
            'data' => EmployeeResource::collection($subordinates),
        ]);
    }

    /**
     * Get employee managers.
     */
    public function managers(Employee $employee): JsonResponse
    {
        $this->authorize('view', $employee);

        $managers = $this->employeeService->getEmployeeManagers($employee->id);

        return response()->json([
            'data' => EmployeeResource::collection($managers),
        ]);
    }

    /**
     * Get employee hierarchy.
     */
    public function hierarchy(Employee $employee): JsonResponse
    {
        $this->authorize('viewOrganizationalChart');

        $hierarchy = $this->employeeService->getEmployeeHierarchy($employee->id);

        return response()->json([
            'data' => $hierarchy,
        ]);
    }

    /**
     * Get employee analytics.
     */
    public function analytics(Request $request): JsonResponse
    {
        $this->authorize('viewAnalytics');

        $department = $request->get('department');
        $period = $request->get('period', 'current');

        $analytics = $this->employeeService->generateEmployeeReport($department, $period);

        return response()->json([
            'data' => new EmployeeAnalyticsResource($analytics),
        ]);
    }

    /**
     * Get dashboard data.
     */
    public function dashboard(): JsonResponse
    {
        $this->authorize('viewDashboard');

        $dashboardData = $this->employeeService->getEmployeeDashboardData();

        return response()->json([
            'data' => $dashboardData,
        ]);
    }

    /**
     * Update employee skills.
     */
    public function updateSkills(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('manageSkills', $employee);

        $request->validate([
            'skills' => 'required|array',
            'skills.*' => 'string|max:100',
        ]);

        try {
            $success = $this->employeeService->updateSkills(
                $employee,
                $request->get('skills')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Employee skills updated successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to update employee skills',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee skills',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update employee certifications.
     */
    public function updateCertifications(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('manageCertifications', $employee);

        $request->validate([
            'certifications' => 'required|array',
            'certifications.*' => 'string|max:100',
        ]);

        try {
            $success = $this->employeeService->updateCertifications(
                $employee,
                $request->get('certifications')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Employee certifications updated successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to update employee certifications',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee certifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update employee training.
     */
    public function updateTraining(Request $request, Employee $employee): JsonResponse
    {
        $this->authorize('manageTraining', $employee);

        $request->validate([
            'training_completed' => 'required|array',
            'training_completed.*' => 'string|max:100',
        ]);

        try {
            $success = $this->employeeService->updateTrainingRecords(
                $employee,
                $request->get('training_completed')
            );

            if ($success) {
                return response()->json([
                    'message' => 'Employee training records updated successfully',
                    'data' => new EmployeeResource($employee->fresh()),
                ]);
            }

            return response()->json([
                'message' => 'Failed to update employee training records',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee training records',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
