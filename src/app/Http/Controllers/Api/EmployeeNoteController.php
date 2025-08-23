<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Fereydooni\Shopping\app\Services\EmployeeNoteService;

class EmployeeNoteController extends Controller
{
    public function __construct(
        protected EmployeeNoteService $employeeNoteService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $notes = $this->employeeNoteService->getAllNotes($perPage);
        
        return response()->json(['data' => $notes]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000',
            'note_type' => 'required|string',
            'priority' => 'required|string',
            'is_private' => 'boolean',
            'tags' => 'array',
        ]);
        
        $note = $this->employeeNoteService->createNote($validated);
        
        return response()->json([
            'message' => 'Employee note created successfully',
            'data' => $note
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $note = $this->employeeNoteService->findNote($id);
        
        if (!$note) {
            return response()->json(['message' => 'Employee note not found'], 404);
        }
        
        return response()->json(['data' => $note]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'content' => 'string|max:10000',
            'note_type' => 'string',
            'priority' => 'string',
            'is_private' => 'boolean',
            'tags' => 'array',
        ]);
        
        $note = $this->employeeNoteService->updateNote($id, $validated);
        
        if (!$note) {
            return response()->json(['message' => 'Employee note not found'], 404);
        }
        
        return response()->json([
            'message' => 'Employee note updated successfully',
            'data' => $note
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->employeeNoteService->deleteNote($id);
        
        if (!$deleted) {
            return response()->json(['message' => 'Employee note not found'], 404);
        }
        
        return response()->json(['message' => 'Employee note deleted successfully']);
    }

    public function employeeNotes(int $employeeId): JsonResponse
    {
        $notes = $this->employeeNoteService->getEmployeeNotes($employeeId);
        
        return response()->json(['data' => $notes]);
    }

    public function archive(int $id): JsonResponse
    {
        $archived = $this->employeeNoteService->archiveNote($id);
        
        if (!$archived) {
            return response()->json(['message' => 'Employee note not found'], 404);
        }
        
        return response()->json(['message' => 'Employee note archived successfully']);
    }

    public function unarchive(int $id): JsonResponse
    {
        $unarchived = $this->employeeNoteService->unarchiveNote($id);
        
        if (!$unarchived) {
            return response()->json(['message' => 'Employee note not found'], 404);
        }
        
        return response()->json(['message' => 'Employee note unarchived successfully']);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        $employeeId = $request->get('employee_id');
        
        if ($employeeId) {
            $notes = $this->employeeNoteService->searchEmployeeNotes($employeeId, $query);
        } else {
            $notes = $this->employeeNoteService->searchNotes($query);
        }
        
        return response()->json(['data' => $notes]);
    }
}
