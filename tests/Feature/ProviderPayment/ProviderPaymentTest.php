<?php

namespace Tests\Feature\ProviderPayment;

use Tests\TestCase;
use Fereydooni\Shopping\App\Models\ProviderPayment;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\Models\User;
use Fereydooni\Shopping\App\Enums\ProviderPaymentStatus;
use Fereydooni\Shopping\App\Enums\ProviderPaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProviderPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected Provider $provider;
    protected ProviderInvoice $invoice;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->provider = Provider::factory()->create();
        $this->invoice = ProviderInvoice::factory()->create([
            'provider_id' => $this->provider->id
        ]);
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_create_a_provider_payment()
    {
        $paymentData = [
            'provider_id' => $this->provider->id,
            'invoice_id' => $this->invoice->id,
            'payment_number' => 'PP20240101001',
            'payment_date' => now()->format('Y-m-d'),
            'amount' => 1000.00,
            'currency' => 'USD',
            'payment_method' => ProviderPaymentMethod::BANK_TRANSFER->value,
            'status' => ProviderPaymentStatus::PENDING->value,
            'notes' => 'Test payment',
        ];

        $payment = ProviderPayment::create($paymentData);

        $this->assertInstanceOf(ProviderPayment::class, $payment);
        $this->assertEquals($this->provider->id, $payment->provider_id);
        $this->assertEquals($this->invoice->id, $payment->invoice_id);
        $this->assertEquals('PP20240101001', $payment->payment_number);
        $this->assertEquals(1000.00, $payment->amount);
        $this->assertEquals('USD', $payment->currency);
        $this->assertEquals(ProviderPaymentMethod::BANK_TRANSFER->value, $payment->payment_method->value);
        $this->assertEquals(ProviderPaymentStatus::PENDING->value, $payment->status->value);
    }

    /** @test */
    public function it_can_update_a_provider_payment()
    {
        $payment = ProviderPayment::factory()->create([
            'provider_id' => $this->provider->id,
            'status' => ProviderPaymentStatus::PENDING->value,
        ]);

        $updateData = [
            'amount' => 1500.00,
            'notes' => 'Updated payment notes',
        ];

        $updated = $payment->update($updateData);

        $this->assertTrue($updated);
        $this->assertEquals(1500.00, $payment->fresh()->amount);
        $this->assertEquals('Updated payment notes', $payment->fresh()->notes);
    }

    /** @test */
    public function it_can_delete_a_provider_payment()
    {
        $payment = ProviderPayment::factory()->create([
            'provider_id' => $this->provider->id,
        ]);

        $deleted = $payment->delete();

        $this->assertTrue($deleted);
        $this->assertSoftDeleted($payment);
    }

    /** @test */
    public function it_can_find_payments_by_provider()
    {
        // Create multiple payments for the same provider
        ProviderPayment::factory()->count(3)->create([
            'provider_id' => $this->provider->id,
        ]);

        // Create payments for different provider
        $otherProvider = Provider::factory()->create();
        ProviderPayment::factory()->count(2)->create([
            'provider_id' => $otherProvider->id,
        ]);

        $providerPayments = ProviderPayment::where('provider_id', $this->provider->id)->get();

        $this->assertCount(3, $providerPayments);
        $this->assertTrue($providerPayments->every(fn($payment) => $payment->provider_id === $this->provider->id));
    }

    /** @test */
    public function it_can_find_payments_by_status()
    {
        // Create payments with different statuses
        ProviderPayment::factory()->count(2)->create([
            'provider_id' => $this->provider->id,
            'status' => ProviderPaymentStatus::PENDING->value,
        ]);

        ProviderPayment::factory()->count(3)->create([
            'provider_id' => $this->provider->id,
            'status' => ProviderPaymentStatus::COMPLETED->value,
        ]);

        $pendingPayments = ProviderPayment::where('status', ProviderPaymentStatus::PENDING->value)->get();
        $completedPayments = ProviderPayment::where('status', ProviderPaymentStatus::COMPLETED->value)->get();

        $this->assertCount(2, $pendingPayments);
        $this->assertCount(3, $completedPayments);
    }

    /** @test */
    public function it_can_find_payments_by_payment_method()
    {
        // Create payments with different methods
        ProviderPayment::factory()->count(2)->create([
            'provider_id' => $this->provider->id,
            'payment_method' => ProviderPaymentMethod::BANK_TRANSFER->value,
        ]);

        ProviderPayment::factory()->count(3)->create([
            'provider_id' => $this->provider->id,
            'payment_method' => ProviderPaymentMethod::CHECK->value,
        ]);

        $bankTransferPayments = ProviderPayment::where('payment_method', ProviderPaymentMethod::BANK_TRANSFER->value)->get();
        $checkPayments = ProviderPayment::where('payment_method', ProviderPaymentMethod::CHECK->value)->get();

        $this->assertCount(2, $bankTransferPayments);
        $this->assertCount(3, $checkPayments);
    }

    /** @test */
    public function it_can_find_payments_by_date_range()
    {
        $startDate = now()->subDays(30)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        // Create payments within date range
        ProviderPayment::factory()->count(3)->create([
            'provider_id' => $this->provider->id,
            'payment_date' => now()->subDays(15),
        ]);

        // Create payments outside date range
        ProviderPayment::factory()->count(2)->create([
            'provider_id' => $this->provider->id,
            'payment_date' => now()->subDays(45),
        ]);

        $paymentsInRange = ProviderPayment::whereBetween('payment_date', [$startDate, $endDate])->get();

        $this->assertCount(3, $paymentsInRange);
    }

    /** @test */
    public function it_can_find_payments_by_amount_range()
    {
        $minAmount = 500.00;
        $maxAmount = 2000.00;

        // Create payments within amount range
        ProviderPayment::factory()->count(3)->create([
            'provider_id' => $this->provider->id,
            'amount' => 1000.00,
        ]);

        // Create payments outside amount range
        ProviderPayment::factory()->count(2)->create([
            'provider_id' => $this->provider->id,
            'amount' => 3000.00,
        ]);

        $paymentsInRange = ProviderPayment::whereBetween('amount', [$minAmount, $maxAmount])->get();

        $this->assertCount(3, $paymentsInRange);
    }

    /** @test */
    public function it_can_generate_unique_payment_number()
    {
        $paymentNumber1 = ProviderPayment::generatePaymentNumber();
        $paymentNumber2 = ProviderPayment::generatePaymentNumber();

        $this->assertNotEquals($paymentNumber1, $paymentNumber2);
        $this->assertTrue(ProviderPayment::isPaymentNumberUnique($paymentNumber1));
        $this->assertTrue(ProviderPayment::isPaymentNumberUnique($paymentNumber2));
    }

    /** @test */
    public function it_can_check_payment_status_capabilities()
    {
        $pendingPayment = ProviderPayment::factory()->create([
            'provider_id' => $this->provider->id,
            'status' => ProviderPaymentStatus::PENDING->value,
        ]);

        $completedPayment = ProviderPayment::factory()->create([
            'provider_id' => $this->provider->id,
            'status' => ProviderPaymentStatus::COMPLETED->value,
        ]);

        // Test pending payment capabilities
        $this->assertTrue($pendingPayment->canBeEdited());
        $this->assertTrue($pendingPayment->canBeProcessed());
        $this->assertFalse($pendingPayment->canBeCompleted());
        $this->assertFalse($pendingPayment->canBeReconciled());

        // Test completed payment capabilities
        $this->assertFalse($completedPayment->canBeEdited());
        $this->assertFalse($completedPayment->canBeProcessed());
        $this->assertFalse($completedPayment->canBeCompleted());
        $this->assertTrue($completedPayment->canBeReconciled());
    }

    /** @test */
    public function it_can_get_payment_relationships()
    {
        $payment = ProviderPayment::factory()->create([
            'provider_id' => $this->provider->id,
            'invoice_id' => $this->invoice->id,
            'processed_by' => $this->user->id,
        ]);

        $this->assertInstanceOf(Provider::class, $payment->provider);
        $this->assertInstanceOf(ProviderInvoice::class, $payment->invoice);
        $this->assertInstanceOf(User::class, $payment->processor);
        $this->assertEquals($this->provider->id, $payment->provider->id);
        $this->assertEquals($this->invoice->id, $payment->invoice->id);
        $this->assertEquals($this->user->id, $payment->processor->id);
    }

    /** @test */
    public function it_can_get_formatted_payment_attributes()
    {
        $payment = ProviderPayment::factory()->create([
            'provider_id' => $this->provider->id,
            'amount' => 1234.56,
            'currency' => 'USD',
            'status' => ProviderPaymentStatus::PENDING->value,
            'payment_method' => ProviderPaymentMethod::BANK_TRANSFER->value,
        ]);

        $this->assertEquals('USD 1,234.56', $payment->formatted_amount);
        $this->assertEquals('Pending', $payment->status_label);
        $this->assertEquals('Bank Transfer', $payment->payment_method_label);
        $this->assertEquals('warning', $payment->status_color);
        $this->assertEquals('bank', $payment->payment_method_icon);
    }
}
