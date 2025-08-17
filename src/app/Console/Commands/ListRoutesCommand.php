<?php

namespace Fereydooni\Shopping\app\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class ListRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopping:routes
                            {--type=* : Filter routes by type (api, web)}
                            {--group=* : Filter routes by group (products, orders, etc.)}
                            {--method=* : Filter routes by HTTP method (GET, POST, etc.)}
                            {--format=table : Output format (table, json, csv)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available shopping package routes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $typeFilter = $this->option('type');
        $groupFilter = $this->option('group');
        $methodFilter = $this->option('method');
        $format = $this->option('format');

        $this->info('Shopping Package Routes');
        $this->info('======================');

        $routes = $this->getShoppingRoutes($typeFilter, $groupFilter, $methodFilter);

        if (empty($routes)) {
            $this->warn('No shopping routes found.');
            return;
        }

        switch ($format) {
            case 'json':
                $this->outputJson($routes);
                break;
            case 'csv':
                $this->outputCsv($routes);
                break;
            default:
                $this->outputTable($routes);
        }
    }

    /**
     * Get shopping routes based on filters.
     */
    protected function getShoppingRoutes(array $typeFilter = [], array $groupFilter = [], array $methodFilter = []): array
    {
        $routes = [];
        $allRoutes = Route::getRoutes();

        foreach ($allRoutes as $route) {
            $uri = $route->uri();
            $methods = $route->methods();
            $name = $route->getName() ?? '';

            // Check if this is a shopping route
            if ($this->isShoppingRoute($uri, $name)) {
                $routeType = $this->getRouteType($uri);
                $routeGroup = $this->getRouteGroup($uri);
                $method = implode('|', array_diff($methods, ['HEAD']));

                // Apply filters
                if (!empty($typeFilter) && !in_array($routeType, $typeFilter)) {
                    continue;
                }

                if (!empty($groupFilter) && !in_array($routeGroup, $groupFilter)) {
                    continue;
                }

                if (!empty($methodFilter) && !in_array($method, $methodFilter)) {
                    continue;
                }

                $routes[] = [
                    'method' => $method,
                    'uri' => $uri,
                    'name' => $name,
                    'type' => $routeType,
                    'group' => $routeGroup,
                ];
            }
        }

        return $routes;
    }

    /**
     * Check if a route is a shopping route.
     */
    protected function isShoppingRoute(string $uri, string $name): bool
    {
        return str_contains($uri, 'shopping') ||
               str_contains($name, 'shopping') ||
               str_contains($uri, 'api/v1') && $this->isShoppingApiRoute($uri);
    }

    /**
     * Check if an API route is a shopping route.
     */
    protected function isShoppingApiRoute(string $uri): bool
    {
        $shoppingApiPatterns = [
            'addresses', 'categories', 'brands', 'orders', 'order-items',
            'product-attributes', 'product-discounts', 'products',
            'product-meta', 'product-reviews', 'product-tags',
            'product-variants', 'shipments', 'transactions',
            'user-subscriptions'
        ];

        foreach ($shoppingApiPatterns as $pattern) {
            if (str_contains($uri, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get route type (api or web).
     */
    protected function getRouteType(string $uri): string
    {
        return str_contains($uri, 'api/') ? 'api' : 'web';
    }

    /**
     * Get route group based on URI.
     */
    protected function getRouteGroup(string $uri): string
    {
        $segments = explode('/', $uri);

        // Find the shopping-related segment
        foreach ($segments as $segment) {
            if (in_array($segment, [
                'addresses', 'categories', 'brands', 'orders', 'order-items',
                'product-attributes', 'product-discounts', 'products',
                'product-meta', 'product-reviews', 'product-tags',
                'product-variants', 'shipments', 'transactions',
                'user-subscriptions'
            ])) {
                return $segment;
            }
        }

        return 'general';
    }

    /**
     * Output routes as table.
     */
    protected function outputTable(array $routes): void
    {
        $headers = ['Method', 'URI', 'Name', 'Type', 'Group'];
        $rows = [];

        foreach ($routes as $route) {
            $rows[] = [
                $route['method'],
                $route['uri'],
                $route['name'] ?: '-',
                $route['type'],
                $route['group'],
            ];
        }

        $this->table($headers, $rows);
    }

    /**
     * Output routes as JSON.
     */
    protected function outputJson(array $routes): void
    {
        $this->line(json_encode($routes, JSON_PRETTY_PRINT));
    }

    /**
     * Output routes as CSV.
     */
    protected function outputCsv(array $routes): void
    {
        $headers = ['Method', 'URI', 'Name', 'Type', 'Group'];

        // Output headers
        $this->line(implode(',', $headers));

        // Output data
        foreach ($routes as $route) {
            $row = [
                $route['method'],
                $route['uri'],
                $route['name'] ?: '',
                $route['type'],
                $route['group'],
            ];
            $this->line(implode(',', $row));
        }
    }
}
