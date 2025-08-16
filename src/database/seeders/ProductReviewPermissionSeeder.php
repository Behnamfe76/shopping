<?php

namespace Fereydooni\Shopping\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductReviewPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // View permissions
            'product-review.view',
            'product-review.view.any',
            'product-review.view.own',
            'product-review.view.approved',
            'product-review.view.pending',
            'product-review.view.rejected',

            // Create permissions
            'product-review.create',
            'product-review.create.any',
            'product-review.create.own',

            // Update permissions
            'product-review.update',
            'product-review.update.any',
            'product-review.update.own',

            // Delete permissions
            'product-review.delete',
            'product-review.delete.any',
            'product-review.delete.own',

            // Approval permissions
            'product-review.approve',
            'product-review.approve.any',
            'product-review.approve.own',
            'product-review.reject',
            'product-review.reject.any',
            'product-review.reject.own',

            // Feature permissions
            'product-review.feature',
            'product-review.feature.any',
            'product-review.feature.own',

            // Verification permissions
            'product-review.verify',
            'product-review.verify.any',
            'product-review.verify.own',

            // Vote permissions
            'product-review.vote',
            'product-review.vote.any',
            'product-review.vote.own',

            // Flag permissions
            'product-review.flag',
            'product-review.flag.any',
            'product-review.flag.own',

            // Moderation permissions
            'product-review.moderate',
            'product-review.moderate.any',
            'product-review.moderate.own',

            // Search permissions
            'product-review.search',
            'product-review.search.any',
            'product-review.search.own',

            // Import/Export permissions
            'product-review.export',
            'product-review.import',

            // Analytics permissions
            'product-review.analytics.view',
            'product-review.analytics.view.any',
            'product-review.analytics.view.own',

            // Report permissions
            'product-review.reports.view',
            'product-review.reports.generate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $this->createProductReviewManagerRole();
        $this->createProductReviewModeratorRole();
        $this->createProductReviewEditorRole();
        $this->createProductReviewViewerRole();
        $this->createProductReviewCreatorRole();
    }

    /**
     * Create product review manager role with full permissions
     */
    private function createProductReviewManagerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-review-manager']);

        $permissions = Permission::where('name', 'like', 'product-review.%')->get();
        $role->syncPermissions($permissions);
    }

    /**
     * Create product review moderator role
     */
    private function createProductReviewModeratorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-review-moderator']);

        $permissions = Permission::whereIn('name', [
            'product-review.view.any',
            'product-review.view.approved',
            'product-review.view.pending',
            'product-review.view.rejected',
            'product-review.update.any',
            'product-review.approve.any',
            'product-review.reject.any',
            'product-review.feature.any',
            'product-review.verify.any',
            'product-review.flag.any',
            'product-review.moderate.any',
            'product-review.search.any',
            'product-review.analytics.view.any',
            'product-review.reports.view',
        ])->get();

        $role->syncPermissions($permissions);
    }

    /**
     * Create product review editor role
     */
    private function createProductReviewEditorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-review-editor']);

        $permissions = Permission::whereIn('name', [
            'product-review.view.any',
            'product-review.view.approved',
            'product-review.view.pending',
            'product-review.update.any',
            'product-review.feature.any',
            'product-review.verify.any',
            'product-review.search.any',
            'product-review.analytics.view.any',
        ])->get();

        $role->syncPermissions($permissions);
    }

    /**
     * Create product review viewer role (read-only)
     */
    private function createProductReviewViewerRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-review-viewer']);

        $permissions = Permission::whereIn('name', [
            'product-review.view.approved',
            'product-review.view.own',
            'product-review.search.own',
            'product-review.analytics.view.own',
        ])->get();

        $role->syncPermissions($permissions);
    }

    /**
     * Create product review creator role
     */
    private function createProductReviewCreatorRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'product-review-creator']);

        $permissions = Permission::whereIn('name', [
            'product-review.view.approved',
            'product-review.view.own',
            'product-review.create.own',
            'product-review.update.own',
            'product-review.delete.own',
            'product-review.vote.own',
            'product-review.flag.own',
            'product-review.search.own',
        ])->get();

        $role->syncPermissions($permissions);
    }
}
