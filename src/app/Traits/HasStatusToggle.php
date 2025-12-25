<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasStatusToggle
{
    public function toggleActive(Model $model): bool
    {
        if (! $this->canToggleStatus($model, 'active')) {
            return false;
        }

        $model->is_active = ! $model->is_active;
        $saved = $model->save();

        if ($saved) {
            $this->logStatusChange($model, 'active', $model->is_active);
        }

        return $saved;
    }

    public function toggleFeatured(Model $model): bool
    {
        if (! $this->canToggleStatus($model, 'featured')) {
            return false;
        }

        $model->is_featured = ! $model->is_featured;
        $saved = $model->save();

        if ($saved) {
            $this->logStatusChange($model, 'featured', $model->is_featured);
        }

        return $saved;
    }

    public function publish(Model $model): bool
    {
        if (! $this->canChangeStatus($model, 'published')) {
            return false;
        }

        $model->status = 'published';
        $saved = $model->save();

        if ($saved) {
            $this->logStatusChange($model, 'status', 'published');
        }

        return $saved;
    }

    public function unpublish(Model $model): bool
    {
        if (! $this->canChangeStatus($model, 'draft')) {
            return false;
        }

        $model->status = 'draft';
        $saved = $model->save();

        if ($saved) {
            $this->logStatusChange($model, 'status', 'draft');
        }

        return $saved;
    }

    public function archive(Model $model): bool
    {
        if (! $this->canChangeStatus($model, 'archived')) {
            return false;
        }

        $model->status = 'archived';
        $saved = $model->save();

        if ($saved) {
            $this->logStatusChange($model, 'status', 'archived');
        }

        return $saved;
    }

    protected function canToggleStatus(Model $model, string $statusType): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        $permission = $this->getStatusPermission($statusType);

        return $user->can($permission, $model);
    }

    protected function canChangeStatus(Model $model, string $newStatus): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        $permission = $this->getStatusChangePermission($newStatus);

        return $user->can($permission, $model);
    }

    protected function getStatusPermission(string $statusType): string
    {
        $modelName = strtolower(class_basename($model));

        return "{$modelName}.toggle.{$statusType}";
    }

    protected function getStatusChangePermission(string $status): string
    {
        $modelName = strtolower(class_basename($model));

        return "{$modelName}.{$status}";
    }

    protected function logStatusChange(Model $model, string $field, $value): void
    {
        // Log status change for audit trail
        // This can be implemented based on your logging requirements
        logger()->info('Status changed', [
            'model' => get_class($model),
            'model_id' => $model->id,
            'field' => $field,
            'value' => $value,
            'user_id' => Auth::id(),
        ]);
    }
}
