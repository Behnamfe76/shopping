<?php

namespace Fereydooni\Shopping\app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Fereydooni\Shopping\app\Http\Requests\CustomerNote\StoreCustomerNoteRequest;
use Fereydooni\Shopping\app\Http\Requests\CustomerNote\UpdateCustomerNoteRequest;
use Fereydooni\Shopping\app\Http\Resources\CustomerNoteCollection;
use Fereydooni\Shopping\app\Http\Resources\CustomerNoteResource;
use Fereydooni\Shopping\app\Models\CustomerNote;
use Fereydooni\Shopping\app\Services\CustomerNoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerNoteController extends Controller
{
    public function __construct(
        protected CustomerNoteService $customerNoteService
    ) {}

    /**
     * Display a listing of customer notes.
     */
    public function index(Request $request): ResourceCollection
    {
        $this->authorize('viewAny', CustomerNote::class);

        $perPage = $request->get('per_page', 15);
        $notes = $this->customerNoteService->getNotesPaginated($perPage);

        return new CustomerNoteCollection($notes);
    }

    /**
     * Store a newly created customer note.
     */
    public function store(StoreCustomerNoteRequest $request): JsonResource
    {
        $this->authorize('create', CustomerNote::class);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $note = $this->customerNoteService->createNote($data);

        return new CustomerNoteResource($note);
    }

    /**
     * Display the specified customer note.
     */
    public function show(CustomerNote $customerNote): JsonResource
    {
        $this->authorize('view', $customerNote);

        $note = $this->customerNoteService->getNote($customerNote->id);

        return new CustomerNoteResource($note);
    }

    /**
     * Update the specified customer note.
     */
    public function update(UpdateCustomerNoteRequest $request, CustomerNote $customerNote): JsonResource
    {
        $this->authorize('update', $customerNote);

        $data = $request->validated();
        $updated = $this->customerNoteService->updateNote($customerNote, $data);

        if (! $updated) {
            abort(500, 'Failed to update customer note');
        }

        $note = $this->customerNoteService->getNote($customerNote->id);

        return new CustomerNoteResource($note);
    }

    /**
     * Remove the specified customer note.
     */
    public function destroy(CustomerNote $customerNote): JsonResponse
    {
        $this->authorize('delete', $customerNote);

        $deleted = $this->customerNoteService->deleteNote($customerNote);

        if (! $deleted) {
            abort(500, 'Failed to delete customer note');
        }

        return response()->json(['message' => 'Customer note deleted successfully']);
    }

    /**
     * Pin a customer note.
     */
    public function pin(CustomerNote $customerNote): JsonResponse
    {
        $this->authorize('pin', $customerNote);

        $pinned = $this->customerNoteService->pinCustomerNote($customerNote);

        if (! $pinned) {
            abort(500, 'Failed to pin customer note');
        }

        return response()->json(['message' => 'Customer note pinned successfully']);
    }

    /**
     * Unpin a customer note.
     */
    public function unpin(CustomerNote $customerNote): JsonResponse
    {
        $this->authorize('unpin', $customerNote);

        $unpinned = $this->customerNoteService->unpinCustomerNote($customerNote);

        if (! $unpinned) {
            abort(500, 'Failed to unpin customer note');
        }

        return response()->json(['message' => 'Customer note unpinned successfully']);
    }

    /**
     * Make a customer note private.
     */
    public function makePrivate(CustomerNote $customerNote): JsonResponse
    {
        $this->authorize('makePrivate', $customerNote);

        $madePrivate = $this->customerNoteService->makeCustomerNotePrivate($customerNote);

        if (! $madePrivate) {
            abort(500, 'Failed to make customer note private');
        }

        return response()->json(['message' => 'Customer note made private successfully']);
    }

    /**
     * Make a customer note public.
     */
    public function makePublic(CustomerNote $customerNote): JsonResponse
    {
        $this->authorize('makePublic', $customerNote);

        $madePublic = $this->customerNoteService->makeCustomerNotePublic($customerNote);

        if (! $madePublic) {
            abort(500, 'Failed to make customer note public');
        }

        return response()->json(['message' => 'Customer note made public successfully']);
    }

    /**
     * Add tag to customer note.
     */
    public function addTag(Request $request, CustomerNote $customerNote): JsonResponse
    {
        $this->authorize('manageTags', $customerNote);

        $request->validate([
            'tag' => 'required|string|max:50',
        ]);

        $tagAdded = $this->customerNoteService->addCustomerNoteTag($customerNote, $request->tag);

        if (! $tagAdded) {
            abort(500, 'Failed to add tag to customer note');
        }

        return response()->json(['message' => 'Tag added successfully']);
    }

    /**
     * Remove tag from customer note.
     */
    public function removeTag(Request $request, CustomerNote $customerNote, string $tag): JsonResponse
    {
        $this->authorize('manageTags', $customerNote);

        $tagRemoved = $this->customerNoteService->removeCustomerNoteTag($customerNote, $tag);

        if (! $tagRemoved) {
            abort(500, 'Failed to remove tag from customer note');
        }

        return response()->json(['message' => 'Tag removed successfully']);
    }

    /**
     * Add attachment to customer note.
     */
    public function addAttachment(Request $request, CustomerNote $customerNote): JsonResponse
    {
        $this->authorize('manageAttachments', $customerNote);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $attachmentAdded = $this->customerNoteService->addCustomerNoteAttachment($customerNote, $request->file('file'));

        if (! $attachmentAdded) {
            abort(500, 'Failed to add attachment to customer note');
        }

        return response()->json(['message' => 'Attachment added successfully']);
    }

    /**
     * Remove attachment from customer note.
     */
    public function removeAttachment(CustomerNote $customerNote, int $mediaId): JsonResponse
    {
        $this->authorize('manageAttachments', $customerNote);

        $attachmentRemoved = $this->customerNoteService->removeCustomerNoteAttachment($customerNote, $mediaId);

        if (! $attachmentRemoved) {
            abort(500, 'Failed to remove attachment from customer note');
        }

        return response()->json(['message' => 'Attachment removed successfully']);
    }

    /**
     * Get customer notes for a specific customer.
     */
    public function getCustomerNotes(Request $request, int $customerId): ResourceCollection
    {
        $this->authorize('viewCustomerNotes', $customerId);

        $perPage = $request->get('per_page', 15);
        $notes = $this->customerNoteService->getCustomerNotes($customerId);

        return new CustomerNoteCollection($notes);
    }

    /**
     * Search customer notes.
     */
    public function search(Request $request): ResourceCollection
    {
        $this->authorize('viewAny', CustomerNote::class);

        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $notes = $this->customerNoteService->searchNotes($request->query);

        return new CustomerNoteCollection($notes);
    }

    /**
     * Get customer note statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        $this->authorize('viewStats');

        $customerId = $request->get('customer_id');

        if ($customerId) {
            $stats = $this->customerNoteService->getNoteStatsByCustomer($customerId);
        } else {
            $stats = $this->customerNoteService->getNoteStats();
        }

        return response()->json($stats);
    }

    /**
     * Get customer note types.
     */
    public function types(): JsonResponse
    {
        $this->authorize('viewAny', CustomerNote::class);

        $types = $this->customerNoteService->getNoteTypes();

        return response()->json($types);
    }

    /**
     * Get customer note priorities.
     */
    public function priorities(): JsonResponse
    {
        $this->authorize('viewAny', CustomerNote::class);

        $priorities = $this->customerNoteService->getNotePriorities();

        return response()->json($priorities);
    }

    /**
     * Get customer note templates.
     */
    public function templates(): JsonResponse
    {
        $this->authorize('manageTemplates');

        $templates = $this->customerNoteService->getNoteTemplates();

        return response()->json($templates);
    }

    /**
     * Export customer notes.
     */
    public function export(Request $request, int $customerId): JsonResponse
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
     * Import customer notes.
     */
    public function import(Request $request, int $customerId): JsonResponse
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

        return response()->json([
            'message' => 'Import completed',
            'results' => $results,
        ]);
    }
}
