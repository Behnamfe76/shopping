<?php

namespace Fereydooni\Shopping\app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopping:seed
                            {--class= : The class name of the seeder to run}
                            {--force : Force the operation without confirmation}
                            {--fresh : Run fresh migrations before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run seeders for the shopping package';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $class = $this->option('class');
        $force = $this->option('force');
        $fresh = $this->option('fresh');

        // If fresh option is provided, run fresh migrations first
        if ($fresh) {
            $this->info('Running fresh migrations...');
            Artisan::call('migrate:fresh', [], $this->getOutput());
        }

        if ($class) {
            $this->runSpecificSeeder($class, $force);
        } else {
            $this->runAllSeeders($force);
        }
    }

    /**
     * Run a specific seeder class.
     */
    protected function runSpecificSeeder(string $class, bool $force): void
    {
        // Check if the class exists in the package seeders
        $seederClass = $this->resolveSeederClass($class);

        if (! $seederClass) {
            $this->error("Seeder class '{$class}' not found in the shopping package.");
            $this->info('Available seeders:');
            $this->listAvailableSeeders();

            return;
        }

        if (! $force) {
            if (! $this->confirm("Are you sure you want to run the '{$class}' seeder?")) {
                $this->info('Seeding cancelled.');

                return;
            }
        }

        $this->info("Running seeder: {$class}");

        try {
            Artisan::call('db:seed', [
                '--class' => $seederClass,
                '--force' => true,
            ], $this->getOutput());

            $this->info("Seeder '{$class}' completed successfully!");
        } catch (\Exception $e) {
            $this->error("Error running seeder '{$class}': ".$e->getMessage());
        }
    }

    /**
     * Run all seeders for the package.
     */
    protected function runAllSeeders(bool $force): void
    {
        if (! $force) {
            if (! $this->confirm('Are you sure you want to run all shopping package seeders?')) {
                $this->info('Seeding cancelled.');

                return;
            }
        }

        $this->info('Running all shopping package seeders...');

        try {
            Artisan::call('db:seed', [
                '--class' => \Fereydooni\Shopping\database\seeders\ShoppingDatabaseSeeder::class,
                '--force' => true,
            ], $this->getOutput());

            $this->info('All shopping package seeders completed successfully!');
        } catch (\Exception $e) {
            $this->error('Error running seeders: '.$e->getMessage());
        }
    }

    /**
     * Resolve the full class name for a seeder.
     */
    protected function resolveSeederClass(string $class): ?string
    {
        // Remove .php extension if provided
        $class = str_replace('.php', '', $class);

        // If it's already a full class name, return it
        if (class_exists($class)) {
            return $class;
        }

        // Try to resolve as a package seeder
        $packageSeederClass = "Fereydooni\\Shopping\\database\\seeders\\{$class}";

        if (class_exists($packageSeederClass)) {
            return $packageSeederClass;
        }

        // Check if the seeder file exists in the package
        $seederPath = __DIR__."/../../../database/seeders/{$class}.php";

        if (File::exists($seederPath)) {
            return $packageSeederClass;
        }

        return null;
    }

    /**
     * List all available seeders in the package.
     */
    protected function listAvailableSeeders(): void
    {
        $seedersPath = __DIR__.'/../../../database/seeders';

        if (! File::exists($seedersPath)) {
            $this->error('Seeders directory not found.');

            return;
        }

        $files = File::files($seedersPath);

        foreach ($files as $file) {
            $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $this->line("  - {$className}");
        }
    }
}
