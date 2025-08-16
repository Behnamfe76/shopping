<?php

namespace Fereydooni\Shopping\app\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Collection all()
 * @method static \Illuminate\Database\Eloquent\Collection allDTO()
 * @method static \Illuminate\Pagination\LengthAwarePaginator paginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\Paginator simplePaginate(int $perPage = 15)
 * @method static \Illuminate\Pagination\CursorPaginator cursorPaginate(int $perPage = 15, string $cursor = null)
 * @method static \Fereydooni\Shopping\app\Models\Order|null find(int $id)
 * @method static \Fereydooni\Shopping\app\DTOs\OrderDTO|null findDTO(int $id)
 * @method static \Illuminate\Database\Eloquent\Collection findByUserId(int $userId)
 * @method static \Illuminate\Database\Eloquent\Collection findByUserIdDTO(int $userId)
 * @method static \Illuminate\Database\Eloquent\Collection findByStatus(string $status)
 * @method static \Illuminate\Database\Eloquent\Collection findByStatusDTO(string $status)
 * @method static \Illuminate\Database\Eloquent\Collection findByPaymentStatus(string $paymentStatus)
 * @method static \Illuminate\Database\Eloquent\Collection findByPaymentStatusDTO(string $paymentStatus)
 * @method static \Illuminate\Database\Eloquent\Collection findByDateRange(string $startDate, string $endDate)
 * @method static \Illuminate\Database\Eloquent\Collection findByDateRangeDTO(string $startDate, string $endDate)
 * @method static \Fereydooni\Shopping\app\Models\Order create(array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\OrderDTO createDTO(array $data)
 * @method static bool update(\Fereydooni\Shopping\app\Models\Order $order, array $data)
 * @method static \Fereydooni\Shopping\app\DTOs\OrderDTO|null updateDTO(\Fereydooni\Shopping\app\Models\Order $order, array $data)
 * @method static bool delete(\Fereydooni\Shopping\app\Models\Order $order)
 * @method static bool cancel(\Fereydooni\Shopping\app\Models\Order $order, string $reason = null)
 * @method static \Fereydooni\Shopping\app\DTOs\OrderDTO|null cancelDTO(\Fereydooni\Shopping\app\Models\Order $order, string $reason = null)
 * @method static bool markAsPaid(\Fereydooni\Shopping\app\Models\Order $order)
 * @method static bool markAsShipped(\Fereydooni\Shopping\app\Models\Order $order, string $trackingNumber = null)
 * @method static bool markAsCompleted(\Fereydooni\Shopping\app\Models\Order $order)
 * @method static \Illuminate\Database\Eloquent\Collection getPending()
 * @method static \Illuminate\Database\Eloquent\Collection getPendingDTO()
 * @method static \Illuminate\Database\Eloquent\Collection getShipped()
 * @method static \Illuminate\Database\Eloquent\Collection getShippedDTO()
 * @method static \Illuminate\Database\Eloquent\Collection getCompleted()
 * @method static \Illuminate\Database\Eloquent\Collection getCompletedDTO()
 * @method static \Illuminate\Database\Eloquent\Collection getCancelled()
 * @method static \Illuminate\Database\Eloquent\Collection getCancelledDTO()
 * @method static \Illuminate\Database\Eloquent\Collection getRecent(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getRecentDTO(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getByPaymentMethod(string $paymentMethod)
 * @method static \Illuminate\Database\Eloquent\Collection getByPaymentMethodDTO(string $paymentMethod)
 * @method static \Illuminate\Database\Eloquent\Collection search(string $query)
 * @method static \Illuminate\Database\Eloquent\Collection searchDTO(string $query)
 * @method static int getCount()
 * @method static int getCountByStatus(string $status)
 * @method static int getCountByUserId(int $userId)
 * @method static float getTotalRevenue()
 * @method static float getTotalRevenueByDateRange(string $startDate, string $endDate)
 * @method static bool validate(array $data)
 * @method static array calculateTotals(array $items)
 * @method static bool applyDiscount(\Fereydooni\Shopping\app\Models\Order $order, float $discountAmount, string $discountType = 'fixed')
 * @method static bool removeDiscount(\Fereydooni\Shopping\app\Models\Order $order)
 * @method static bool processPayment(\Fereydooni\Shopping\app\Models\Order $order, string $paymentMethod, float $amount)
 * @method static bool processRefund(\Fereydooni\Shopping\app\Models\Order $order, float $amount, string $reason = null)
 * @method static bool addNote(\Fereydooni\Shopping\app\Models\Order $order, string $note, string $type = 'general')
 * @method static \Illuminate\Database\Eloquent\Collection getNotes(\Fereydooni\Shopping\app\Models\Order $order)
 * @method static array getNotesByType(\Fereydooni\Shopping\app\Models\Order $order, string $type)
 * @method static bool deleteNote(\Fereydooni\Shopping\app\Models\Order $order, int $noteIndex)
 * @method static bool updateNote(\Fereydooni\Shopping\app\Models\Order $order, int $noteIndex, string $note, string $type = null)
 * @method static array getNoteTypes()
 * @method static array getRecentNotes(\Fereydooni\Shopping\app\Models\Order $order, int $limit = 5)
 * @method static array searchNotes(\Fereydooni\Shopping\app\Models\Order $order, string $query)
 */
class Order extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'shopping.order';
    }
}
