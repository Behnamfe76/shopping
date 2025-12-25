<?php

namespace Fereydooni\Shopping\Database\Factories;

use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Fereydooni\Shopping\App\Models\ProviderInvoice>
 */
class ProviderInvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProviderInvoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 10000);
        $taxRate = $this->faker->randomFloat(2, 0, 15);
        $taxAmount = $subtotal * ($taxRate / 100);
        $discountAmount = $this->faker->randomFloat(2, 0, $subtotal * 0.2);
        $shippingAmount = $this->faker->randomFloat(2, 0, 500);
        $totalAmount = $subtotal + $taxAmount - $discountAmount + $shippingAmount;

        $invoiceDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $paymentTerms = $this->faker->randomElement(['net_30', 'net_60', 'net_90', 'immediate']);

        // Calculate due date based on payment terms
        $dueDate = clone $invoiceDate;
        switch ($paymentTerms) {
            case 'net_30':
                $dueDate->add(new \DateInterval('P30D'));
                break;
            case 'net_60':
                $dueDate->add(new \DateInterval('P60D'));
                break;
            case 'net_90':
                $dueDate->add(new \DateInterval('P90D'));
                break;
            case 'immediate':
                $dueDate = clone $invoiceDate;
                break;
            default:
                $dueDate->add(new \DateInterval('P30D'));
        }

        $status = $this->faker->randomElement(['draft', 'sent', 'paid', 'overdue', 'cancelled']);

        // Set appropriate dates based on status
        $sentAt = null;
        $paidAt = null;

        if (in_array($status, ['sent', 'paid', 'overdue'])) {
            $sentAt = $this->faker->dateTimeBetween($invoiceDate, 'now');
        }

        if ($status === 'paid') {
            $paidAt = $this->faker->dateTimeBetween($sentAt ?? $invoiceDate, 'now');
        }

        return [
            'provider_id' => Provider::factory(),
            'invoice_number' => 'INV-'.$this->faker->unique()->numberBetween(10000, 99999),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'total_amount' => $totalAmount,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'shipping_amount' => $shippingAmount,
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP', 'CAD', 'AUD']),
            'status' => $status,
            'payment_terms' => $paymentTerms,
            'payment_method' => $this->faker->randomElement(['bank_transfer', 'credit_card', 'check', 'cash', 'paypal']),
            'reference_number' => $this->faker->optional(0.7)->bothify('REF-####-????'),
            'notes' => $this->faker->optional(0.6)->paragraph(),
            'attachments' => $this->faker->optional(0.3)->randomElements([
                'invoice.pdf',
                'receipt.pdf',
                'contract.pdf',
                'supporting_docs.zip',
            ], $this->faker->numberBetween(1, 3)),
            'sent_at' => $sentAt,
            'paid_at' => $paidAt,
            'overdue_notice_sent' => $status === 'overdue' ? $this->faker->boolean(80) : false,
        ];
    }

    /**
     * Indicate that the invoice is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'sent_at' => null,
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the invoice has been sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => $this->faker->dateTimeBetween($attributes['invoice_date'], 'now'),
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the invoice has been paid.
     */
    public function paid(): static
    {
        $sentAt = $this->faker->dateTimeBetween('-3 months', '-1 month');

        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'sent_at' => $sentAt,
            'paid_at' => $this->faker->dateTimeBetween($sentAt, 'now'),
        ]);
    }

    /**
     * Indicate that the invoice is overdue.
     */
    public function overdue(): static
    {
        $invoiceDate = $this->faker->dateTimeBetween('-6 months', '-2 months');
        $dueDate = (clone $invoiceDate)->add(new \DateInterval('P30D'));
        $sentAt = $this->faker->dateTimeBetween($invoiceDate, $dueDate);

        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'sent_at' => $sentAt,
            'paid_at' => null,
            'overdue_notice_sent' => $this->faker->boolean(80),
        ]);
    }

    /**
     * Indicate that the invoice has been cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'sent_at' => null,
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the invoice is disputed.
     */
    public function disputed(): static
    {
        $sentAt = $this->faker->dateTimeBetween('-2 months', '-1 month');

        return $this->state(fn (array $attributes) => [
            'status' => 'disputed',
            'sent_at' => $sentAt,
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate immediate payment terms.
     */
    public function immediatePayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_terms' => 'immediate',
            'due_date' => $attributes['invoice_date'],
        ]);
    }

    /**
     * Indicate net 30 payment terms.
     */
    public function net30(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_terms' => 'net_30',
            'due_date' => (clone $attributes['invoice_date'])->add(new \DateInterval('P30D')),
        ]);
    }

    /**
     * Indicate net 60 payment terms.
     */
    public function net60(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_terms' => 'net_60',
            'due_date' => (clone $attributes['invoice_date'])->add(new \DateInterval('P60D')),
        ]);
    }

    /**
     * Indicate net 90 payment terms.
     */
    public function net90(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_terms' => 'net_90',
            'due_date' => (clone $attributes['invoice_date'])->add(new \DateInterval('P90D')),
        ]);
    }

    /**
     * Indicate high value invoice.
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'subtotal' => $this->faker->randomFloat(2, 50000, 200000),
        ]);
    }

    /**
     * Indicate low value invoice.
     */
    public function lowValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'subtotal' => $this->faker->randomFloat(2, 100, 1000),
        ]);
    }
}
