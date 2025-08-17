<?php

namespace Fereydooni\Shopping\app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class InstallRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopping:install
                            {--route=* : The type of routes to install (api, web, or both)}
                            {--force : Overwrite existing route files}
                            {--prefix= : Custom route prefix for the package}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install shopping package routes (API and/or Web)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routeTypes = $this->option('route');
        $force = $this->option('force');
        $prefix = $this->option('prefix');

        // If no route types specified, ask user
        if (empty($routeTypes)) {
            $routeTypes = $this->choice(
                'Which routes would you like to install?',
                ['api', 'web', 'both'],
                'both'
            );

            if ($routeTypes === 'both') {
                $routeTypes = ['api', 'web'];
            } else {
                $routeTypes = [$routeTypes];
            }
        }

        $this->info('Installing shopping package routes...');

        foreach ($routeTypes as $routeType) {
            $this->installRouteType($routeType, $force, $prefix);
        }

        $this->info('Routes installation completed successfully!');

        if (in_array('api', $routeTypes)) {
            $this->info('API routes are available at: /api/v1/shopping/*');
        }

        if (in_array('web', $routeTypes)) {
            $this->info('Web routes are available at: /shopping/*');
        }
    }

    /**
     * Install routes for a specific type (api or web).
     */
    protected function installRouteType(string $routeType, bool $force, ?string $prefix): void
    {
        $this->info("Installing {$routeType} routes...");

        $routesPath = base_path("routes/{$routeType}.php");
        $packageRoutesPath = __DIR__ . "/../../../routes/{$routeType}.php";

        // Check if package routes file exists
        if (!File::exists($packageRoutesPath)) {
            $this->error("Package {$routeType} routes file not found!");
            return;
        }

        // Read package routes content
        $packageRoutesContent = File::get($packageRoutesPath);

        // If custom prefix is provided, modify the routes
        if ($prefix) {
            $packageRoutesContent = $this->applyCustomPrefix($packageRoutesContent, $prefix, $routeType);
        }

        // Check if routes are already installed
        if (File::exists($routesPath)) {
            $existingContent = File::get($routesPath);

            if (str_contains($existingContent, 'shopping')) {
                if (!$force) {
                    $this->warn("Shopping routes already exist in {$routeType}.php");
                    $this->warn("Use --force to overwrite existing routes.");
                    return;
                } else {
                    $this->warn("Overwriting existing shopping routes in {$routeType}.php");
                    // Remove existing shopping routes
                    $existingContent = $this->removeExistingShoppingRoutes($existingContent);
                }
            }
        }

        // Add shopping routes to the main routes file
        $this->addRoutesToFile($routesPath, $packageRoutesContent, $routeType);

        $this->info("✓ {$routeType} routes installed successfully!");
    }

    /**
     * Apply custom prefix to routes.
     */
    protected function applyCustomPrefix(string $content, string $prefix, string $routeType): string
    {
        if ($routeType === 'api') {
            // Replace the API prefix
            $content = preg_replace(
                '/Route::prefix\(\'api\/v1\'\)/',
                "Route::prefix('api/v1/{$prefix}')",
                $content
            );
        } else {
            // Replace the web prefix
            $content = preg_replace(
                '/Route::prefix\(\'shopping\'\)/',
                "Route::prefix('{$prefix}')",
                $content
            );
        }

        return $content;
    }

    /**
     * Remove existing shopping routes from content.
     */
    protected function removeExistingShoppingRoutes(string $content): string
    {
        // Remove lines containing shopping routes
        $lines = explode("\n", $content);
        $filteredLines = [];
        $skipNextLines = false;
        $braceCount = 0;

        foreach ($lines as $line) {
            // Check if this line contains shopping route registration
            if (str_contains($line, 'shopping') && str_contains($line, 'Route::')) {
                $skipNextLines = true;
                $braceCount = 0;
                continue;
            }

            if ($skipNextLines) {
                // Count braces to know when to stop skipping
                $braceCount += substr_count($line, '{') - substr_count($line, '}');

                if ($braceCount <= 0) {
                    $skipNextLines = false;
                }
                continue;
            }

            $filteredLines[] = $line;
        }

        return implode("\n", $filteredLines);
    }

    /**
     * Add routes to the main routes file.
     */
    protected function addRoutesToFile(string $routesPath, string $packageRoutesContent, string $routeType): void
    {
        if (!File::exists($routesPath)) {
            // Create the routes file if it doesn't exist
            $this->createRoutesFile($routesPath, $routeType);
        }

        $content = File::get($routesPath);

        // Add shopping routes at the end of the file
        $content .= "\n\n// Shopping Package Routes\n";
        $content .= $packageRoutesContent;

        File::put($routesPath, $content);
    }

    /**
     * Create a new routes file if it doesn't exist.
     */
    protected function createRoutesFile(string $routesPath, string $routeType): void
    {
        $routesDir = dirname($routesPath);

        if (!File::exists($routesDir)) {
            File::makeDirectory($routesDir, 0755, true);
        }

        $stub = $this->getRoutesStub($routeType);
        File::put($routesPath, $stub);

        $this->info("Created new {$routeType}.php file");
    }

    /**
     * Get the routes stub content.
     */
    protected function getRoutesStub(string $routeType): string
    {
        if ($routeType === 'api') {
            return "<?php\n\nuse Illuminate\Http\Request;\nuse Illuminate\Support\Facades\Route;\n\n/*\n|--------------------------------------------------------------------------\n| API Routes\n|--------------------------------------------------------------------------\n|\n| Here is where you can register API routes for your application. These\n| routes are loaded by the RouteServiceProvider and all of them will\n| be assigned to the \"api\" middleware group. Make something great!\n|\n*/\n\nRoute::middleware('auth:sanctum')->get('/user', function (Request \$request) {\n    return \$request->user();\n});\n";
        }

        return "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n/*\n|--------------------------------------------------------------------------\n| Web Routes\n|--------------------------------------------------------------------------\n|\n| Here is where you can register web routes for your application. These\n| routes are loaded by the RouteServiceProvider and all of them will\n| be assigned to the \"web\" middleware group. Make something great!\n|\n*/\n\nRoute::get('/', function () {\n    return view('welcome');\n});\n";
    }
}
