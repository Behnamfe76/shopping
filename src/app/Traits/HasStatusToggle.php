<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasStatusToggle
{
    /**
     * Toggle active status
     */
    public function toggleActive(object $item): bool
    {
        $this->validateStatusToggle($item, 'active');

        $newStatus = !$item->is_active;
        $data = ['is_active' => $newStatus];

        $result = $this->repository->update($item, $data);

        if ($result) {
            $this->fireStatusChangedEvent($item, 'active', $newStatus);
        }

        return $result;
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(object $item): bool
    {
        $this->validateStatusToggle($item, 'featured');

        $newStatus = !$item->is_featured;
        $data = ['is_featured' => $newStatus];

        $result = $this->repository->update($item, $data);

        if ($result) {
            $this->fireStatusChangedEvent($item, 'featured', $newStatus);
        }

        return $result;
    }

    /**
     * Set active status
     */
    public function setActive(object $item, bool $status): bool
    {
        $this->validateStatusToggle($item, 'active');

        if ($item->is_active === $status) {
            return true; // No change needed
        }

        $data = ['is_active' => $status];
        $result = $this->repository->update($item, $data);

        if ($result) {
            $this->fireStatusChangedEvent($item, 'active', $status);
        }

        return $result;
    }

    /**
     * Set featured status
     */
    public function setFeatured(object $item, bool $status): bool
    {
        $this->validateStatusToggle($item, 'featured');

        if ($item->is_featured === $status) {
            return true; // No change needed
        }

        $data = ['is_featured' => $status];
        $result = $this->repository->update($item, $data);

        if ($result) {
            $this->fireStatusChangedEvent($item, 'featured', $status);
        }

        return $result;
    }

    /**
     * Get active items
     */
    public function getActive(): Collection
    {
        return $this->repository->findActive();
    }

    /**
     * Get active items as DTOs
     */
    public function getActiveDTO(): Collection
    {
        return $this->repository->findActiveDTO();
    }

    /**
     * Get featured items
     */
    public function getFeatured(): Collection
    {
        return $this->repository->findFeatured();
    }

    /**
     * Get featured items as DTOs
     */
    public function getFeaturedDTO(): Collection
    {
        return $this->repository->findFeaturedDTO();
    }

    /**
     * Validate status toggle operation
     */
    protected function validateStatusToggle(object $item, string $statusType): void
    {
        $rules = [
            'status' => 'required|in:active,featured',
        ];

        $data = ['status' => $statusType];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Fire status changed event
     */
    protected function fireStatusChangedEvent(object $item, string $statusType, bool $newStatus): void
    {
        // This method can be overridden in specific services to fire custom events
        // For now, we'll leave it empty as a placeholder
    }

    /**
     * Check if item is active
     */
    public function isActive(object $item): bool
    {
        return $item->is_active;
    }

    /**
     * Check if item is featured
     */
    public function isFeatured(object $item): bool
    {
        return $item->is_featured;
    }

    /**
     * Get active count
     */
    public function getActiveCount(): int
    {
        return $this->repository->getActiveBrandCount();
    }

    /**
     * Get featured count
     */
    public function getFeaturedCount(): int
    {
        return $this->repository->getFeaturedBrandCount();
    }
}
