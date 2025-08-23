<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Web;

use Fereydooni\Shopping\app\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Services\EmployeeNoteService;
use Fereydooni\Shopping\app\Models\EmployeeNote;
use Fereydooni\Shopping\app\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class EmployeeNoteController extends Controller
{
    public function __construct(
        private EmployeeNoteService $employeeNoteService
    ) {
        $this->authorizeResource(EmployeeNote::class, 'employeeNote');
    }

    /**
     * Display a listing of employee notes
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'type', 'priority', 'employee_id', 'is_private', 'is_archived']);
        $notes = $this->employeeNoteService->getEmployeeNotes(null, $filters);
        
        return view('employee-notes.index', compact('notes', 'filters'));
    }

    /**
     * Show the form for creating a new employee note
     */
    public function create(): View
    {
        $employees = Employee::all();
        return view('employee-notes.create', compact('employees'));
    }

    /**
     * Store a newly created employee note
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'note_type' => 'required|string',
            'priority' => 'required|string',
            'is_private' => 'boolean',
            'tags' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        $validated['user_id'] = Auth::id();
        
        try {
            $note = $this->employeeNoteService->createNote($validated);
            
            // Handle file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $path = $attachment->store('employee-notes', 'public');
                    $this->employeeNoteService->addAttachment($note->id, $path);
                }
            }
            
            return redirect()->route('employee-notes.show', $note->id)
                ->with('success', 'Employee note created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create employee note: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified employee note
     */
    public function show(EmployeeNote $employeeNote): View
    {
        return view('employee-notes.show', compact('employeeNote'));
    }

    /**
     * Show the form for editing the specified employee note
     */
    public function edit(EmployeeNote $employeeNote): View
    {
        $employees = Employee::all();
        return view('employee-notes.edit', compact('employeeNote', 'employees'));
    }

    /**
     * Update the specified employee note
     */
    public function update(Request $request, EmployeeNote $employeeNote): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'note_type' => 'required|string',
            'priority' => 'required|string',
            'is_private' => 'boolean',
            'tags' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        try {
            $note = $this->employeeNoteService->updateNote($employeeNote->id, $validated);
            
            // Handle file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $path = $attachment->store('employee-notes', 'public');
                    $this->employeeNoteService->addAttachment($note->id, $path);
                }
            }
            
            return redirect()->route('employee-notes.show', $note->id)
                ->with('success', 'Employee note updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to update employee note: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified employee note
     */
    public function destroy(EmployeeNote $employeeNote): RedirectResponse
    {
        try {
            $this->employeeNoteService->deleteNote($employeeNote->id);
            return redirect()->route('employee-notes.index')
                ->with('success', 'Employee note deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete employee note: ' . $e->getMessage()]);
        }
    }

    /**
     * Display employee notes for a specific employee
     */
    public function employeeNotes(Employee $employee): View
    {
        $notes = $this->employeeNoteService->getEmployeeNotes($employee->id);
        return view('employee-notes.employee', compact('employee', 'notes'));
    }

    /**
     * Show create form for a specific employee
     */
    public function createForEmployee(Employee $employee): View
    {
        return view('employee-notes.create', compact('employee'));
    }

    /**
     * Store note for a specific employee
     */
    public function storeForEmployee(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'note_type' => 'required|string',
            'priority' => 'required|string',
            'is_private' => 'boolean',
            'tags' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $validated['employee_id'] = $employee->id;
        $validated['user_id'] = Auth::id();
        
        try {
            $note = $this->employeeNoteService->createNote($validated);
            
            // Handle file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $path = $attachment->store('employee-notes', 'public');
                    $this->employeeNoteService->addAttachment($note->id, $path);
                }
            }
            
            return redirect()->route('employees.notes', $employee)
                ->with('success', 'Employee note created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create employee note: ' . $e->getMessage()]);
        }
    }

    /**
     * Search employee notes
     */
    public function search(Request $request): View
    {
        $query = $request->get('q');
        $notes = $this->employeeNoteService->searchEmployeeNotes(null, $query);
        
        return view('employee-notes.search', compact('notes', 'query'));
    }

    /**
     * Archive an employee note
     */
    public function archive(EmployeeNote $employeeNote): RedirectResponse
    {
        try {
            $this->employeeNoteService->archiveNote($employeeNote->id);
            return back()->with('success', 'Employee note archived successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to archive employee note: ' . $e->getMessage()]);
        }
    }

    /**
     * Unarchive an employee note
     */
    public function unarchive(EmployeeNote $employeeNote): RedirectResponse
    {
        try {
            $this->employeeNoteService->unarchiveNote($employeeNote->id);
            return back()->with('success', 'Employee note unarchived successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to unarchive employee note: ' . $e->getMessage()]);
        }
    }

    /**
     * Make an employee note private
     */
    public function makePrivate(EmployeeNote $employeeNote): RedirectResponse
    {
        try {
            $this->employeeNoteService->makePrivate($employeeNote->id);
            return back()->with('success', 'Employee note made private successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to make employee note private: ' . $e->getMessage()]);
        }
    }

    /**
     * Make an employee note public
     */
    public function makePublic(EmployeeNote $employeeNote): RedirectResponse
    {
        try {
            $this->employeeNoteService->makePublic($employeeNote->id);
            return back()->with('success', 'Employee note made public successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to make employee note public: ' . $e->getMessage()]);
        }
    }

    /**
     * Add tags to an employee note
     */
    public function addTags(Request $request, EmployeeNote $employeeNote): RedirectResponse
    {
        $validated = $request->validate([
            'tags' => 'required|array|min:1',
            'tags.*' => 'string|max:50',
        ]);

        try {
            $this->employeeNoteService->addTags($employeeNote->id, $validated['tags']);
            return back()->with('success', 'Tags added successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to add tags: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove tags from an employee note
     */
    public function removeTags(Request $request, EmployeeNote $employeeNote): RedirectResponse
    {
        $validated = $request->validate([
            'tags' => 'required|array|min:1',
            'tags.*' => 'string|max:50',
        ]);

        try {
            $this->employeeNoteService->removeTags($employeeNote->id, $validated['tags']);
            return back()->with('success', 'Tags removed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to remove tags: ' . $e->getMessage()]);
        }
    }

    /**
     * Add attachment to an employee note
     */
    public function addAttachment(Request $request, EmployeeNote $employeeNote): RedirectResponse
    {
        $request->validate([
            'attachment' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            $path = $request->file('attachment')->store('employee-notes', 'public');
            $this->employeeNoteService->addAttachment($employeeNote->id, $path);
            return back()->with('success', 'Attachment added successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to add attachment: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove attachment from an employee note
     */
    public function removeAttachment(EmployeeNote $employeeNote, string $attachment): RedirectResponse
    {
        try {
            $this->employeeNoteService->removeAttachment($employeeNote->id, $attachment);
            return back()->with('success', 'Attachment removed successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to remove attachment: ' . $e->getMessage()]);
        }
    }

    /**
     * Display note statistics
     */
    public function statistics(): View
    {
        $statistics = $this->employeeNoteService->getNoteStatistics(null);
        return view('employee-notes.statistics', compact('statistics'));
    }

    /**
     * Display employee note statistics
     */
    public function employeeStatistics(Employee $employee): View
    {
        $statistics = $this->employeeNoteService->getNoteStatistics($employee->id);
        return view('employee-notes.employee-statistics', compact('employee', 'statistics'));
    }

    /**
     * Export employee notes
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $request->validate([
            'format' => 'in:json,csv,xml',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        try {
            $format = $request->get('format', 'json');
            $employeeId = $request->get('employee_id');
            
            $data = $this->employeeNoteService->exportEmployeeNotes($employeeId, $format);
            
            $filename = 'employee-notes-' . date('Y-m-d') . '.' . $format;
            
            return response($data)
                ->header('Content-Type', $this->getContentType($format))
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to export notes: ' . $e->getMessage()]);
        }
    }

    /**
     * Import employee notes
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:json,csv,xml|max:10240',
            'employee_id' => 'required|exists:employees,id',
        ]);

        try {
            $file = $request->file('file');
            $format = $file->getClientOriginalExtension();
            $data = file_get_contents($file->getRealPath());
            $employeeId = $request->get('employee_id');
            
            $this->employeeNoteService->importEmployeeNotes($employeeId, $data, $format);
            
            return back()->with('success', 'Notes imported successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to import notes: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk archive notes
     */
    public function bulkArchive(Request $request): RedirectResponse
    {
        $request->validate([
            'note_ids' => 'required|array|min:1',
            'note_ids.*' => 'exists:employee_notes,id',
        ]);

        try {
            $this->employeeNoteService->bulkArchiveNotes($request->get('note_ids'));
            return back()->with('success', 'Notes archived successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to archive notes: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk delete notes
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'note_ids' => 'required|array|min:1',
            'note_ids.*' => 'exists:employee_notes,id',
        ]);

        try {
            $this->employeeNoteService->bulkDeleteNotes($request->get('note_ids'));
            return back()->with('success', 'Notes deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete notes: ' . $e->getMessage()]);
        }
    }

    /**
     * Get content type for export format
     */
    private function getContentType(string $format): string
    {
        return match ($format) {
            'json' => 'application/json',
            'csv' => 'text/csv',
            'xml' => 'application/xml',
            default => 'application/json',
        };
    }
}
