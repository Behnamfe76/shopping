<?php

namespace Fereydooni\Shopping\Database\Seeders;

use Fereydooni\Shopping\App\Enums\ProviderPaymentMethod;
use Fereydooni\Shopping\App\Enums\ProviderPaymentStatus;
use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Fereydooni\Shopping\App\Models\ProviderPayment;
use Fereydooni\Shopping\App\Models\User;
use Illuminate\Database\Seeder;

class ProviderPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing providers, invoices, and users
        $providers = Provider::all();
        $invoices = ProviderInvoice::all();
        $users = User::all();

        if ($providers->isEmpty()) {
            $this->command->warn('No providers found. Creating sample providers first.');

            // You might want to run ProviderSeeder here first
            return;
        }

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Creating sample users first.');

            // You might want to run UserSeeder here first
            return;
        }

        $this->command->info('Seeding provider payments...');

        // Create sample payments with different scenarios
        $this->createSamplePayments($providers, $invoices, $users);

        $this->command->info('Provider payments seeded successfully!');
    }

    /**
     * Create sample payments with different scenarios.
     */
    protected function createSamplePayments($providers, $invoices, $users): void
    {
        $paymentScenarios = [
            'pending' => 20,
            'processed' => 15,
            'completed' => 30,
            'failed' => 5,
            'cancelled' => 3,
            'refunded' => 7,
        ];

        $paymentMethods = ProviderPaymentMethod::cases();
        $currencies = ['USD', 'EUR', 'GBP', 'CAD', 'AUD'];

        foreach ($paymentScenarios as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $provider = $providers->random();
                $invoice = $invoices->where('provider_id', $provider->id)->first();
                $user = $users->random();
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $currency = $currencies[array_rand($currencies)];

                $paymentData = $this->generatePaymentData($status, $provider, $invoice, $user, $paymentMethod, $currency);

                ProviderPayment::create($paymentData);
            }
        }

        // Create some payments with specific characteristics
        $this->createSpecializedPayments($providers, $invoices, $users);
    }

    /**
     * Generate payment data based on status and other parameters.
     */
    protected function generatePaymentData(string $status, $provider, $invoice, $user, $paymentMethod, string $currency): array
    {
        $paymentDate = now()->subDays(rand(1, 365));
        $amount = $this->generateRealisticAmount($currency);

        $data = [
            'provider_id' => $provider->id,
            'invoice_id' => $invoice ? $invoice->id : null,
            'payment_number' => $this->generatePaymentNumber(),
            'payment_date' => $paymentDate,
            'amount' => $amount,
            'currency' => $currency,
            'payment_method' => $paymentMethod->value,
            'status' => $status,
            'notes' => $this->generateNotes($status),
        ];

        // Add method-specific data
        $data = array_merge($data, $this->getMethodSpecificData($paymentMethod));

        // Add status-specific data
        $data = array_merge($data, $this->getStatusSpecificData($status, $user, $paymentDate));

        return $data;
    }

    /**
     * Generate realistic payment amounts based on currency.
     */
    protected function generateRealisticAmount(string $currency): float
    {
        $ranges = [
            'USD' => [100, 5000],
            'EUR' => [85, 4250],
            'GBP' => [75, 3750],
            'CAD' => [130, 6500],
            'AUD' => [150, 7500],
        ];

        $range = $ranges[$currency] ?? [100, 5000];

        return round(rand($range[0] * 100, $range[1] * 100) / 100, 2);
    }

    /**
     * Generate notes based on payment status.
     */
    protected function generateNotes(string $status): string
    {
        $notes = [
            'pending' => [
                'Payment pending processing',
                'Awaiting bank confirmation',
                'Payment received, processing...',
                'Pending verification',
                'Awaiting approval',
            ],
            'processed' => [
                'Payment processed successfully',
                'Bank transfer initiated',
                'Check mailed to provider',
                'Credit card payment processed',
                'Wire transfer sent',
            ],
            'completed' => [
                'Payment completed successfully',
                'Provider confirmed receipt',
                'Bank transfer completed',
                'Check cleared',
                'Payment reconciled',
            ],
            'failed' => [
                'Insufficient funds',
                'Invalid account details',
                'Bank rejection',
                'Payment timeout',
                'Technical error',
            ],
            'cancelled' => [
                'Cancelled by provider request',
                'Cancelled due to invoice changes',
                'Cancelled by admin',
                'Duplicate payment cancelled',
                'Cancelled due to error',
            ],
            'refunded' => [
                'Refunded due to overpayment',
                'Refunded per provider request',
                'Refunded due to invoice adjustment',
                'Partial refund processed',
                'Refunded due to error',
            ],
        ];

        $statusNotes = $notes[$status] ?? ['Payment note'];

        return $statusNotes[array_rand($statusNotes)];
    }

    /**
     * Get method-specific data for payment methods.
     */
    protected function getMethodSpecificData($paymentMethod): array
    {
        switch ($paymentMethod->value) {
            case 'bank_transfer':
                return [
                    'reference_number' => 'BANK-'.strtoupper(substr(md5(uniqid()), 0, 8)),
                    'transaction_id' => null,
                ];
            case 'check':
                return [
                    'reference_number' => 'CHK-'.strtoupper(substr(md5(uniqid()), 0, 8)),
                    'transaction_id' => null,
                ];
            case 'credit_card':
                return [
                    'reference_number' => null,
                    'transaction_id' => 'TXN-'.strtoupper(substr(md5(uniqid()), 0, 12)),
                ];
            case 'wire_transfer':
                return [
                    'reference_number' => 'WIRE-'.strtoupper(substr(md5(uniqid()), 0, 8)),
                    'transaction_id' => null,
                ];
            case 'cash':
                return [
                    'reference_number' => null,
                    'transaction_id' => null,
                ];
            default:
                return [
                    'reference_number' => 'REF-'.strtoupper(substr(md5(uniqid()), 0, 8)),
                    'transaction_id' => null,
                ];
        }
    }

    /**
     * Get status-specific data for payment statuses.
     */
    protected function getStatusSpecificData(string $status, $user, $paymentDate): array
    {
        $data = [];

        switch ($status) {
            case 'pending':
                $data['processed_by'] = null;
                $data['processed_at'] = null;
                $data['reconciled_at'] = null;
                $data['reconciliation_notes'] = null;
                break;

            case 'processed':
                $data['processed_by'] = $user->id;
                $data['processed_at'] = $paymentDate->addDays(rand(1, 3));
                $data['reconciled_at'] = null;
                $data['reconciliation_notes'] = null;
                break;

            case 'completed':
                $data['processed_by'] = $user->id;
                $data['processed_at'] = $paymentDate->addDays(rand(1, 3));
                $data['reconciled_at'] = $paymentDate->addDays(rand(4, 7));
                $data['reconciliation_notes'] = $this->generateReconciliationNotes();
                break;

            case 'failed':
                $data['processed_by'] = null;
                $data['processed_at'] = null;
                $data['reconciled_at'] = null;
                $data['reconciliation_notes'] = null;
                break;

            case 'cancelled':
                $data['processed_by'] = null;
                $data['processed_at'] = null;
                $data['reconciled_at'] = null;
                $data['reconciliation_notes'] = null;
                break;

            case 'refunded':
                $data['processed_by'] = $user->id;
                $data['processed_at'] = $paymentDate->addDays(rand(1, 3));
                $data['reconciled_at'] = $paymentDate->addDays(rand(4, 7));
                $data['reconciliation_notes'] = $this->generateReconciliationNotes();
                break;
        }

        return $data;
    }

    /**
     * Generate reconciliation notes.
     */
    protected function generateReconciliationNotes(): string
    {
        $notes = [
            'Payment reconciled with bank statement',
            'Provider confirmed receipt',
            'Invoice marked as paid',
            'Payment verified and reconciled',
            'Bank reconciliation completed',
            'Provider payment confirmed',
            'Payment matched with invoice',
            'Reconciliation successful',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Create specialized payments with specific characteristics.
     */
    protected function createSpecializedPayments($providers, $invoices, $users): void
    {
        // Create some high-value payments
        $this->createHighValuePayments($providers, $invoices, $users);

        // Create some recent payments
        $this->createRecentPayments($providers, $invoices, $users);

        // Create some payments with attachments
        $this->createPaymentsWithAttachments($providers, $invoices, $users);
    }

    /**
     * Create high-value payments.
     */
    protected function createHighValuePayments($providers, $invoices, $users): void
    {
        for ($i = 0; $i < 5; $i++) {
            $provider = $providers->random();
            $invoice = $invoices->where('provider_id', $provider->id)->first();
            $user = $users->random();

            ProviderPayment::create([
                'provider_id' => $provider->id,
                'invoice_id' => $invoice ? $invoice->id : null,
                'payment_number' => $this->generatePaymentNumber(),
                'payment_date' => now()->subDays(rand(1, 30)),
                'amount' => rand(10000, 50000),
                'currency' => 'USD',
                'payment_method' => ProviderPaymentMethod::WIRE_TRANSFER->value,
                'status' => ProviderPaymentStatus::COMPLETED->value,
                'processed_by' => $user->id,
                'processed_at' => now()->subDays(rand(1, 25)),
                'reconciled_at' => now()->subDays(rand(1, 20)),
                'reconciliation_notes' => 'High-value payment reconciled',
                'notes' => 'Large payment for major order',
            ]);
        }
    }

    /**
     * Create recent payments.
     */
    protected function createRecentPayments($providers, $invoices, $users): void
    {
        for ($i = 0; $i < 10; $i++) {
            $provider = $providers->random();
            $invoice = $invoices->where('provider_id', $provider->id)->first();
            $user = $users->random();

            ProviderPayment::create([
                'provider_id' => $provider->id,
                'invoice_id' => $invoice ? $invoice->id : null,
                'payment_number' => $this->generatePaymentNumber(),
                'payment_date' => now()->subDays(rand(0, 7)),
                'amount' => rand(500, 3000),
                'currency' => 'USD',
                'payment_method' => ProviderPaymentMethod::BANK_TRANSFER->value,
                'status' => ProviderPaymentStatus::PENDING->value,
                'notes' => 'Recent payment awaiting processing',
            ]);
        }
    }

    /**
     * Create payments with attachments.
     */
    protected function createPaymentsWithAttachments($providers, $invoices, $users): void
    {
        for ($i = 0; $i < 8; $i++) {
            $provider = $providers->random();
            $invoice = $invoices->where('provider_id', $provider->id)->first();
            $user = $users->random();

            ProviderPayment::create([
                'provider_id' => $provider->id,
                'invoice_id' => $invoice ? $invoice->id : null,
                'payment_number' => $this->generatePaymentNumber(),
                'payment_date' => now()->subDays(rand(1, 14)),
                'amount' => rand(1000, 8000),
                'currency' => 'USD',
                'payment_method' => ProviderPaymentMethod::CHECK->value,
                'status' => ProviderPaymentStatus::COMPLETED->value,
                'processed_by' => $user->id,
                'processed_at' => now()->subDays(rand(1, 10)),
                'reconciled_at' => now()->subDays(rand(1, 5)),
                'attachments' => [
                    'check_image.jpg',
                    'deposit_slip.pdf',
                    'bank_confirmation.pdf',
                ],
                'reconciliation_notes' => 'Payment reconciled with attached documents',
                'notes' => 'Check payment with supporting documents',
            ]);
        }
    }

    /**
     * Generate a unique payment number.
     */
    protected function generatePaymentNumber(): string
    {
        $prefix = 'PP';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return $prefix.$date.$random;
    }
}
