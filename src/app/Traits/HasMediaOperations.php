<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

trait HasMediaOperations
{
    /**
     * Upload media file
     */
    public function uploadMedia(object $item, UploadedFile $file, string $collection = 'default'): Media
    {
        $this->validateMediaFile($file);

        return $item->addMedia($file)
            ->toMediaCollection($collection);
    }

    /**
     * Upload multiple media files
     */
    public function uploadMultipleMedia(object $item, array $files, string $collection = 'default'): array
    {
        $media = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $media[] = $this->uploadMedia($item, $file, $collection);
            }
        }

        return $media;
    }

    /**
     * Delete media file
     */
    public function deleteMedia(object $item, int $mediaId): bool
    {
        $media = $item->getMedia()->find($mediaId);

        if ($media) {
            return $media->delete();
        }

        return false;
    }

    /**
     * Delete all media from collection
     */
    public function deleteAllMedia(object $item, string $collection = 'default'): bool
    {
        return $item->clearMediaCollection($collection);
    }

    /**
     * Get first media from collection
     */
    public function getFirstMedia(object $item, string $collection = 'default'): ?Media
    {
        return $item->getFirstMedia($collection);
    }

    /**
     * Get media URLs
     */
    public function getMediaUrls(object $item, string $collection = 'default'): array
    {
        return $item->getMedia($collection)->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'url' => $media->getUrl(),
                'size' => $media->size,
                'mime_type' => $media->mime_type,
            ];
        })->toArray();
    }

    /**
     * Validate media file
     */
    protected function validateMediaFile(UploadedFile $file): void
    {
        $rules = [
            'file' => 'required|file|max:10240', // 10MB max
        ];

        $data = ['file' => $file];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Upload logo
     */
    public function uploadLogo(object $item, UploadedFile $file): Media
    {
        return $this->uploadMedia($item, $file, 'logo');
    }

    /**
     * Upload banner
     */
    public function uploadBanner(object $item, UploadedFile $file): Media
    {
        return $this->uploadMedia($item, $file, 'banner');
    }

    /**
     * Get logo URL
     */
    public function getLogoUrl(object $item): ?string
    {
        $media = $this->getFirstMedia($item, 'logo');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get banner URL
     */
    public function getBannerUrl(object $item): ?string
    {
        $media = $this->getFirstMedia($item, 'banner');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Delete logo
     */
    public function deleteLogo(object $item): bool
    {
        return $this->deleteAllMedia($item, 'logo');
    }

    /**
     * Delete banner
     */
    public function deleteBanner(object $item): bool
    {
        return $this->deleteAllMedia($item, 'banner');
    }

    public function addMedia(Model $model, $file, string $collection = 'default'): bool
    {
        if (!$model instanceof HasMedia) {
            return false;
        }

        $model->addMedia($file)->toMediaCollection($collection);
        return true;
    }

    public function removeMedia(Model $model, int $mediaId): bool
    {
        if (!$model instanceof HasMedia) {
            return false;
        }

        $media = $model->media()->find($mediaId);
        if ($media) {
            $media->delete();
            return true;
        }

        return false;
    }

    public function getMedia(Model $model, string $collection = 'default'): Collection
    {
        if (!$model instanceof HasMedia) {
            return collect();
        }

        return $model->getMedia($collection);
    }
}
