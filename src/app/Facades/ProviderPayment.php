<?php

namespace Fereydooni\Shopping\App\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO;
use Fereydooni\Shopping\App\Models\ProviderPayment;

/**
 * @method static \Illuminate\Database\Eloquent\Collection all()
 * @method static \Illuminate\Pagination\LengthAwarePaginator paginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\Paginator simplePaginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static \Fereydooni\Shopping\App\Models\ProviderPayment|null find(int $id)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO|null findDTO(int $id)
 * @method static \Illuminate\Database\Eloquent\Collection findByProviderId(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection findByProviderIdDTO(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection findByInvoiceId(int $invoiceId)
 * @method static \Illuminate\Database\Eloquent\Collection findByInvoiceIdDTO(int $invoiceId)
 * @method static \Fereydooni\Shopping\App\Models\ProviderPayment|null findByPaymentNumber(string $paymentNumber)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO|null findByPaymentNumberDTO(string $paymentNumber)
 * @method static \Illuminate\Database\Eloquent\Collection findByStatus(string $status)
 * @method static \Illuminate\Database\Eloquent\Collection findByStatusDTO(string $status)
 * @method static \Illuminate\Database\Eloquent\Collection findByPaymentMethod(string $paymentMethod)
 * @method static \Illuminate\Database\Eloquent\Collection findByPaymentMethodDTO(string $paymentMethod)
 * @method static \Illuminate\Database\Eloquent\Collection findByDateRange(string $startDate, string $endDate)
 * @method static \Illuminate\Database\Eloquent\Collection findByDateRangeDTO(string $startDate, string $endDate)
 * @method static \Illuminate\Database\Eloquent\Collection findByProviderAndDateRange(int $providerId, string $startDate, string $endDate)
 * @method static \Illuminate\Database\Eloquent\Collection findByProviderAndDateRangeDTO(int $providerId, string $startDate, string $endDate)
 * @method static \Fereydooni\Shopping\App\Models\ProviderPayment|null findByTransactionId(string $transactionId)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO|null findByTransactionIdDTO(string $transactionId)
 * @method static \Illuminate\Database\Eloquent\Collection findByReferenceNumber(string $referenceNumber)
 * @method static \Illuminate\Database\Eloquent\Collection findByReferenceNumberDTO(string $referenceNumber)
 * @method static \Illuminate\Database\Eloquent\Collection findPending()
 * @method static \Illuminate\Database\Eloquent\Collection findPendingDTO()
 * @method static \Illuminate\Database\Eloquent\Collection findProcessed()
 * @method static \Illuminate\Database\Eloquent\Collection findProcessedDTO()
 * @method static \Illuminate\Database\Eloquent\Collection findCompleted()
 * @method static \Illuminate\Database\Eloquent\Collection findCompletedDTO()
 * @method static \Illuminate\Database\Eloquent\Collection findFailed()
 * @method static \Illuminate\Database\Eloquent\Collection findFailedDTO()
 * @method static \Illuminate\Database\Eloquent\Collection findByAmountRange(float $minAmount, float $maxAmount)
 * @method static \Illuminate\Database\Eloquent\Collection findByAmountRangeDTO(float $minAmount, float $maxAmount)
 * @method static \Illuminate\Database\Eloquent\Collection findByCurrency(string $currency)
 * @method static \Illuminate\Database\Eloquent\Collection findByCurrencyDTO(string $currency)
 * @method static \Illuminate\Database\Eloquent\Collection findUnreconciled()
 * @method static \Illuminate\Database\Eloquent\Collection findUnreconciledDTO()
 * @method static \Illuminate\Database\Eloquent\Collection findReconciled()
 * @method static \Illuminate\Database\Eloquent\Collection findReconciledDTO()
 * @method static \Fereydooni\Shopping\App\Models\ProviderPayment create(array $data)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO createAndReturnDTO(array $data)
 * @method static bool update(\Fereydooni\Shopping\App\Models\ProviderPayment $payment, array $data)
 * @method static \Fereydooni\Shopping\App\DTOs\ProviderPaymentDTO|null updateAndReturnDTO(\Fereydooni\Shopping\App\Models\ProviderPayment $payment, array $data)
 * @method static bool delete(\Fereydooni\Shopping\App\Models\ProviderPayment $payment)
 * @method static bool process(\Fereydooni\Shopping\App\Models\ProviderPayment $payment, int $processedBy)
 * @method static bool complete(\Fereydooni\Shopping\App\Models\ProviderPayment $payment)
 * @method static bool fail(\Fereydooni\Shopping\App\Models\ProviderPayment $payment, string $reason = null)
 * @method static bool cancel(\Fereydooni\Shopping\App\Models\ProviderPayment $payment, string $reason = null)
 * @method static bool refund(\Fereydooni\Shopping\App\Models\ProviderPayment $payment, float $refundAmount, string $reason = null)
 * @method static bool reconcile(\Fereydooni\Shopping\App\Models\ProviderPayment $payment, string $reconciliationNotes = null)
 * @method static bool updateAmount(\Fereydooni\Shopping\App\Models\ProviderPayment $payment, float $newAmount)
 * @method static int getProviderPaymentCount(int $providerId)
 * @method static int getProviderPaymentCountByStatus(int $providerId, string $status)
 * @method static int getProviderPaymentCountByMethod(int $providerId, string $paymentMethod)
 * @method static float getProviderTotalPaid(int $providerId, string $startDate = null, string $endDate = null)
 * @method static float getProviderTotalPending(int $providerId)
 * @method static float getProviderTotalFailed(int $providerId)
 * @method static float getProviderAveragePaymentAmount(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getProviderPaymentHistory(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getProviderPaymentHistoryDTO(int $providerId)
 * @method static \Illuminate\Database\Eloquent\Collection getInvoicePaymentHistory(int $invoiceId)
 * @method static \Illuminate\Database\Eloquent\Collection getInvoicePaymentHistoryDTO(int $invoiceId)
 * @method static int getTotalPaymentCount()
 * @method static int getTotalPaymentCountByStatus(string $status)
 * @method static int getTotalPaymentCountByMethod(string $paymentMethod)
 * @method static float getTotalPaidAmount(string $startDate = null, string $endDate = null)
 * @method static float getTotalPendingAmount()
 * @method static float getTotalFailedAmount()
 * @method static float getAveragePaymentAmount()
 * @method static int getPendingPaymentCount()
 * @method static int getCompletedPaymentCount()
 * @method static int getFailedPaymentCount()
 * @method static int getUnreconciledPaymentCount()
 * @method static \Illuminate\Database\Eloquent\Collection searchPayments(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchPaymentsDTO(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchPaymentsByProvider(int $providerId, string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchPaymentsByProviderDTO(int $providerId, string $query)
 * @method static string exportPaymentData(array $filters = [])
 * @method static bool importPaymentData(string $data)
 * @method static array getPaymentStatistics()
 * @method static array getProviderPaymentStatistics(int $providerId)
 * @method static array getPaymentTrends(string $startDate = null, string $endDate = null)
 * @method static string generatePaymentNumber()
 * @method static bool isPaymentNumberUnique(string $paymentNumber)
 * @method static array calculatePaymentTotals(int $paymentId)
 *
 * @see \Fereydooni\Shopping\App\Repositories\ProviderPaymentRepository
 */
class ProviderPaymentFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'provider-payment';
    }

    /**
     * Create a new provider payment using the action.
     */
    public static function createPayment(array $data): ProviderPaymentDTO
    {
        $action = app(\Fereydooni\Shopping\App\Actions\ProviderPayment\CreateProviderPaymentAction::class);
        return $action->execute($data);
    }

    /**
     * Get payments for a specific provider with pagination.
     */
    public static function getProviderPayments(int $providerId, int $perPage = 15): LengthAwarePaginator
    {
        return static::getFacadeRoot()->findByProviderId($providerId)
            ->paginate($perPage);
    }

    /**
     * Get payments for a specific provider as DTOs with pagination.
     */
    public static function getProviderPaymentsDTO(int $providerId, int $perPage = 15): LengthAwarePaginator
    {
        $payments = static::getFacadeRoot()->findByProviderIdDTO($providerId);
        return new LengthAwarePaginator($payments, $payments->count(), $perPage);
    }

    /**
     * Get payments for a specific invoice.
     */
    public static function getInvoicePayments(int $invoiceId): Collection
    {
        return static::getFacadeRoot()->findByInvoiceId($invoiceId);
    }

    /**
     * Get payments for a specific invoice as DTOs.
     */
    public static function getInvoicePaymentsDTO(int $invoiceId): Collection
    {
        return static::getFacadeRoot()->findByInvoiceIdDTO($invoiceId);
    }

    /**
     * Get pending payments for a provider.
     */
    public static function getProviderPendingPayments(int $providerId): Collection
    {
        return static::getFacadeRoot()->findByProviderId($providerId)
            ->where('status', 'pending');
    }

    /**
     * Get completed payments for a provider.
     */
    public static function getProviderCompletedPayments(int $providerId): Collection
    {
        return static::getFacadeRoot()->findByProviderId($providerId)
            ->where('status', 'completed');
    }

    /**
     * Get failed payments for a provider.
     */
    public static function getProviderFailedPayments(int $providerId): Collection
    {
        return static::getFacadeRoot()->findByProviderId($providerId)
            ->where('status', 'failed');
    }

    /**
     * Get payment summary for a provider.
     */
    public static function getProviderPaymentSummary(int $providerId): array
    {
        return [
            'total_count' => static::getProviderPaymentCount($providerId),
            'pending_amount' => static::getProviderTotalPending($providerId),
            'total_paid' => static::getProviderTotalPaid($providerId),
            'failed_amount' => static::getProviderTotalFailed($providerId),
            'average_amount' => static::getProviderAveragePaymentAmount($providerId),
        ];
    }

    /**
     * Get overall payment summary.
     */
    public static function getOverallPaymentSummary(): array
    {
        return [
            'total_count' => static::getTotalPaymentCount(),
            'pending_amount' => static::getTotalPendingAmount(),
            'total_paid' => static::getTotalPaidAmount(),
            'failed_amount' => static::getTotalFailedAmount(),
            'average_amount' => static::getAveragePaymentAmount(),
            'pending_count' => static::getPendingPaymentCount(),
            'completed_count' => static::getCompletedPaymentCount(),
            'failed_count' => static::getFailedPaymentCount(),
            'unreconciled_count' => static::getUnreconciledPaymentCount(),
        ];
    }

    /**
     * Search payments globally.
     */
    public static function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        $payments = static::getFacadeRoot()->searchPayments($query);
        return new LengthAwarePaginator($payments, $payments->count(), $perPage);
    }

    /**
     * Search payments for a specific provider.
     */
    public static function searchByProvider(int $providerId, string $query, int $perPage = 15): LengthAwarePaginator
    {
        $payments = static::getFacadeRoot()->searchPaymentsByProvider($providerId, $query);
        return new LengthAwarePaginator($payments, $payments->count(), $perPage);
    }

    /**
     * Get payments by date range with pagination.
     */
    public static function getPaymentsByDateRange(string $startDate, string $endDate, int $perPage = 15): LengthAwarePaginator
    {
        $payments = static::getFacadeRoot()->findByDateRange($startDate, $endDate);
        return new LengthAwarePaginator($payments, $payments->count(), $perPage);
    }

    /**
     * Get payments by amount range with pagination.
     */
    public static function getPaymentsByAmountRange(float $minAmount, float $maxAmount, int $perPage = 15): LengthAwarePaginator
    {
        $payments = static::getFacadeRoot()->findByAmountRange($minAmount, $maxAmount);
        return new LengthAwarePaginator($payments, $payments->count(), $perPage);
    }

    /**
     * Get payments by status with pagination.
     */
    public static function getPaymentsByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        $payments = static::getFacadeRoot()->findByStatus($status);
        return new LengthAwarePaginator($payments, $payments->count(), $perPage);
    }

    /**
     * Get payments by payment method with pagination.
     */
    public static function getPaymentsByMethod(string $method, int $perPage = 15): LengthAwarePaginator
    {
        $payments = static::getFacadeRoot()->findByPaymentMethod($method);
        return new LengthAwarePaginator($payments, $payments->count(), $perPage);
    }

    /**
     * Get payments by currency with pagination.
     */
    public static function getPaymentsByCurrency(string $currency, int $perPage = 15): LengthAwarePaginator
    {
        $payments = static::getFacadeRoot()->findByCurrency($currency);
        return new LengthAwarePaginator($payments, $payments->count(), $perPage);
    }

    /**
     * Get unreconciled payments with pagination.
     */
    public static function getUnreconciledPayments(int $perPage = 15): LengthAwarePaginator
    {
        $payments = static::getFacadeRoot()->findUnreconciled();
        return new LengthAwarePaginator($payments, $payments->count(), $perPage);
    }

    /**
     * Get reconciled payments with pagination.
     */
    public static function getReconciledPayments(int $perPage = 15): LengthAwarePaginator
    {
        $payments = static::getFacadeRoot()->findReconciled();
        return new LengthAwarePaginator($payments, $payments->count(), $perPage);
    }
}
