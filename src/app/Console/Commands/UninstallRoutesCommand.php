<?php

namespace Fereydooni\Shopping\app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UninstallRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopping:uninstall
                            {--route=* : The type of routes to uninstall (api, web, or both)}
                            {--force : Force uninstall without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall shopping package routes from the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routeTypes = $this->option('route');
        $force = $this->option('force');

        // If no route types specified, ask user
        if (empty($routeTypes)) {
            $routeTypes = $this->choice(
                'Which routes would you like to uninstall?',
                ['api', 'web', 'both'],
                'both'
            );

            if ($routeTypes === 'both') {
                $routeTypes = ['api', 'web'];
            } else {
                $routeTypes = [$routeTypes];
            }
        }

        if (! $force) {
            $confirmed = $this->confirm('Are you sure you want to uninstall shopping package routes?');
            if (! $confirmed) {
                $this->info('Uninstallation cancelled.');

                return;
            }
        }

        $this->info('Uninstalling shopping package routes...');

        foreach ($routeTypes as $routeType) {
            $this->uninstallRouteType($routeType);
        }

        $this->info('Routes uninstallation completed successfully!');
    }

    /**
     * Uninstall routes for a specific type (api or web).
     */
    protected function uninstallRouteType(string $routeType): void
    {
        $this->info("Uninstalling {$routeType} routes...");

        $routesPath = base_path("routes/{$routeType}.php");

        if (! File::exists($routesPath)) {
            $this->warn("Routes file {$routeType}.php not found!");

            return;
        }

        $content = File::get($routesPath);

        if (! str_contains($content, 'shopping')) {
            $this->warn("No shopping routes found in {$routeType}.php");

            return;
        }

        // Remove shopping routes
        $cleanedContent = $this->removeShoppingRoutes($content);

        File::put($routesPath, $cleanedContent);

        $this->info("✓ {$routeType} routes uninstalled successfully!");
    }

    /**
     * Remove shopping routes from content.
     */
    protected function removeShoppingRoutes(string $content): string
    {
        $lines = explode("\n", $content);
        $filteredLines = [];
        $skipNextLines = false;
        $braceCount = 0;
        $inShoppingSection = false;

        foreach ($lines as $line) {
            // Check if we're entering a shopping routes section
            if (str_contains($line, '// Shopping Package Routes') ||
                str_contains($line, 'shopping') && str_contains($line, 'Route::')) {
                $inShoppingSection = true;
                $skipNextLines = true;
                $braceCount = 0;

                continue;
            }

            if ($skipNextLines) {
                // Count braces to know when to stop skipping
                $braceCount += substr_count($line, '{') - substr_count($line, '}');

                if ($braceCount <= 0 && $inShoppingSection) {
                    $skipNextLines = false;
                    $inShoppingSection = false;
                }

                continue;
            }

            $filteredLines[] = $line;
        }

        return implode("\n", $filteredLines);
    }
}
