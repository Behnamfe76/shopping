<?php

namespace Fereydooni\Shopping\App\Actions\ProviderInvoice;

use Carbon\Carbon;
use Fereydooni\Shopping\App\DTOs\ProviderInvoiceDTO;
use Fereydooni\Shopping\App\Enums\InvoiceStatus;
use Fereydooni\Shopping\App\Enums\PaymentTerms;
use Fereydooni\Shopping\App\Repositories\Interfaces\ProviderInvoiceRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreateProviderInvoiceAction
{
    protected ProviderInvoiceRepositoryInterface $repository;

    public function __construct(ProviderInvoiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Execute the action to create a new provider invoice
     */
    public function execute(array $data): ProviderInvoiceDTO
    {
        // Validate input data
        $this->validateData($data);

        // Prepare invoice data
        $invoiceData = $this->prepareInvoiceData($data);

        // Create the invoice
        $invoice = $this->repository->create($invoiceData);

        // Log the action
        Log::info('Provider invoice created', [
            'invoice_id' => $invoice->id,
            'provider_id' => $invoice->provider_id,
            'invoice_number' => $invoice->invoice_number,
            'total_amount' => $invoice->total_amount,
        ]);

        // Return DTO
        return ProviderInvoiceDTO::fromModel($invoice);
    }

    /**
     * Validate the input data
     */
    protected function validateData(array $data): void
    {
        $rules = [
            'provider_id' => 'required|integer|exists:providers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'discount_amount' => 'required|numeric|min:0',
            'shipping_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'payment_terms' => 'required|string|in:'.implode(',', PaymentTerms::all()),
            'payment_method' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
            'attachments' => 'nullable|array',
        ];

        $messages = [
            'provider_id.required' => 'Provider is required.',
            'provider_id.exists' => 'Selected provider does not exist.',
            'invoice_date.required' => 'Invoice date is required.',
            'due_date.required' => 'Due date is required.',
            'due_date.after_or_equal' => 'Due date must be after or equal to invoice date.',
            'subtotal.required' => 'Subtotal is required.',
            'subtotal.min' => 'Subtotal must be greater than or equal to 0.',
            'tax_amount.required' => 'Tax amount is required.',
            'tax_amount.min' => 'Tax amount must be greater than or equal to 0.',
            'discount_amount.required' => 'Discount amount is required.',
            'discount_amount.min' => 'Discount amount must be greater than or equal to 0.',
            'shipping_amount.required' => 'Shipping amount is required.',
            'shipping_amount.min' => 'Shipping amount must be greater than or equal to 0.',
            'currency.required' => 'Currency is required.',
            'currency.max' => 'Currency must not exceed 3 characters.',
            'payment_terms.required' => 'Payment terms are required.',
            'payment_terms.in' => 'Invalid payment terms selected.',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }
    }

    /**
     * Prepare invoice data for creation
     */
    protected function prepareInvoiceData(array $data): array
    {
        // Generate invoice number if not provided
        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = $this->repository->generateInvoiceNumber();
        }

        // Set default status
        $data['status'] = InvoiceStatus::DRAFT->value;

        // Calculate total amount
        $data['total_amount'] = $this->calculateTotalAmount($data);

        // Set default currency if not provided
        if (empty($data['currency'])) {
            $data['currency'] = 'USD';
        }

        // Parse dates
        $data['invoice_date'] = Carbon::parse($data['invoice_date'])->format('Y-m-d');
        $data['due_date'] = Carbon::parse($data['due_date'])->format('Y-m-d');

        // Set timestamps
        $data['created_at'] = now();
        $data['updated_at'] = now();

        return $data;
    }

    /**
     * Calculate total amount from components
     */
    protected function calculateTotalAmount(array $data): float
    {
        $subtotal = $data['subtotal'] ?? 0;
        $taxAmount = $data['tax_amount'] ?? 0;
        $discountAmount = $data['discount_amount'] ?? 0;
        $shippingAmount = $data['shipping_amount'] ?? 0;

        return $subtotal + $taxAmount + $shippingAmount - $discountAmount;
    }

    /**
     * Validate invoice number uniqueness
     */
    protected function validateInvoiceNumber(string $invoiceNumber): bool
    {
        return $this->repository->isInvoiceNumberUnique($invoiceNumber);
    }

    /**
     * Calculate due date based on payment terms
     */
    protected function calculateDueDate(string $invoiceDate, string $paymentTerms): string
    {
        $date = Carbon::parse($invoiceDate);

        switch ($paymentTerms) {
            case PaymentTerms::IMMEDIATE->value:
                return $date->format('Y-m-d');
            case PaymentTerms::NET_15->value:
                return $date->addDays(15)->format('Y-m-d');
            case PaymentTerms::NET_30->value:
                return $date->addDays(30)->format('Y-m-d');
            case PaymentTerms::NET_45->value:
                return $date->addDays(45)->format('Y-m-d');
            case PaymentTerms::NET_60->value:
                return $date->addDays(60)->format('Y-m-d');
            case PaymentTerms::NET_90->value:
                return $date->addDays(90)->format('Y-m-d');
            default:
                return $date->addDays(30)->format('Y-m-d');
        }
    }
}
