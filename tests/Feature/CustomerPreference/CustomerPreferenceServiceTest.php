<?php

namespace Tests\Feature\CustomerPreference;

use Tests\TestCase;
use Fereydooni\Shopping\app\Models\CustomerPreference;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Services\CustomerPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerPreferenceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected CustomerPreferenceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = Customer::factory()->create();
        $this->service = app(CustomerPreferenceService::class);
    }

    /** @test */
    public function it_can_set_customer_preference()
    {
        $result = $this->service->setCustomerPreference(
            $this->customer->id,
            'ui.theme',
            'dark',
            'string'
        );

        $this->assertTrue($result);
        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
            'preference_value' => 'dark',
            'preference_type' => 'string',
        ]);
    }

    /** @test */
    public function it_can_get_customer_preference()
    {
        $this->service->setCustomerPreference(
            $this->customer->id,
            'ui.theme',
            'dark',
            'string'
        );

        $value = $this->service->getCustomerPreference($this->customer->id, 'ui.theme');

        $this->assertEquals('dark', $value);
    }

    /** @test */
    public function it_returns_default_when_preference_not_found()
    {
        $value = $this->service->getCustomerPreference(
            $this->customer->id,
            'nonexistent.key',
            'default_value'
        );

        $this->assertEquals('default_value', $value);
    }

    /** @test */
    public function it_can_remove_customer_preference()
    {
        $this->service->setCustomerPreference(
            $this->customer->id,
            'ui.theme',
            'dark',
            'string'
        );

        $result = $this->service->removeCustomerPreference($this->customer->id, 'ui.theme');

        $this->assertTrue($result);
        $this->assertDatabaseMissing('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
        ]);
    }

    /** @test */
    public function it_can_get_all_customer_preferences()
    {
        $this->service->setCustomerPreference($this->customer->id, 'ui.theme', 'dark', 'string');
        $this->service->setCustomerPreference($this->customer->id, 'notifications.email', 'true', 'boolean');

        $preferences = $this->service->getAllCustomerPreferences($this->customer->id);

        $this->assertCount(2, $preferences);
        $this->assertEquals('dark', $preferences['ui.theme']);
        $this->assertTrue($preferences['notifications.email']);
    }

    /** @test */
    public function it_can_initialize_customer_preferences()
    {
        $result = $this->service->initializeCustomerPreferences($this->customer->id);

        $this->assertTrue($result);
        
        // Check that default preferences were created
        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
        ]);
    }

    /** @test */
    public function it_can_activate_customer_preference()
    {
        $preference = CustomerPreference::factory()->create([
            'customer_id' => $this->customer->id,
            'is_active' => false,
        ]);

        $result = $this->service->activateCustomerPreference($preference);

        $this->assertTrue($result);
        $this->assertDatabaseHas('customer_preferences', [
            'id' => $preference->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_can_deactivate_customer_preference()
    {
        $preference = CustomerPreference::factory()->create([
            'customer_id' => $this->customer->id,
            'is_active' => true,
        ]);

        $result = $this->service->deactivateCustomerPreference($preference);

        $this->assertTrue($result);
        $this->assertDatabaseHas('customer_preferences', [
            'id' => $preference->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function it_can_import_customer_preferences()
    {
        $preferences = [
            'ui.theme' => ['value' => 'dark', 'type' => 'string'],
            'notifications.email' => ['value' => 'true', 'type' => 'boolean'],
        ];

        $result = $this->service->importCustomerPreferences($this->customer->id, $preferences);

        $this->assertTrue($result);
        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
            'preference_value' => 'dark',
        ]);
    }

    /** @test */
    public function it_can_export_customer_preferences()
    {
        $this->service->setCustomerPreference($this->customer->id, 'ui.theme', 'dark', 'string');
        $this->service->setCustomerPreference($this->customer->id, 'notifications.email', 'true', 'boolean');

        $exported = $this->service->exportCustomerPreferences($this->customer->id);

        $this->assertCount(2, $exported);
        $this->assertArrayHasKey('ui.theme', $exported);
        $this->assertArrayHasKey('notifications.email', $exported);
    }

    /** @test */
    public function it_can_get_customer_preference_stats()
    {
        $this->service->setCustomerPreference($this->customer->id, 'ui.theme', 'dark', 'string');
        $this->service->setCustomerPreference($this->customer->id, 'notifications.email', 'true', 'boolean');

        $stats = $this->service->getCustomerPreferenceStats($this->customer->id);

        $this->assertEquals(2, $stats['total_preferences']);
        $this->assertEquals(2, $stats['active_preferences']);
        $this->assertEquals(0, $stats['inactive_preferences']);
    }

    /** @test */
    public function it_can_get_preference_templates()
    {
        $templates = $this->service->getPreferenceTemplates();

        $this->assertArrayHasKey('ui', $templates);
        $this->assertArrayHasKey('notifications', $templates);
        $this->assertArrayHasKey('privacy', $templates);
        $this->assertArrayHasKey('shopping', $templates);
    }

    /** @test */
    public function it_can_apply_preference_template()
    {
        $result = $this->service->applyPreferenceTemplate($this->customer->id, 'ui');

        $this->assertTrue($result);
        $this->assertDatabaseHas('customer_preferences', [
            'customer_id' => $this->customer->id,
            'preference_key' => 'ui.theme',
        ]);
    }

    /** @test */
    public function it_can_reset_customer_preferences()
    {
        $this->service->setCustomerPreference($this->customer->id, 'ui.theme', 'dark', 'string');
        $this->service->setCustomerPreference($this->customer->id, 'notifications.email', 'true', 'boolean');

        $result = $this->service->resetCustomerPreferences($this->customer->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('customer_preferences', [
            'customer_id' => $this->customer->id,
        ]);
    }

    /** @test */
    public function it_can_validate_preference_data()
    {
        $validData = [
            'customer_id' => $this->customer->id,
            'preference_key' => 'test.key',
            'preference_value' => 'test_value',
            'preference_type' => 'string',
        ];

        $result = $this->service->validatePreference($validData);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_get_preference_with_default()
    {
        $value = $this->service->getCustomerPreferenceWithDefault(
            $this->customer->id,
            'ui.theme',
            'light'
        );

        $this->assertEquals('light', $value);
    }

    /** @test */
    public function it_can_get_preferences_by_category()
    {
        $this->service->setCustomerPreference($this->customer->id, 'ui.theme', 'dark', 'string');
        $this->service->setCustomerPreference($this->customer->id, 'ui.language', 'en', 'string');
        $this->service->setCustomerPreference($this->customer->id, 'notifications.email', 'true', 'boolean');

        $uiPreferences = $this->service->getCustomerPreferencesByCategory($this->customer->id, 'ui');

        $this->assertCount(2, $uiPreferences);
        $this->assertArrayHasKey('ui.theme', $uiPreferences);
        $this->assertArrayHasKey('ui.language', $uiPreferences);
    }
}

