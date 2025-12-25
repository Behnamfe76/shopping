<?php

namespace Fereydooni\Shopping\app\Actions;

use Exception;
use Fereydooni\Shopping\app\DTOs\EmployeeNoteDTO;
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
use Fereydooni\Shopping\app\Repositories\Interfaces\EmployeeNoteRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class CreateEmployeeNoteAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(array $data): EmployeeNoteDTO
    {
        try {
            DB::beginTransaction();

            $employeeNote = $this->repository->create($data);
            $dto = $this->repository->findDTO($employeeNote->id);

            Event::dispatch(new EmployeeNoteCreated($employeeNote));

            DB::commit();

            return $dto;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create employee note: '.$e->getMessage());
            throw $e;
        }
    }
}

class UpdateEmployeeNoteAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $id, array $data): ?EmployeeNoteDTO
    {
        try {
            DB::beginTransaction();

            $employeeNote = $this->repository->find($id);
            if (! $employeeNote) {
                throw new Exception('Employee note not found');
            }

            $updated = $this->repository->update($employeeNote, $data);
            if (! $updated) {
                throw new Exception('Failed to update employee note');
            }

            $dto = $this->repository->findDTO($id);
            Event::dispatch(new EmployeeNoteUpdated($employeeNote));

            DB::commit();

            return $dto;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update employee note: '.$e->getMessage());
            throw $e;
        }
    }
}

class DeleteEmployeeNoteAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $id): bool
    {
        try {
            DB::beginTransaction();

            $employeeNote = $this->repository->find($id);
            if (! $employeeNote) {
                throw new Exception('Employee note not found');
            }

            $deleted = $this->repository->delete($employeeNote);
            if ($deleted) {
                Event::dispatch(new EmployeeNoteDeleted($employeeNote));
            }

            DB::commit();

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete employee note: '.$e->getMessage());
            throw $e;
        }
    }
}

class ArchiveEmployeeNoteAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $id): bool
    {
        try {
            DB::beginTransaction();

            $employeeNote = $this->repository->find($id);
            if (! $employeeNote) {
                throw new Exception('Employee note not found');
            }

            $archived = $this->repository->archive($employeeNote);
            if ($archived) {
                event(new EmployeeNoteArchived($employeeNote));
            }

            DB::commit();

            return $archived;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to archive employee note: '.$e->getMessage());
            throw $e;
        }
    }
}

class UnarchiveEmployeeNoteAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $id): bool
    {
        try {
            DB::beginTransaction();

            $employeeNote = $this->repository->find($id);
            if (! $employeeNote) {
                throw new Exception('Employee note not found');
            }

            $unarchived = $this->repository->unarchive($employeeNote);
            if ($unarchived) {
                event(new EmployeeNoteUnarchived($employeeNote));
            }

            DB::commit();

            return $unarchived;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to unarchive employee note: '.$e->getMessage());
            throw $e;
        }
    }
}

class MakePrivateEmployeeNoteAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $id): bool
    {
        try {
            DB::beginTransaction();

            $employeeNote = $this->repository->find($id);
            if (! $employeeNote) {
                throw new Exception('Employee note not found');
            }

            $madePrivate = $this->repository->makePrivate($employeeNote);
            if ($madePrivate) {
                event(new EmployeeNoteMadePrivate($employeeNote));
            }

            DB::commit();

            return $madePrivate;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to make employee note private: '.$e->getMessage());
            throw $e;
        }
    }
}

class MakePublicEmployeeNoteAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $id): bool
    {
        try {
            DB::beginTransaction();

            $employeeNote = $this->repository->find($id);
            if (! $employeeNote) {
                throw new Exception('Employee note not found');
            }

            $madePublic = $this->repository->makePublic($employeeNote);
            if ($madePublic) {
                event(new EmployeeNoteMadePublic($employeeNote));
            }

            DB::commit();

            return $madePublic;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to make employee note public: '.$e->getMessage());
            throw $e;
        }
    }
}

class AddTagsToEmployeeNoteAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $id, array $tags): bool
    {
        try {
            DB::beginTransaction();

            $employeeNote = $this->repository->find($id);
            if (! $employeeNote) {
                throw new Exception('Employee note not found');
            }

            $tagsAdded = $this->repository->addTags($employeeNote, $tags);
            if ($tagsAdded) {
                event(new EmployeeNoteTagged($employeeNote, $tags));
            }

            DB::commit();

            return $tagsAdded;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add tags to employee note: '.$e->getMessage());
            throw $e;
        }
    }
}

class RemoveTagsFromEmployeeNoteAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $id, array $tags): bool
    {
        try {
            DB::beginTransaction();

            $employeeNote = $this->repository->find($id);
            if (! $employeeNote) {
                throw new Exception('Employee note not found');
            }

            $tagsRemoved = $this->repository->removeTags($employeeNote, $tags);
            if ($tagsRemoved) {
                event(new EmployeeNoteUntagged($employeeNote, $tags));
            }

            DB::commit();

            return $tagsRemoved;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove tags from employee note: '.$e->getMessage());
            throw $e;
        }
    }
}

class AddAttachmentToEmployeeNoteAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $id, string $attachmentPath): bool
    {
        try {
            DB::beginTransaction();

            $employeeNote = $this->repository->find($id);
            if (! $employeeNote) {
                throw new Exception('Employee note not found');
            }

            $attachmentAdded = $this->repository->addAttachment($employeeNote, $attachmentPath);
            if ($attachmentAdded) {
                event(new EmployeeNoteAttachmentAdded($employeeNote, $attachmentPath));
            }

            DB::commit();

            return $attachmentAdded;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add attachment to employee note: '.$e->getMessage());
            throw $e;
        }
    }
}

class RemoveAttachmentFromEmployeeNoteAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $id, string $attachmentPath): bool
    {
        try {
            DB::beginTransaction();

            $employeeNote = $this->repository->find($id);
            if (! $employeeNote) {
                throw new Exception('Employee note not found');
            }

            $attachmentRemoved = $this->repository->removeAttachment($employeeNote, $attachmentPath);
            if ($attachmentRemoved) {
                event(new EmployeeNoteAttachmentRemoved($employeeNote, $attachmentPath));
            }

            DB::commit();

            return $attachmentRemoved;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove attachment from employee note: '.$e->getMessage());
            throw $e;
        }
    }
}

class SearchEmployeeNotesAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(string $query): Collection
    {
        return $this->repository->searchNotes($query);
    }
}

class GetEmployeeNotesByTypeAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $employeeId, string $noteType): Collection
    {
        return $this->repository->findByEmployeeAndType($employeeId, $noteType);
    }
}

class GetEmployeeNotesByPriorityAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $employeeId, string $priority): Collection
    {
        return $this->repository->findByEmployeeAndPriority($employeeId, $priority);
    }
}

class GetEmployeeNoteStatisticsAction
{
    public function __construct(
        private EmployeeNoteRepositoryInterface $repository
    ) {}

    public function execute(int $employeeId): array
    {
        return [
            'total' => $this->repository->getEmployeeNoteCount($employeeId),
            'by_type' => [
                'performance' => $this->repository->getEmployeeNoteCountByType($employeeId, 'performance'),
                'general' => $this->repository->getEmployeeNoteCountByType($employeeId, 'general'),
                'warning' => $this->repository->getEmployeeNoteCountByType($employeeId, 'warning'),
                'praise' => $this->repository->getEmployeeNoteCountByType($employeeId, 'praise'),
                'incident' => $this->repository->getEmployeeNoteCountByType($employeeId, 'incident'),
                'training' => $this->repository->getEmployeeNoteCountByType($employeeId, 'training'),
                'goal' => $this->repository->getEmployeeNoteCountByType($employeeId, 'goal'),
                'feedback' => $this->repository->getEmployeeNoteCountByType($employeeId, 'feedback'),
                'other' => $this->repository->getEmployeeNoteCountByType($employeeId, 'other'),
            ],
            'by_priority' => [
                'low' => $this->repository->getEmployeeNoteCountByPriority($employeeId, 'low'),
                'medium' => $this->repository->getEmployeeNoteCountByPriority($employeeId, 'medium'),
                'high' => $this->repository->getEmployeeNoteCountByPriority($employeeId, 'high'),
                'urgent' => $this->repository->getEmployeeNoteCountByPriority($employeeId, 'urgent'),
            ],
            'private' => $this->repository->getPrivateNoteCount($employeeId),
            'public' => $this->repository->getPublicNoteCount($employeeId),
            'archived' => $this->repository->getArchivedNoteCount($employeeId),
            'active' => $this->repository->getActiveNoteCount($employeeId),
        ];
    }
}
