<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasNotesManagement
{
    /**
     * Add note to item
     */
    public function addNote(object $item, string $note, string $type = 'general'): bool
    {
        $this->validateNote($note, $type);

        $currentNotes = $item->notes ? json_decode($item->notes, true) : [];

        $newNote = [
            'note' => $note,
            'type' => $type,
            'created_at' => now()->toISOString(),
            'user_id' => auth()->id(),
        ];

        $currentNotes[] = $newNote;

        $data = ['notes' => json_encode($currentNotes)];

        $result = $this->repository->update($item, $data);

        if ($result) {
            $this->fireNoteAddedEvent($item, $newNote);
        }

        return $result;
    }

    /**
     * Get all notes for item
     */
    public function getNotes(object $item): array
    {
        return $item->getNotes();
    }

    /**
     * Get notes by type
     */
    public function getNotesByType(object $item, string $type): array
    {
        return $item->getNotesByType($type);
    }

    /**
     * Delete note by index
     */
    public function deleteNote(object $item, int $noteIndex): bool
    {
        $notes = $this->getNotes($item);

        if (! isset($notes[$noteIndex])) {
            return false;
        }

        // Check if user can delete this note
        if (! $this->canDeleteNote($item, $notes[$noteIndex])) {
            return false;
        }

        unset($notes[$noteIndex]);
        $notes = array_values($notes); // Reindex array

        $data = ['notes' => json_encode($notes)];

        $result = $this->repository->update($item, $data);

        if ($result) {
            $this->fireNoteDeletedEvent($item, $noteIndex);
        }

        return $result;
    }

    /**
     * Update note by index
     */
    public function updateNote(object $item, int $noteIndex, string $note, ?string $type = null): bool
    {
        $notes = $this->getNotes($item);

        if (! isset($notes[$noteIndex])) {
            return false;
        }

        // Check if user can update this note
        if (! $this->canUpdateNote($item, $notes[$noteIndex])) {
            return false;
        }

        $this->validateNote($note, $type ?? $notes[$noteIndex]['type']);

        $notes[$noteIndex]['note'] = $note;
        if ($type) {
            $notes[$noteIndex]['type'] = $type;
        }
        $notes[$noteIndex]['updated_at'] = now()->toISOString();

        $data = ['notes' => json_encode($notes)];

        $result = $this->repository->update($item, $data);

        if ($result) {
            $this->fireNoteUpdatedEvent($item, $noteIndex, $notes[$noteIndex]);
        }

        return $result;
    }

    /**
     * Get note types
     */
    public function getNoteTypes(): array
    {
        return [
            'general' => 'General',
            'status_change' => 'Status Change',
            'payment' => 'Payment',
            'shipping' => 'Shipping',
            'refund' => 'Refund',
            'customer' => 'Customer',
            'internal' => 'Internal',
        ];
    }

    /**
     * Validate note
     */
    protected function validateNote(string $note, string $type): void
    {
        $rules = [
            'note' => 'required|string|max:1000',
            'type' => 'required|string|in:'.implode(',', array_keys($this->getNoteTypes())),
        ];

        $data = [
            'note' => $note,
            'type' => $type,
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Check if user can delete note
     */
    protected function canDeleteNote(object $item, array $note): bool
    {
        // Admin can delete any note
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        // User can delete their own notes
        return $note['user_id'] === auth()->id();
    }

    /**
     * Check if user can update note
     */
    protected function canUpdateNote(object $item, array $note): bool
    {
        // Admin can update any note
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        // User can update their own notes
        return $note['user_id'] === auth()->id();
    }

    /**
     * Fire note added event
     */
    protected function fireNoteAddedEvent(object $item, array $note): void
    {
        // This method can be overridden in specific services to fire custom events
        // For now, we'll leave it empty as a placeholder
    }

    /**
     * Fire note deleted event
     */
    protected function fireNoteDeletedEvent(object $item, int $noteIndex): void
    {
        // This method can be overridden in specific services to fire custom events
        // For now, we'll leave it empty as a placeholder
    }

    /**
     * Fire note updated event
     */
    protected function fireNoteUpdatedEvent(object $item, int $noteIndex, array $note): void
    {
        // This method can be overridden in specific services to fire custom events
        // For now, we'll leave it empty as a placeholder
    }

    /**
     * Get note count by type
     */
    public function getNoteCountByType(object $item, string $type): int
    {
        $notes = $this->getNotesByType($item, $type);

        return count($notes);
    }

    /**
     * Get recent notes
     */
    public function getRecentNotes(object $item, int $limit = 5): array
    {
        $notes = $this->getNotes($item);

        // Sort by created_at descending
        usort($notes, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return array_slice($notes, 0, $limit);
    }

    /**
     * Search notes
     */
    public function searchNotes(object $item, string $query): array
    {
        $notes = $this->getNotes($item);

        return array_filter($notes, function ($note) use ($query) {
            return stripos($note['note'], $query) !== false;
        });
    }
}
