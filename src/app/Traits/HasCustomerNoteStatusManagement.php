<?php

namespace Fereydooni\Shopping\app\Traits;

use Fereydooni\Shopping\app\Models\CustomerNote;

trait HasCustomerNoteStatusManagement
{
    /**
     * Pin a customer note
     */
    public function pinCustomerNote(CustomerNote $note): bool
    {
        $result = $this->repository->pin($note);

        if ($result) {
            $this->fireCustomerNotePinnedEvent($note);
        }

        return $result;
    }

    /**
     * Unpin a customer note
     */
    public function unpinCustomerNote(CustomerNote $note): bool
    {
        $result = $this->repository->unpin($note);

        if ($result) {
            $this->fireCustomerNoteUnpinnedEvent($note);
        }

        return $result;
    }

    /**
     * Make a customer note private
     */
    public function makeCustomerNotePrivate(CustomerNote $note): bool
    {
        $result = $this->repository->makePrivate($note);

        if ($result) {
            $this->fireCustomerNoteMadePrivateEvent($note);
        }

        return $result;
    }

    /**
     * Make a customer note public
     */
    public function makeCustomerNotePublic(CustomerNote $note): bool
    {
        $result = $this->repository->makePublic($note);

        if ($result) {
            $this->fireCustomerNoteMadePublicEvent($note);
        }

        return $result;
    }

    /**
     * Add tag to customer note
     */
    public function addCustomerNoteTag(CustomerNote $note, string $tag): bool
    {
        $result = $this->repository->addTag($note, $tag);

        if ($result) {
            $this->fireCustomerNoteTagAddedEvent($note, $tag);
        }

        return $result;
    }

    /**
     * Remove tag from customer note
     */
    public function removeCustomerNoteTag(CustomerNote $note, string $tag): bool
    {
        $result = $this->repository->removeTag($note, $tag);

        if ($result) {
            $this->fireCustomerNoteTagRemovedEvent($note, $tag);
        }

        return $result;
    }

    /**
     * Add attachment to customer note
     */
    public function addCustomerNoteAttachment(CustomerNote $note, $file): bool
    {
        $result = $this->repository->addAttachment($note, $file);

        if ($result) {
            $this->fireCustomerNoteAttachmentAddedEvent($note, $file);
        }

        return $result;
    }

    /**
     * Remove attachment from customer note
     */
    public function removeCustomerNoteAttachment(CustomerNote $note, int $mediaId): bool
    {
        $result = $this->repository->removeAttachment($note, $mediaId);

        if ($result) {
            $this->fireCustomerNoteAttachmentRemovedEvent($note, $mediaId);
        }

        return $result;
    }

    /**
     * Validate customer note status change
     */
    protected function validateCustomerNoteStatusChange(CustomerNote $note, string $action): bool
    {
        // Check if user has permission to perform the action
        $userId = auth()->id();

        return match ($action) {
            'pin', 'unpin' => $this->canEditCustomerNote($note, $userId),
            'make_private', 'make_public' => $this->canEditCustomerNote($note, $userId),
            'add_tag', 'remove_tag' => $this->canEditCustomerNote($note, $userId),
            'add_attachment', 'remove_attachment' => $this->canEditCustomerNote($note, $userId),
            default => false,
        };
    }

    /**
     * Get customer note status change notes
     */
    protected function getCustomerNoteStatusChangeNote(string $action, array $data = []): string
    {
        return match ($action) {
            'pin' => 'Note pinned',
            'unpin' => 'Note unpinned',
            'make_private' => 'Note made private',
            'make_public' => 'Note made public',
            'add_tag' => 'Tag "'.($data['tag'] ?? '').'" added',
            'remove_tag' => 'Tag "'.($data['tag'] ?? '').'" removed',
            'add_attachment' => 'Attachment added',
            'remove_attachment' => 'Attachment removed',
            default => 'Status changed',
        };
    }

    // Event firing methods (can be overridden in specific services)
    protected function fireCustomerNotePinnedEvent(CustomerNote $note): void
    {
        // Override in specific service to fire custom events
    }

    protected function fireCustomerNoteUnpinnedEvent(CustomerNote $note): void
    {
        // Override in specific service to fire custom events
    }

    protected function fireCustomerNoteMadePrivateEvent(CustomerNote $note): void
    {
        // Override in specific service to fire custom events
    }

    protected function fireCustomerNoteMadePublicEvent(CustomerNote $note): void
    {
        // Override in specific service to fire custom events
    }

    protected function fireCustomerNoteTagAddedEvent(CustomerNote $note, string $tag): void
    {
        // Override in specific service to fire custom events
    }

    protected function fireCustomerNoteTagRemovedEvent(CustomerNote $note, string $tag): void
    {
        // Override in specific service to fire custom events
    }

    protected function fireCustomerNoteAttachmentAddedEvent(CustomerNote $note, $file): void
    {
        // Override in specific service to fire custom events
    }

    protected function fireCustomerNoteAttachmentRemovedEvent(CustomerNote $note, int $mediaId): void
    {
        // Override in specific service to fire custom events
    }
}
