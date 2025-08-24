<?php

namespace Fereydooni\Shopping\Database\Seeders;

use Fereydooni\Shopping\App\Models\Provider;
use Fereydooni\Shopping\App\Models\ProviderInvoice;
use Illuminate\Database\Seeder;

class ProviderInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = Provider::all();

        if ($providers->isEmpty()) {
            $this->command->warn('No providers found. Creating sample providers first...');
            $providers = Provider::factory(10)->create();
        }

        $this->command->info('Seeding provider invoices...');

        // Create invoices with different statuses and scenarios
        $this->createDraftInvoices($providers);
        $this->createSentInvoices($providers);
        $this->createPaidInvoices($providers);
        $this->createOverdueInvoices($providers);
        $this->createCancelledInvoices($providers);
        $this->createDisputedInvoices($providers);

        $this->command->info('Provider invoices seeded successfully!');
    }

    /**
     * Create draft invoices
     */
    protected function createDraftInvoices($providers): void
    {
        $this->command->info('Creating draft invoices...');

        foreach ($providers->take(5) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(1, 3))
                ->draft()
                ->for($provider)
                ->create();
        }
    }

    /**
     * Create sent invoices
     */
    protected function createSentInvoices($providers): void
    {
        $this->command->info('Creating sent invoices...');

        foreach ($providers->take(8) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(2, 5))
                ->sent()
                ->for($provider)
                ->create();
        }
    }

    /**
     * Create paid invoices
     */
    protected function createPaidInvoices($providers): void
    {
        $this->command->info('Creating paid invoices...');

        foreach ($providers->take(10) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(3, 8))
                ->paid()
                ->for($provider)
                ->create();
        }
    }

    /**
     * Create overdue invoices
     */
    protected function createOverdueInvoices($providers): void
    {
        $this->command->info('Creating overdue invoices...');

        foreach ($providers->take(6) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(1, 4))
                ->overdue()
                ->for($provider)
                ->create();
        }
    }

    /**
     * Create cancelled invoices
     */
    protected function createCancelledInvoices($providers): void
    {
        $this->command->info('Creating cancelled invoices...');

        foreach ($providers->take(4) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(1, 2))
                ->cancelled()
                ->for($provider)
                ->create();
        }
    }

    /**
     * Create disputed invoices
     */
    protected function createDisputedInvoices($providers): void
    {
        $this->command->info('Creating disputed invoices...');

        foreach ($providers->take(3) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(1, 2))
                ->disputed()
                ->for($provider)
                ->create();
        }
    }

    /**
     * Create invoices with different payment terms
     */
    protected function createInvoicesWithPaymentTerms($providers): void
    {
        $this->command->info('Creating invoices with different payment terms...');

        // Net 30 invoices
        foreach ($providers->take(5) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(1, 3))
                ->net30()
                ->sent()
                ->for($provider)
                ->create();
        }

        // Net 60 invoices
        foreach ($providers->take(4) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(1, 2))
                ->net60()
                ->sent()
                ->for($provider)
                ->create();
        }

        // Net 90 invoices
        foreach ($providers->take(3) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(1, 2))
                ->net90()
                ->sent()
                ->for($provider)
                ->create();
        }

        // Immediate payment invoices
        foreach ($providers->take(2) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(1, 2))
                ->immediatePayment()
                ->sent()
                ->for($provider)
                ->create();
        }
    }

    /**
     * Create invoices with different value ranges
     */
    protected function createInvoicesWithValueRanges($providers): void
    {
        $this->command->info('Creating invoices with different value ranges...');

        // High value invoices
        foreach ($providers->take(3) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(1, 2))
                ->highValue()
                ->sent()
                ->for($provider)
                ->create();
        }

        // Low value invoices
        foreach ($providers->take(5) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(2, 4))
                ->lowValue()
                ->sent()
                ->for($provider)
                ->create();
        }
    }

    /**
     * Create invoices for specific scenarios
     */
    protected function createScenarioInvoices($providers): void
    {
        $this->command->info('Creating scenario-specific invoices...');

        // Create some invoices that are due soon
        foreach ($providers->take(4) as $provider) {
            $dueSoonDate = now()->addDays(rand(1, 7));

            ProviderInvoice::factory()
                ->count(rand(1, 2))
                ->sent()
                ->for($provider)
                ->state([
                    'due_date' => $dueSoonDate,
                    'status' => 'sent'
                ])
                ->create();
        }

        // Create some invoices with attachments
        foreach ($providers->take(3) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(1, 2))
                ->sent()
                ->for($provider)
                ->state([
                    'attachments' => ['invoice.pdf', 'receipt.pdf', 'contract.pdf']
                ])
                ->create();
        }

        // Create some invoices with notes
        foreach ($providers->take(4) as $provider) {
            ProviderInvoice::factory()
                ->count(rand(1, 2))
                ->sent()
                ->for($provider)
                ->state([
                    'notes' => 'This invoice includes additional services and materials as requested by the client.'
                ])
                ->create();
        }
    }
}

