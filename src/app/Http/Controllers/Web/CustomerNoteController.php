<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Http\Requests\CustomerNote\StoreCustomerNoteRequest;
use Fereydooni\Shopping\app\Http\Requests\CustomerNote\UpdateCustomerNoteRequest;
use Fereydooni\Shopping\app\Models\CustomerNote;
use Fereydooni\Shopping\app\Services\CustomerNoteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerNoteController extends Controller
{
    public function __construct(
        protected CustomerNoteService $customerNoteService
    ) {}

    /**
     * Display a listing of customer notes.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', CustomerNote::class);

        $perPage = $request->get('per_page', 15);
        $notes = $this->customerNoteService->getNotesPaginated($perPage);

        $stats = $this->customerNoteService->getNoteStats();
        $types = $this->customerNoteService->getNoteTypes();
        $priorities = $this->customerNoteService->getNotePriorities();

        return view('customer-notes.index', compact('notes', 'stats', 'types', 'priorities'));
    }

    /**
     * Show the form for creating a new customer note.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', CustomerNote::class);

        $customerId = $request->get('customer_id');
        $types = $this->customerNoteService->getNoteTypes();
        $priorities = $this->customerNoteService->getNotePriorities();
        $templates = $this->customerNoteService->getNoteTemplates();

        return view('customer-notes.create', compact('customerId', 'types', 'priorities', 'templates'));
    }

    /**
     * Store a newly created customer note.
     */
    public function store(StoreCustomerNoteRequest $request): RedirectResponse
    {
        $this->authorize('create', CustomerNote::class);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $note = $this->customerNoteService->createNote($data);

        return redirect()
            ->route('customer-notes.show', $note)
            ->with('success', 'Customer note created successfully.');
    }

    /**
     * Display the specified customer note.
     */
    public function show(CustomerNote $customerNote): View
    {
        $this->authorize('view', $customerNote);

        $note = $this->customerNoteService->getNote($customerNote->id);
        $attachments = $this->customerNoteService->getCustomerNoteAttachments($note);

        return view('customer-notes.show', compact('note', 'attachments'));
    }

    /**
     * Show the form for editing the specified customer note.
     */
    public function edit(CustomerNote $customerNote): View
    {
        $this->authorize('update', $customerNote);

        $note = $this->customerNoteService->getNote($customerNote->id);
        $types = $this->customerNoteService->getNoteTypes();
        $priorities = $this->customerNoteService->getNotePriorities();

        return view('customer-notes.edit', compact('note', 'types', 'priorities'));
    }

    /**
     * Update the specified customer note.
     */
    public function update(UpdateCustomerNoteRequest $request, CustomerNote $customerNote): RedirectResponse
    {
        $this->authorize('update', $customerNote);

        $data = $request->validated();
        $updated = $this->customerNoteService->updateNote($customerNote, $data);

        if (! $updated) {
            return back()->with('error', 'Failed to update customer note.');
        }

        return redirect()
            ->route('customer-notes.show', $customerNote)
            ->with('success', 'Customer note updated successfully.');
    }

    /**
     * Remove the specified customer note.
     */
    public function destroy(CustomerNote $customerNote): RedirectResponse
    {
        $this->authorize('delete', $customerNote);

        $deleted = $this->customerNoteService->deleteNote($customerNote);

        if (! $deleted) {
            return back()->with('error', 'Failed to delete customer note.');
        }

        return redirect()
            ->route('customer-notes.index')
            ->with('success', 'Customer note deleted successfully.');
    }

    /**
     * Pin a customer note.
     */
    public function pin(CustomerNote $customerNote): RedirectResponse
    {
        $this->authorize('pin', $customerNote);

        $pinned = $this->customerNoteService->pinCustomerNote($customerNote);

        if (! $pinned) {
            return back()->with('error', 'Failed to pin customer note.');
        }

        return back()->with('success', 'Customer note pinned successfully.');
    }

    /**
     * Unpin a customer note.
     */
    public function unpin(CustomerNote $customerNote): RedirectResponse
    {
        $this->authorize('unpin', $customerNote);

        $unpinned = $this->customerNoteService->unpinCustomerNote($customerNote);

        if (! $unpinned) {
            return back()->with('error', 'Failed to unpin customer note.');
        }

        return back()->with('success', 'Customer note unpinned successfully.');
    }

    /**
     * Make a customer note private.
     */
    public function makePrivate(CustomerNote $customerNote): RedirectResponse
    {
        $this->authorize('makePrivate', $customerNote);

        $madePrivate = $this->customerNoteService->makeCustomerNotePrivate($customerNote);

        if (! $madePrivate) {
            return back()->with('error', 'Failed to make customer note private.');
        }

        return back()->with('success', 'Customer note made private successfully.');
    }

    /**
     * Make a customer note public.
     */
    public function makePublic(CustomerNote $customerNote): RedirectResponse
    {
        $this->authorize('makePublic', $customerNote);

        $madePublic = $this->customerNoteService->makeCustomerNotePublic($customerNote);

        if (! $madePublic) {
            return back()->with('error', 'Failed to make customer note public.');
        }

        return back()->with('success', 'Customer note made public successfully.');
    }

    /**
     * Add tag to customer note.
     */
    public function addTag(Request $request, CustomerNote $customerNote): RedirectResponse
    {
        $this->authorize('manageTags', $customerNote);

        $request->validate([
            'tag' => 'required|string|max:50',
        ]);

        $tagAdded = $this->customerNoteService->addCustomerNoteTag($customerNote, $request->tag);

        if (! $tagAdded) {
            return back()->with('error', 'Failed to add tag to customer note.');
        }

        return back()->with('success', 'Tag added successfully.');
    }

    /**
     * Remove tag from customer note.
     */
    public function removeTag(Request $request, CustomerNote $customerNote, string $tag): RedirectResponse
    {
        $this->authorize('manageTags', $customerNote);

        $tagRemoved = $this->customerNoteService->removeCustomerNoteTag($customerNote, $tag);

        if (! $tagRemoved) {
            return back()->with('error', 'Failed to remove tag from customer note.');
        }

        return back()->with('success', 'Tag removed successfully.');
    }

    /**
     * Add attachment to customer note.
     */
    public function addAttachment(Request $request, CustomerNote $customerNote): RedirectResponse
    {
        $this->authorize('manageAttachments', $customerNote);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $attachmentAdded = $this->customerNoteService->addCustomerNoteAttachment($customerNote, $request->file('file'));

        if (! $attachmentAdded) {
            return back()->with('error', 'Failed to add attachment to customer note.');
        }

        return back()->with('success', 'Attachment added successfully.');
    }

    /**
     * Remove attachment from customer note.
     */
    public function removeAttachment(CustomerNote $customerNote, int $mediaId): RedirectResponse
    {
        $this->authorize('manageAttachments', $customerNote);

        $attachmentRemoved = $this->customerNoteService->removeCustomerNoteAttachment($customerNote, $mediaId);

        if (! $attachmentRemoved) {
            return back()->with('error', 'Failed to remove attachment from customer note.');
        }

        return back()->with('success', 'Attachment removed successfully.');
    }

    /**
     * Get customer notes for a specific customer.
     */
    public function getCustomerNotes(Request $request, int $customerId): View
    {
        $this->authorize('viewCustomerNotes', $customerId);

        $perPage = $request->get('per_page', 15);
        $notes = $this->customerNoteService->getCustomerNotes($customerId);
        $stats = $this->customerNoteService->getNoteStatsByCustomer($customerId);
        $popularTags = $this->customerNoteService->getCustomerNotePopularTags($customerId);

        return view('customer-notes.customer-notes', compact('notes', 'stats', 'popularTags', 'customerId'));
    }

    /**
     * Search customer notes.
     */
    public function search(Request $request): View
    {
        $this->authorize('viewAny', CustomerNote::class);

        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $notes = $this->customerNoteService->searchNotes($request->query);
        $query = $request->query;

        return view('customer-notes.search', compact('notes', 'query'));
    }

    /**
     * Display customer note statistics.
     */
    public function stats(Request $request): View
    {
        $this->authorize('viewStats');

        $customerId = $request->get('customer_id');

        if ($customerId) {
            $stats = $this->customerNoteService->getNoteStatsByCustomer($customerId);
        } else {
            $stats = $this->customerNoteService->getNoteStats();
        }

        $types = $this->customerNoteService->getNoteTypes();
        $priorities = $this->customerNoteService->getNotePriorities();

        return view('customer-notes.stats', compact('stats', 'types', 'priorities', 'customerId'));
    }

    /**
     * Display customer note templates.
     */
    public function templates(): View
    {
        $this->authorize('manageTemplates');

        $templates = $this->customerNoteService->getNoteTemplates();

        return view('customer-notes.templates', compact('templates'));
    }

    /**
     * Export customer notes.
     */
    public function export(Request $request, int $customerId): RedirectResponse
    {
        $this->authorize('exportData');

        $request->validate([
            'format' => 'in:json,csv',
        ]);

        $format = $request->get('format', 'json');
        $exported = $this->customerNoteService->exportCustomerNotes($customerId, $format);

        $filename = "customer_notes_{$customerId}_".now()->format('Y-m-d_H-i-s').".{$format}";

        return response($exported)
            ->header('Content-Type', $format === 'json' ? 'application/json' : 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Show import form.
     */
    public function showImport(int $customerId): View
    {
        $this->authorize('importData');

        return view('customer-notes.import', compact('customerId'));
    }

    /**
     * Import customer notes.
     */
    public function import(Request $request, int $customerId): RedirectResponse
    {
        $this->authorize('importData');

        $request->validate([
            'notes' => 'required|array',
            'notes.*.title' => 'required|string|max:255',
            'notes.*.content' => 'required|string|max:10000',
            'notes.*.note_type' => 'required|string',
            'notes.*.priority' => 'required|string',
        ]);

        $results = $this->customerNoteService->importCustomerNotes($customerId, $request->notes);

        $message = "Import completed. {$results['success']} notes imported successfully.";
        if ($results['failed'] > 0) {
            $message .= " {$results['failed']} notes failed to import.";
        }

        return redirect()
            ->route('customer-notes.getCustomerNotes', $customerId)
            ->with('success', $message)
            ->with('import_results', $results);
    }
}
