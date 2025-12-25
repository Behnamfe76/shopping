<?php

namespace Tests\Feature\CustomerPreference;

use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Models\CustomerPreference;
use Fereydooni\Shopping\app\Services\CustomerPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerPreferenceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected Customer $customer;

    protected CustomerPreference $preference;

    protected CustomerPreferenceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::factory()->create();
        $this->preference = CustomerPreference::factory()->create([
            'customer_id' => $this->customer->id,
        ]);
        $this->service = app(CustomerPreferenceService::class);
    }

    /** @test */
    public function it_can_list_customer_preferences()
    {
        $response = $this->getJson('/api/v1/customer-preferences');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'customer_id',
                        'preference_key',
                        'preference_value',
                        'preference_type',
                        'is_active',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_create_customer_preference()
    {
        $data = [
            'customer_id' => $this->customer->id,
            'preference_key' => 'test.preference',
            'preference_value' => 'test_value',
            'preference_type' => 'string',
            'is_active' => true,
            'description' => 'Test preference',
        ];

        $response = $this->postJson('/api/v1/customer-preferences', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'customer_id',
                    'preference_key',
                    'preference_value',
                    'preference_type',
                    'is_active',
                ],
            ]);

        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'test.preference',
            'preference_value' => 'test_value',
        ]);
    }

    /** @test */
    public function it_can_show_customer_preference()
    {
        $response = $this->getJson("/api/v1/customer-preferences/{$this->preference->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'customer_id',
                    'preference_key',
                    'preference_value',
                    'preference_type',
                    'is_active',
                ],
            ]);
    }

    /** @test */
    public function it_can_update_customer_preference()
    {
        $data = [
            'preference_value' => 'updated_value',
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/v1/customer-preferences/{$this->preference->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'preference_value',
                    'description',
                ],
            ]);

        $this->assertDatabaseHas('customer_preferences', [
            'id' => $this->preference->id,
            'preference_value' => 'updated_value',
        ]);
    }

    /** @test */
    public function it_can_delete_customer_preference()
    {
        $response = $this->deleteJson("/api/v1/customer-preferences/{$this->preference->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Customer preference deleted successfully',
            ]);

        $this->assertDatabaseMissing('customer_preferences', [
            'id' => $this->preference->id,
        ]);
    }

    /** @test */
    public function it_can_activate_customer_preference()
    {
        $this->preference->update(['is_active' => false]);

        $response = $this->postJson("/api/v1/customer-preferences/{$this->preference->id}/activate");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Customer preference activated successfully',
            ]);

        $this->assertDatabaseHas('customer_preferences', [
            'id' => $this->preference->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_can_deactivate_customer_preference()
    {
        $this->preference->update(['is_active' => true]);

        $response = $this->postJson("/api/v1/customer-preferences/{$this->preference->id}/deactivate");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Customer preference deactivated successfully',
            ]);

        $this->assertDatabaseHas('customer_preferences', [
            'id' => $this->preference->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function it_can_get_customer_preferences()
    {
        $response = $this->getJson("/api/v1/customers/{$this->customer->id}/preferences");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'key',
                        'value',
                        'type',
                        'description',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_set_customer_preference()
    {
        $data = [
            'key' => 'ui.theme',
            'value' => 'dark',
            'type' => 'string',
            'description' => 'UI theme preference',
        ];

        $response = $this->postJson("/api/v1/customers/{$this->customer->id}/preferences", $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Customer preference set successfully',
            ]);

        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
            'preference_value' => 'dark',
        ]);
    }

    /** @test */
    public function it_can_get_specific_customer_preference()
    {
        $preference = CustomerPreference::factory()->create([
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
            'preference_value' => 'dark',
        ]);

        $response = $this->getJson("/api/v1/customers/{$this->customer->id}/preferences/ui.theme");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'key',
                    'value',
                ],
            ]);
    }

    /** @test */
    public function it_can_update_specific_customer_preference()
    {
        $preference = CustomerPreference::factory()->create([
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
            'preference_value' => 'light',
        ]);

        $data = [
            'value' => 'dark',
            'description' => 'Updated theme preference',
        ];

        $response = $this->putJson("/api/v1/customers/{$this->customer->id}/preferences/ui.theme", $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Customer preference updated successfully',
            ]);

        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
            'preference_value' => 'dark',
        ]);
    }

    /** @test */
    public function it_can_remove_specific_customer_preference()
    {
        $preference = CustomerPreference::factory()->create([
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
        ]);

        $response = $this->deleteJson("/api/v1/customers/{$this->customer->id}/preferences/ui.theme");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Customer preference removed successfully',
            ]);

        $this->assertDatabaseMissing('customer_preferences', [
            'id' => $preference->id,
        ]);
    }

    /** @test */
    public function it_can_reset_customer_preferences()
    {
        CustomerPreference::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->postJson("/api/v1/customers/{$this->customer->id}/preferences/reset");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Customer preferences reset successfully',
            ]);

        $this->assertDatabaseMissing('customer_preferences', [
            'customer_id' => $this->customer->id,
        ]);
    }

    /** @test */
    public function it_can_import_customer_preferences()
    {
        $preferences = [
            [
                'key' => 'ui.theme',
                'value' => 'dark',
                'type' => 'string',
                'description' => 'UI theme',
            ],
            [
                'key' => 'notifications.email',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Email notifications',
            ],
        ];

        $response = $this->postJson("/api/v1/customers/{$this->customer->id}/preferences/import", [
            'preferences' => $preferences,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Customer preferences imported successfully',
            ]);

        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
        ]);

        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'notifications.email',
        ]);
    }

    /** @test */
    public function it_can_export_customer_preferences()
    {
        CustomerPreference::factory()->count(2)->create([
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->getJson("/api/v1/customers/{$this->customer->id}/preferences/export");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'value',
                        'type',
                        'description',
                        'is_active',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_sync_customer_preferences()
    {
        $existingPreference = CustomerPreference::factory()->create([
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
            'preference_value' => 'light',
        ]);

        $preferences = [
            [
                'key' => 'ui.theme',
                'value' => 'dark',
                'type' => 'string',
            ],
            [
                'key' => 'notifications.email',
                'value' => 'true',
                'type' => 'boolean',
            ],
        ];

        $response = $this->postJson("/api/v1/customers/{$this->customer->id}/preferences/sync", [
            'preferences' => $preferences,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Customer preferences synced successfully',
            ]);

        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
            'preference_value' => 'dark',
        ]);

        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'notifications.email',
        ]);
    }

    /** @test */
    public function it_can_search_customer_preferences()
    {
        CustomerPreference::factory()->create([
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
            'preference_value' => 'dark',
        ]);

        $response = $this->getJson('/api/v1/customer-preferences/search?query=theme');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'preference_key',
                        'preference_value',
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_get_preference_statistics()
    {
        CustomerPreference::factory()->count(5)->create([
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->getJson('/api/v1/customer-preferences/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_preferences',
                    'active_preferences',
                    'inactive_preferences',
                ],
            ]);
    }

    /** @test */
    public function it_can_get_customer_preference_summary()
    {
        CustomerPreference::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->getJson("/api/v1/customers/{$this->customer->id}/preferences/summary");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'customer_id',
                    'total_preferences',
                    'active_preferences',
                    'inactive_preferences',
                    'preferences_by_category',
                    'preferences_by_type',
                ],
            ]);
    }

    /** @test */
    public function it_can_initialize_customer_preferences()
    {
        $response = $this->postJson("/api/v1/customers/{$this->customer->id}/preferences/initialize");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Customer preferences initialized successfully',
            ]);

        // Check that default preferences were created
        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
        ]);
    }

    /** @test */
    public function it_can_get_preference_templates()
    {
        $response = $this->getJson('/api/v1/customer-preferences/templates');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'ui',
                    'notifications',
                    'privacy',
                    'shopping',
                ],
            ]);
    }

    /** @test */
    public function it_can_apply_preference_template()
    {
        $data = [
            'template_name' => 'ui',
        ];

        $response = $this->postJson("/api/v1/customers/{$this->customer->id}/preferences/apply-template", $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Preference template applied successfully',
            ]);

        // Check that UI template preferences were created
        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_preference()
    {
        $response = $this->postJson('/api/v1/customer-preferences', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'customer_id',
                'preference_key',
                'preference_value',
                'preference_type',
            ]);
    }

    /** @test */
    public function it_validates_preference_type_values()
    {
        $data = [
            'customer_id' => $this->customer->id,
            'preference_key' => 'test.preference',
            'preference_value' => 'test_value',
            'preference_type' => 'invalid_type',
        ];

        $response = $this->postJson('/api/v1/customer-preferences', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['preference_type']);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_preference()
    {
        $response = $this->getJson('/api/v1/customer-preferences/999999');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_customer_preference()
    {
        $response = $this->getJson("/api/v1/customers/{$this->customer->id}/preferences/nonexistent.key");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Customer preference not found',
            ]);
    }
}
