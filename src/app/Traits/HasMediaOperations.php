<?php

namespace Fereydooni\Shopping\app\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasMediaOperations
{
    /**
     * Upload media file
     */
    public function uploadMedia(Model $model, UploadedFile $file, string $collection = 'default', array $customProperties = []): Media
    {
        $this->validateMediaFile($file);

        return $model->addMedia($file)
            ->usingFileName(Str::uuid().'.'.$file->getClientOriginalExtension())
            ->withCustomProperties($customProperties)
            ->toMediaCollection($collection);
    }

    /**
     * Upload multiple media files
     */
    public function uploadMultipleMedia(Model $model, array $files, string $collection = 'default'): array
    {
        $media = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $media[] = $this->uploadMedia($model, $file, $collection);
            }
        }

        return $media;
    }

    /**
     * Delete media file
     */
    public function deleteMedia(object $model, int $mediaId): bool
    {
        $media = $model->getMedia()->find($mediaId);

        if ($media) {
            return $media->delete();
        }

        return false;
    }

    /**
     * Delete all media from collection
     */
    public function deleteAllMedia(object $model, string $collection = 'default'): bool
    {
        return $model->clearMediaCollection($collection);
    }

    /**
     * Get first media from collection
     */
    public function getFirstMedia(object $model, string $collection = 'default'): ?Media
    {
        return $model->getFirstMedia($collection);
    }

    /**
     * Get media URLs
     */
    public function getMediaUrls(object $model, string $collection = 'default'): array
    {
        return $model->getMedia($collection)->map(function ($media) {
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
            'file' => 'required|file',
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
    public function uploadLogo(object $model, UploadedFile $file): Media
    {
        return $this->uploadMedia($model, $file, 'logo');
    }

    /**
     * Upload banner
     */
    public function uploadBanner(object $model, UploadedFile $file): Media
    {
        return $this->uploadMedia($model, $file, 'banner');
    }

    /**
     * Get logo URL
     */
    public function getLogoUrl(object $model): ?string
    {
        $media = $this->getFirstMedia($model, 'logo');

        return $media ? $media->getUrl() : null;
    }

    /**
     * Get banner URL
     */
    public function getBannerUrl(object $model): ?string
    {
        $media = $this->getFirstMedia($model, 'banner');

        return $media ? $media->getUrl() : null;
    }

    /**
     * Delete logo
     */
    public function deleteLogo(object $model): bool
    {
        return $this->deleteAllMedia($model, 'logo');
    }

    /**
     * Delete banner
     */
    public function deleteBanner(object $model): bool
    {
        return $this->deleteAllMedia($model, 'banner');
    }

    public function addMedia(Model $model, $file, string $collection = 'default'): bool
    {
        if (! $model instanceof HasMedia) {
            return false;
        }

        $model->addMedia($file)->toMediaCollection($collection);

        return true;
    }

    public function removeMedia(Model $model, int $mediaId): bool
    {
        if (! $model instanceof HasMedia) {
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
        if (! $model instanceof HasMedia) {
            return collect();
        }

        return $model->getMedia($collection);
    }
}
