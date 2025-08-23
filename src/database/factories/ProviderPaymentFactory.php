<?php

namespace Fereydooni\Shopping\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Fereydooni\Shopping\App\Models\ProviderPayment;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\Models\User;
use Fereydooni\Shopping\App\Enums\ProviderPaymentStatus;
use Fereydooni\Shopping\App\Enums\ProviderPaymentMethod;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Fereydooni\Shopping\App\Models\ProviderPayment>
 */
class ProviderPaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = ProviderPayment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $paymentDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $status = $this->faker->randomElement(ProviderPaymentStatus::cases());

        return [
            'provider_id' => Provider::factory(),
            'invoice_id' => ProviderInvoice::factory(),
            'payment_number' => $this->generatePaymentNumber(),
            'payment_date' => $paymentDate,
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP', 'CAD', 'AUD']),
            'payment_method' => $this->faker->randomElement(ProviderPaymentMethod::cases()),
            'reference_number' => $this->faker->optional(0.7)->bothify('REF-####-????'),
            'transaction_id' => $this->faker->optional(0.8)->uuid(),
            'status' => $status,
            'notes' => $this->faker->optional(0.6)->sentence(),
            'attachments' => $this->faker->optional(0.3)->randomElements([
                'receipt.pdf',
                'invoice.pdf',
                'proof_of_payment.pdf',
                'bank_statement.pdf'
            ], $this->faker->numberBetween(1, 3)),
            'processed_by' => $status->value !== 'pending' ? User::factory() : null,
            'processed_at' => $status->value !== 'pending' ? $this->faker->dateTimeBetween($paymentDate, 'now') : null,
            'reconciled_at' => $status->value === 'completed' ? $this->faker->optional(0.8)->dateTimeBetween($paymentDate, 'now') : null,
            'reconciliation_notes' => $status->value === 'completed' ? $this->faker->optional(0.6)->sentence() : null,
        ];
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProviderPaymentStatus::PENDING,
            'processed_by' => null,
            'processed_at' => null,
            'reconciled_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is processed.
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProviderPaymentStatus::PROCESSED,
            'processed_by' => User::factory(),
            'processed_at' => $this->faker->dateTimeBetween($attributes['payment_date'], 'now'),
            'reconciled_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProviderPaymentStatus::COMPLETED,
            'processed_by' => User::factory(),
            'processed_at' => $this->faker->dateTimeBetween($attributes['payment_date'], 'now'),
            'reconciled_at' => $this->faker->optional(0.8)->dateTimeBetween($attributes['payment_date'], 'now'),
        ]);
    }

    /**
     * Indicate that the payment is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProviderPaymentStatus::FAILED,
            'processed_by' => null,
            'processed_at' => null,
            'reconciled_at' => null,
            'notes' => $this->faker->sentence() . ' - Payment processing failed.',
        ]);
    }

    /**
     * Indicate that the payment is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProviderPaymentStatus::CANCELLED,
            'processed_by' => null,
            'processed_at' => null,
            'reconciled_at' => null,
            'notes' => $this->faker->sentence() . ' - Payment was cancelled.',
        ]);
    }

    /**
     * Indicate that the payment is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProviderPaymentStatus::REFUNDED,
            'processed_by' => User::factory(),
            'processed_at' => $this->faker->dateTimeBetween($attributes['payment_date'], 'now'),
            'reconciled_at' => $this->faker->dateTimeBetween($attributes['payment_date'], 'now'),
            'notes' => $this->faker->sentence() . ' - Payment was refunded.',
        ]);
    }

    /**
     * Indicate that the payment is reconciled.
     */
    public function reconciled(): static
    {
        return $this->state(fn (array $attributes) => [
            'reconciled_at' => $this->faker->dateTimeBetween($attributes['payment_date'], 'now'),
            'reconciliation_notes' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the payment is unreconciled.
     */
    public function unreconciled(): static
    {
        return $this->state(fn (array $attributes) => [
            'reconciled_at' => null,
            'reconciliation_notes' => null,
        ]);
    }

    /**
     * Indicate that the payment uses bank transfer method.
     */
    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => ProviderPaymentMethod::BANK_TRANSFER,
            'reference_number' => $this->faker->bothify('BANK-####-????'),
        ]);
    }

    /**
     * Indicate that the payment uses check method.
     */
    public function check(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => ProviderPaymentMethod::CHECK,
            'reference_number' => $this->faker->bothify('CHK-####-????'),
        ]);
    }

    /**
     * Indicate that the payment uses credit card method.
     */
    public function creditCard(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => ProviderPaymentMethod::CREDIT_CARD,
            'transaction_id' => $this->faker->uuid(),
        ]);
    }

    /**
     * Indicate that the payment uses wire transfer method.
     */
    public function wireTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => ProviderPaymentMethod::WIRE_TRANSFER,
            'reference_number' => $this->faker->bothify('WIRE-####-????'),
        ]);
    }

    /**
     * Indicate that the payment uses cash method.
     */
    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => ProviderPaymentMethod::CASH,
            'reference_number' => null,
            'transaction_id' => null,
        ]);
    }

    /**
     * Indicate that the payment has a specific amount range.
     */
    public function amountRange(float $min, float $max): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $this->faker->randomFloat(2, $min, $max),
        ]);
    }

    /**
     * Indicate that the payment is for a specific currency.
     */
    public function currency(string $currency): static
    {
        return $this->state(fn (array $attributes) => [
            'currency' => $currency,
        ]);
    }

    /**
     * Indicate that the payment is for a specific provider.
     */
    public function forProvider(Provider $provider): static
    {
        return $this->state(fn (array $attributes) => [
            'provider_id' => $provider->id,
        ]);
    }

    /**
     * Indicate that the payment is for a specific invoice.
     */
    public function forInvoice(ProviderInvoice $invoice): static
    {
        return $this->state(fn (array $attributes) => [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id,
        ]);
    }

    /**
     * Indicate that the payment was processed by a specific user.
     */
    public function processedBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'processed_by' => $user->id,
            'processed_at' => $this->faker->dateTimeBetween($attributes['payment_date'], 'now'),
        ]);
    }

    /**
     * Generate a unique payment number.
     */
    protected function generatePaymentNumber(): string
    {
        $prefix = 'PP';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return $prefix . $date . $random;
    }
}
