<?php

namespace Fereydooni\Shopping\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class GenerateProductSitemap implements ShouldQueue
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

        // Generate sitemap for products
        $this->generateProductSitemap();

        // Generate category sitemap if product category changed
        if ($product->category_id) {
            $this->generateCategorySitemap($product->category_id);
        }

        Log::info('Product sitemap generated', [
            'product_id' => $product->id,
            'event' => get_class($event),
        ]);
    }

    /**
     * Generate product sitemap.
     */
    private function generateProductSitemap(): void
    {
        // Implementation for generating product sitemap
        // This could use a sitemap generation package like spatie/laravel-sitemap

        // Example implementation:
        // $sitemap = Sitemap::create()
        //     ->add(Product::where('is_active', true)->get())
        //     ->writeToFile(public_path('sitemap-products.xml'));
    }

    /**
     * Generate category sitemap.
     */
    private function generateCategorySitemap(int $categoryId): void
    {
        // Implementation for generating category sitemap
        // This could include category pages and their products

        // Example implementation:
        // $category = Category::find($categoryId);
        // if ($category) {
        //     $sitemap = Sitemap::create()
        //         ->add($category)
        //         ->add($category->products()->where('is_active', true)->get())
        //         ->writeToFile(public_path("sitemap-category-{$categoryId}.xml"));
        // }
    }

    /**
     * Handle a job failure.
     */
    public function failed($event, \Throwable $exception): void
    {
        Log::error('Failed to generate product sitemap', [
            'product_id' => $event->product->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
