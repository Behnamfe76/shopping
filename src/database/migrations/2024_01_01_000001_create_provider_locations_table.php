<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provider_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->string('location_name', 255);
            $table->string('address', 500);
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('postal_code', 20);
            $table->string('country', 2); // ISO 3166-1 alpha-2 country codes
            $table->string('phone', 20);
            $table->string('email', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('location_type', [
                'headquarters',
                'warehouse',
                'store',
                'office',
                'factory',
                'distribution_center',
                'retail_outlet',
                'service_center',
                'other'
            ])->default('office');
            $table->json('operating_hours')->nullable();
            $table->string('timezone', 50)->default('UTC');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('contact_person', 255)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('provider_id');
            $table->index('location_type');
            $table->index('country');
            $table->index('state');
            $table->index('city');
            $table->index('postal_code');
            $table->index('phone');
            $table->index('email');
            $table->index('website');
            $table->index('is_primary');
            $table->index('is_active');
            $table->index('timezone');

            // Geospatial indexes for coordinates
            $table->index(['latitude', 'longitude']);

            // Full-text search indexes
            $table->fullText(['location_name', 'address', 'city', 'state', 'contact_person', 'notes'], 'provider_locations_search_fulltext');

            // Unique constraints
            $table->unique(['provider_id', 'is_primary'], 'provider_locations_primary_unique');
        });

        // Add additional indexes for better query performance
        Schema::table('provider_locations', function (Blueprint $table) {
            // Composite indexes for common query patterns
            $table->index(['provider_id', 'is_active'], 'provider_locations_provider_active_idx');
            $table->index(['provider_id', 'location_type'], 'provider_locations_provider_type_idx');
            $table->index(['provider_id', 'country'], 'provider_locations_provider_country_idx');
            $table->index(['provider_id', 'state'], 'provider_locations_provider_state_idx');
            $table->index(['provider_id', 'city'], 'provider_locations_provider_city_idx');
            $table->index(['location_type', 'is_active'], 'provider_locations_type_active_idx');
            $table->index(['country', 'is_active'], 'provider_locations_country_active_idx');
            $table->index(['state', 'is_active'], 'provider_locations_state_active_idx');
            $table->index(['city', 'is_active'], 'provider_locations_city_active_idx');

            // Indexes for geospatial queries
            $table->index(['latitude', 'longitude', 'is_active'], 'provider_locations_coords_active_idx');

            // Indexes for search operations
            $table->index(['location_name', 'is_active'], 'provider_locations_name_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_locations');
    }
};
