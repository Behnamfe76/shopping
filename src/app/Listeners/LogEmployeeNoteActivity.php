<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\EmployeeNoteArchived;
use Fereydooni\Shopping\app\Events\EmployeeNoteAttachmentAdded;
use Fereydooni\Shopping\app\Events\EmployeeNoteAttachmentRemoved;
use Fereydooni\Shopping\app\Events\EmployeeNoteCreated;
use Fereydooni\Shopping\app\Events\EmployeeNoteDeleted;
use Fereydooni\Shopping\app\Events\EmployeeNoteMadePrivate;
use Fereydooni\Shopping\app\Events\EmployeeNoteMadePublic;
use Fereydooni\Shopping\app\Events\EmployeeNoteTagged;
use Fereydooni\Shopping\app\Events\EmployeeNoteUnarchived;
use Fereydooni\Shopping\app\Events\EmployeeNoteUntagged;
use Fereydooni\Shopping\app\Events\EmployeeNoteUpdated;
use Illuminate\Support\Facades\Log;

class LogEmployeeNoteActivity
{
    public function handle(EmployeeNoteCreated $event): void
    {
        Log::info('Employee note created', [
            'note_id' => $event->employeeNote->id,
            'employee_id' => $event->employeeNote->employee_id,
            'user_id' => $event->employeeNote->user_id,
            'title' => $event->employeeNote->title,
            'type' => $event->employeeNote->note_type,
            'priority' => $event->employeeNote->priority,
        ]);
    }

    public function handleUpdate(EmployeeNoteUpdated $event): void
    {
        Log::info('Employee note updated', [
            'note_id' => $event->employeeNote->id,
            'employee_id' => $event->employeeNote->employee_id,
            'user_id' => $event->employeeNote->user_id,
            'title' => $event->employeeNote->title,
        ]);
    }

    public function handleDelete(EmployeeNoteDeleted $event): void
    {
        Log::info('Employee note deleted', [
            'note_id' => $event->employeeNote->id,
            'employee_id' => $event->employeeNote->employee_id,
            'user_id' => $event->employeeNote->user_id,
        ]);
    }

    public function handleArchive(EmployeeNoteArchived $event): void
    {
        Log::info('Employee note archived', [
            'note_id' => $event->employeeNote->id,
            'employee_id' => $event->employeeNote->employee_id,
        ]);
    }

    public function handleUnarchive(EmployeeNoteUnarchived $event): void
    {
        Log::info('Employee note unarchived', [
            'note_id' => $event->employeeNote->id,
            'employee_id' => $event->employeeNote->employee_id,
        ]);
    }

    public function handleMakePrivate(EmployeeNoteMadePrivate $event): void
    {
        Log::info('Employee note made private', [
            'note_id' => $event->employeeNote->id,
            'employee_id' => $event->employeeNote->employee_id,
        ]);
    }

    public function handleMakePublic(EmployeeNoteMadePublic $event): void
    {
        Log::info('Employee note made public', [
            'note_id' => $event->employeeNote->id,
            'employee_id' => $event->employeeNote->employee_id,
        ]);
    }

    public function handleTagged(EmployeeNoteTagged $event): void
    {
        Log::info('Employee note tagged', [
            'note_id' => $event->employeeNote->id,
            'employee_id' => $event->employeeNote->employee_id,
            'tags' => $event->tags,
        ]);
    }

    public function handleUntagged(EmployeeNoteUntagged $event): void
    {
        Log::info('Employee note untagged', [
            'note_id' => $event->employeeNote->id,
            'employee_id' => $event->employeeNote->employee_id,
            'tags' => $event->tags,
        ]);
    }

    public function handleAttachmentAdded(EmployeeNoteAttachmentAdded $event): void
    {
        Log::info('Employee note attachment added', [
            'note_id' => $event->employeeNote->id,
            'employee_id' => $event->employeeNote->employee_id,
            'attachment' => $event->attachmentPath,
        ]);
    }

    public function handleAttachmentRemoved(EmployeeNoteAttachmentRemoved $event): void
    {
        Log::info('Employee note attachment removed', [
            'note_id' => $event->employeeNote->id,
            'employee_id' => $event->employeeNote->employee_id,
            'attachment' => $event->attachmentPath,
        ]);
    }
}
