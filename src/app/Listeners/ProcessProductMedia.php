<?php

namespace Fereydooni\Shopping\app\Listeners;

use Fereydooni\Shopping\app\Events\ProductCreated;
use Fereydooni\Shopping\app\Events\ProductUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessProductMedia implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $product = $event->product;

        // Process product media
        $this->processMedia($product);

        // Generate thumbnails
        $this->generateThumbnails($product);

        // Optimize images
        $this->optimizeImages($product);

        Log::info('Product media processed', [
            'product_id' => $product->id,
            'event' => get_class($event)
        ]);
    }

    /**
     * Process product media.
     */
    private function processMedia($product): void
    {
        // Implementation for processing product media
        // This could include image resizing, format conversion, etc.

        // Example implementation:
        // if ($product->hasMedia('images')) {
        //     foreach ($product->getMedia('images') as $media) {
        //         $this->processImage($media);
        //     }
        // }
    }

    /**
     * Generate thumbnails.
     */
    private function generateThumbnails($product): void
    {
        // Implementation for generating thumbnails
        // This could use a package like intervention/image

        // Example implementation:
        // if ($product->hasMedia('images')) {
        //     foreach ($product->getMedia('images') as $media) {
        //         $this->generateThumbnail($media);
        //     }
        // }
    }

    /**
     * Optimize images.
     */
    private function optimizeImages($product): void
    {
        // Implementation for optimizing images
        // This could include compression, format optimization, etc.

        // Example implementation:
        // if ($product->hasMedia('images')) {
        //     foreach ($product->getMedia('images') as $media) {
        //         $this->optimizeImage($media);
        //     }
        // }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Failed to process product media', [
            'product_id' => $event->product->id,
            'error' => $exception->getMessage()
        ]);
    }
}
