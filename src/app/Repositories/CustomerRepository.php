<?php

namespace Fereydooni\Shopping\app\Repositories;

use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\DTOs\CustomerDTO;
use Fereydooni\Shopping\app\Repositories\Interfaces\CustomerRepositoryInterface;
use Fereydooni\Shopping\app\Enums\CustomerStatus;
use Fereydooni\Shopping\app\Enums\CustomerType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function all(): Collection
    {
        return Customer::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Customer::paginate($perPage);
    }

    public function simplePaginate(int $perPage = 15): Paginator
    {
        return Customer::simplePaginate($perPage);
    }

    public function cursorPaginate(int $perPage = 15, string $cursor = null): CursorPaginator
    {
        return Customer::cursorPaginate($perPage, ['*'], 'id', $cursor);
    }

    public function find(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function findDTO(int $id): ?CustomerDTO
    {
        $customer = $this->find($id);
        return $customer ? CustomerDTO::fromModel($customer) : null;
    }

    public function findByUserId(int $userId): ?Customer
    {
        return Customer::where('user_id', $userId)->first();
    }

    public function findByUserIdDTO(int $userId): ?CustomerDTO
    {
        $customer = $this->findByUserId($userId);
        return $customer ? CustomerDTO::fromModel($customer) : null;
    }

    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    public function findByEmailDTO(string $email): ?CustomerDTO
    {
        $customer = $this->findByEmail($email);
        return $customer ? CustomerDTO::fromModel($customer) : null;
    }

    public function findByPhone(string $phone): ?Customer
    {
        return Customer::where('phone', $phone)->first();
    }

    public function findByPhoneDTO(string $phone): ?CustomerDTO
    {
        $customer = $this->findByPhone($phone);
        return $customer ? CustomerDTO::fromModel($customer) : null;
    }

    public function findByCustomerNumber(string $customerNumber): ?Customer
    {
        return Customer::where('customer_number', $customerNumber)->first();
    }

    public function findByCustomerNumberDTO(string $customerNumber): ?CustomerDTO
    {
        $customer = $this->findByCustomerNumber($customerNumber);
        return $customer ? CustomerDTO::fromModel($customer) : null;
    }

    public function findByStatus(string $status): Collection
    {
        return Customer::where('status', $status)->get();
    }

    public function findByStatusDTO(string $status): Collection
    {
        return Customer::where('status', $status)->get()->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function findByType(string $type): Collection
    {
        return Customer::where('customer_type', $type)->get();
    }

    public function findByTypeDTO(string $type): Collection
    {
        return Customer::where('customer_type', $type)->get()->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function findActive(): Collection
    {
        return Customer::where('status', CustomerStatus::ACTIVE)->get();
    }

    public function findActiveDTO(): Collection
    {
        return Customer::where('status', CustomerStatus::ACTIVE)->get()->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function findInactive(): Collection
    {
        return Customer::where('status', CustomerStatus::INACTIVE)->get();
    }

    public function findInactiveDTO(): Collection
    {
        return Customer::where('status', CustomerStatus::INACTIVE)->get()->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return Customer::whereBetween('created_at', [$startDate, $endDate])->get();
    }

    public function findByDateRangeDTO(string $startDate, string $endDate): Collection
    {
        return Customer::whereBetween('created_at', [$startDate, $endDate])->get()->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function findByLoyaltyPointsRange(int $minPoints, int $maxPoints): Collection
    {
        return Customer::whereBetween('loyalty_points', [$minPoints, $maxPoints])->get();
    }

    public function findByLoyaltyPointsRangeDTO(int $minPoints, int $maxPoints): Collection
    {
        return Customer::whereBetween('loyalty_points', [$minPoints, $maxPoints])->get()->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function findByTotalSpentRange(float $minSpent, float $maxSpent): Collection
    {
        return Customer::whereBetween('total_spent', [$minSpent, $maxSpent])->get();
    }

    public function findByTotalSpentRangeDTO(float $minSpent, float $maxSpent): Collection
    {
        return Customer::whereBetween('total_spent', [$minSpent, $maxSpent])->get()->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function findByOrderCountRange(int $minOrders, int $maxOrders): Collection
    {
        return Customer::whereBetween('order_count', [$minOrders, $maxOrders])->get();
    }

    public function findByOrderCountRangeDTO(int $minOrders, int $maxOrders): Collection
    {
        return Customer::whereBetween('order_count', [$minOrders, $maxOrders])->get()->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function createAndReturnDTO(array $data): CustomerDTO
    {
        $customer = $this->create($data);
        return CustomerDTO::fromModel($customer);
    }

    public function update(Customer $customer, array $data): bool
    {
        return $customer->update($data);
    }

    public function updateAndReturnDTO(Customer $customer, array $data): ?CustomerDTO
    {
        $updated = $this->update($customer, $data);
        return $updated ? CustomerDTO::fromModel($customer->fresh()) : null;
    }

    public function delete(Customer $customer): bool
    {
        return $customer->delete();
    }

    public function activate(Customer $customer): bool
    {
        return $customer->activate();
    }

    public function deactivate(Customer $customer): bool
    {
        return $customer->deactivate();
    }

    public function suspend(Customer $customer, string $reason = null): bool
    {
        $data = ['status' => CustomerStatus::SUSPENDED];
        if ($reason) {
            $data['notes'] = $customer->notes . "\nSuspended: " . $reason;
        }
        return $this->update($customer, $data);
    }

    public function unsuspend(Customer $customer): bool
    {
        return $this->update($customer, ['status' => CustomerStatus::ACTIVE]);
    }

    public function addLoyaltyPoints(Customer $customer, int $points, string $reason = null): bool
    {
        $newPoints = $customer->loyalty_points + $points;
        $data = ['loyalty_points' => $newPoints];

        if ($reason) {
            $data['notes'] = $customer->notes . "\nLoyalty points added: +{$points} ({$reason})";
        }

        return $this->update($customer, $data);
    }

    public function deductLoyaltyPoints(Customer $customer, int $points, string $reason = null): bool
    {
        $newPoints = max(0, $customer->loyalty_points - $points);
        $data = ['loyalty_points' => $newPoints];

        if ($reason) {
            $data['notes'] = $customer->notes . "\nLoyalty points deducted: -{$points} ({$reason})";
        }

        return $this->update($customer, $data);
    }

    public function resetLoyaltyPoints(Customer $customer): bool
    {
        return $this->update($customer, ['loyalty_points' => 0]);
    }

    public function getCustomerCount(): int
    {
        return Customer::count();
    }

    public function getCustomerCountByStatus(string $status): int
    {
        return Customer::where('status', $status)->count();
    }

    public function getCustomerCountByType(string $type): int
    {
        return Customer::where('customer_type', $type)->count();
    }

    public function getActiveCustomerCount(): int
    {
        return Customer::where('status', CustomerStatus::ACTIVE)->count();
    }

    public function getInactiveCustomerCount(): int
    {
        return Customer::where('status', CustomerStatus::INACTIVE)->count();
    }

    public function getTotalLoyaltyPoints(): int
    {
        return Customer::sum('loyalty_points');
    }

    public function getAverageLoyaltyPoints(): float
    {
        return Customer::avg('loyalty_points') ?? 0;
    }

    public function getTotalCustomerSpending(): float
    {
        return Customer::sum('total_spent');
    }

    public function getAverageCustomerSpending(): float
    {
        return Customer::avg('total_spent') ?? 0;
    }

    public function search(string $query): Collection
    {
        return Customer::where(function ($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%")
              ->orWhere('customer_number', 'like', "%{$query}%")
              ->orWhere('company_name', 'like', "%{$query}%");
        })->get();
    }

    public function searchDTO(string $query): Collection
    {
        return $this->search($query)->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function searchByCompany(string $companyName): Collection
    {
        return Customer::where('company_name', 'like', "%{$companyName}%")->get();
    }

    public function searchByCompanyDTO(string $companyName): Collection
    {
        return $this->searchByCompany($companyName)->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function getTopSpenders(int $limit = 10): Collection
    {
        return Customer::orderBy('total_spent', 'desc')->limit($limit)->get();
    }

    public function getTopSpendersDTO(int $limit = 10): Collection
    {
        return $this->getTopSpenders($limit)->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function getMostLoyal(int $limit = 10): Collection
    {
        return Customer::orderBy('loyalty_points', 'desc')->limit($limit)->get();
    }

    public function getMostLoyalDTO(int $limit = 10): Collection
    {
        return $this->getMostLoyal($limit)->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function getNewestCustomers(int $limit = 10): Collection
    {
        return Customer::orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function getNewestCustomersDTO(int $limit = 10): Collection
    {
        return $this->getNewestCustomers($limit)->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function getOldestCustomers(int $limit = 10): Collection
    {
        return Customer::orderBy('created_at', 'asc')->limit($limit)->get();
    }

    public function getOldestCustomersDTO(int $limit = 10): Collection
    {
        return $this->getOldestCustomers($limit)->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function getCustomersWithBirthdayThisMonth(): Collection
    {
        return Customer::whereMonth('date_of_birth', now()->month)->get();
    }

    public function getCustomersWithBirthdayThisMonthDTO(): Collection
    {
        return $this->getCustomersWithBirthdayThisMonth()->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function getCustomersByMarketingConsent(bool $consent): Collection
    {
        return Customer::where('marketing_consent', $consent)->get();
    }

    public function getCustomersByMarketingConsentDTO(bool $consent): Collection
    {
        return $this->getCustomersByMarketingConsent($consent)->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function getCustomersByNewsletterSubscription(bool $subscribed): Collection
    {
        return Customer::where('newsletter_subscription', $subscribed)->get();
    }

    public function getCustomersByNewsletterSubscriptionDTO(bool $subscribed): Collection
    {
        return $this->getCustomersByNewsletterSubscription($subscribed)->map(fn($customer) => CustomerDTO::fromModel($customer));
    }

    public function validateCustomer(array $data): bool
    {
        $rules = [
            'user_id' => 'required|integer|exists:users,id',
            'customer_number' => 'required|string|max:50|unique:customers,customer_number',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:' . implode(',', ['male', 'female', 'other', 'prefer_not_to_say']),
            'company_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:100',
            'customer_type' => 'required|in:' . implode(',', array_column(CustomerType::cases(), 'value')),
            'status' => 'required|in:' . implode(',', array_column(CustomerStatus::cases(), 'value')),
        ];

        $validator = validator($data, $rules);
        return !$validator->fails();
    }

    public function generateCustomerNumber(): string
    {
        do {
            $number = 'CUST' . strtoupper(Str::random(8));
        } while (!$this->isCustomerNumberUnique($number));

        return $number;
    }

    public function isCustomerNumberUnique(string $customerNumber): bool
    {
        return !Customer::where('customer_number', $customerNumber)->exists();
    }

    public function getCustomerStats(): array
    {
        return [
            'total_customers' => $this->getCustomerCount(),
            'active_customers' => $this->getActiveCustomerCount(),
            'inactive_customers' => $this->getInactiveCustomerCount(),
            'total_loyalty_points' => $this->getTotalLoyaltyPoints(),
            'average_loyalty_points' => $this->getAverageLoyaltyPoints(),
            'total_spending' => $this->getTotalCustomerSpending(),
            'average_spending' => $this->getAverageCustomerSpending(),
        ];
    }

    public function getCustomerStatsByStatus(): array
    {
        $stats = [];
        foreach (CustomerStatus::cases() as $status) {
            $stats[$status->value] = $this->getCustomerCountByStatus($status->value);
        }
        return $stats;
    }

    public function getCustomerStatsByType(): array
    {
        $stats = [];
        foreach (CustomerType::cases() as $type) {
            $stats[$type->value] = $this->getCustomerCountByType($type->value);
        }
        return $stats;
    }

    public function getCustomerGrowthStats(string $period = 'monthly'): array
    {
        $query = Customer::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date');

        if ($period === 'weekly') {
            $query->selectRaw('YEARWEEK(created_at) as week, COUNT(*) as count')
                  ->groupBy('week');
        } elseif ($period === 'monthly') {
            $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                  ->groupBy('month');
        }

        return $query->get()->toArray();
    }

    public function getCustomerRetentionStats(): array
    {
        // This would require more complex logic based on order history
        // For now, returning basic stats
        return [
            'repeat_customers' => Customer::where('order_count', '>', 1)->count(),
            'one_time_customers' => Customer::where('order_count', 1)->count(),
            'loyal_customers' => Customer::where('loyalty_points', '>', 100)->count(),
        ];
    }

    public function getCustomerLifetimeValue(int $customerId): float
    {
        $customer = $this->find($customerId);
        if (!$customer) {
            return 0;
        }

        // Basic LTV calculation: total_spent + (loyalty_points * conversion_rate)
        $conversionRate = 0.01; // 1 point = $0.01
        return $customer->total_spent + ($customer->loyalty_points * $conversionRate);
    }

    public function getCustomerOrderHistory(int $customerId): Collection
    {
        $customer = $this->find($customerId);
        return $customer ? $customer->orders : collect();
    }

    public function getCustomerAddresses(int $customerId): Collection
    {
        $customer = $this->find($customerId);
        return $customer ? $customer->addresses : collect();
    }

    public function getCustomerReviews(int $customerId): Collection
    {
        // This would depend on the relationship between customers and reviews
        // For now, returning empty collection
        return collect();
    }

    public function getCustomerWishlist(int $customerId): Collection
    {
        // This would depend on the wishlist implementation
        // For now, returning empty collection
        return collect();
    }

    public function addCustomerNote(Customer $customer, string $note, string $type = 'general'): bool
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $formattedNote = "[{$timestamp}] [{$type}] {$note}";

        $currentNotes = $customer->notes ?? '';
        $newNotes = $currentNotes ? $currentNotes . "\n" . $formattedNote : $formattedNote;

        return $this->update($customer, ['notes' => $newNotes]);
    }

    public function getCustomerNotes(Customer $customer): Collection
    {
        // Parse notes into structured format
        $notes = [];
        if ($customer->notes) {
            $lines = explode("\n", $customer->notes);
            foreach ($lines as $line) {
                if (preg_match('/\[(.*?)\] \[(.*?)\] (.*)/', $line, $matches)) {
                    $notes[] = [
                        'timestamp' => $matches[1],
                        'type' => $matches[2],
                        'note' => $matches[3]
                    ];
                }
            }
        }

        return collect($notes);
    }

    public function updateCustomerPreferences(Customer $customer, array $preferences): bool
    {
        $allowedPreferences = [
            'preferred_payment_method',
            'preferred_shipping_method',
            'marketing_consent',
            'newsletter_subscription'
        ];

        $data = array_intersect_key($preferences, array_flip($allowedPreferences));
        return $this->update($customer, $data);
    }

    public function getCustomerPreferences(int $customerId): array
    {
        $customer = $this->find($customerId);
        if (!$customer) {
            return [];
        }

        return [
            'preferred_payment_method' => $customer->preferred_payment_method,
            'preferred_shipping_method' => $customer->preferred_shipping_method,
            'marketing_consent' => $customer->marketing_consent,
            'newsletter_subscription' => $customer->newsletter_subscription,
        ];
    }
}
