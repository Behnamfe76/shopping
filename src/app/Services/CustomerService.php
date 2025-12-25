<?php

namespace Fereydooni\Shopping\app\Services;

use App\Models\User;
use Fereydooni\Shopping\app\DTOs\CustomerDTO;
use Fereydooni\Shopping\app\Enums\CustomerStatus;
use Fereydooni\Shopping\app\Enums\CustomerType;
use Fereydooni\Shopping\app\Models\Customer;
use Fereydooni\Shopping\app\Repositories\Interfaces\CustomerRepositoryInterface;
use Fereydooni\Shopping\app\Traits\HasCrudOperations;
use Fereydooni\Shopping\app\Traits\HasCustomerLoyaltyManagement;
use Fereydooni\Shopping\app\Traits\HasCustomerOperations;
use Fereydooni\Shopping\app\Traits\HasCustomerStatusManagement;
use Fereydooni\Shopping\app\Traits\HasNotesManagement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerService
{
    use HasCrudOperations;
    // use HasCustomerOperations;
    // use HasCustomerStatusManagement;
    // use HasCustomerLoyaltyManagement;
    // use HasNotesManagement;

    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {
        $this->model = Customer::class;
        $this->dtoClass = CustomerDTO::class;
    }

    // Customer-specific methods that extend the traits

    /**
     * Register a new customer
     */
    public function registerCustomer(array $data): CustomerDTO
    {
        // Set default status for new registrations
        $data['status'] = CustomerStatus::PENDING;

        return $this->createCustomer($data);
    }

    /**
     * Update customer profile
     */
    public function updateCustomerProfile(Customer $customer, array $data): CustomerDTO
    {
        // Remove sensitive fields that shouldn't be updated via profile
        unset($data['user_id'], $data['customer_number'], $data['status']);

        return $this->updateCustomer($customer, $data);
    }

    /**
     * Update customer contact information
     */
    public function updateContactInfo(Customer $customer, array $contactData): CustomerDTO
    {
        $allowedFields = ['email', 'phone', 'first_name', 'last_name'];
        $data = array_intersect_key($contactData, array_flip($allowedFields));

        return $this->updateCustomer($customer, $data);
    }

    /**
     * Get customer by user ID
     */
    public function getCustomerByUserId(int $userId): ?CustomerDTO
    {
        return $this->findByUserIdDTO($userId);
    }

    /**
     * Get customer by email
     */
    public function getCustomerByEmail(string $email): ?CustomerDTO
    {
        return $this->findByEmailDTO($email);
    }

    /**
     * Get customer by phone
     */
    public function getCustomerByPhone(string $phone): ?CustomerDTO
    {
        return $this->findByPhoneDTO($phone);
    }

    /**
     * Get customer by customer number
     */
    public function getCustomerByNumber(string $customerNumber): ?CustomerDTO
    {
        return $this->findByCustomerNumberDTO($customerNumber);
    }

    /**
     * Get customers by type
     */
    public function getCustomersByType(string $type): Collection
    {
        return $this->findByTypeDTO($type);
    }

    /**
     * Get customers by status
     */
    public function getCustomersByStatus(string $status): Collection
    {
        return $this->findByStatusDTO($status);
    }

    /**
     * Get active customers
     */
    public function getActiveCustomers(): Collection
    {
        return $this->findActiveDTO();
    }

    /**
     * Get inactive customers
     */
    public function getInactiveCustomers(): Collection
    {
        return $this->findInactiveDTO();
    }

    /**
     * Get paginated customers
     */
    public function getPaginatedCustomers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->paginate($perPage);
    }

    /**
     * Get simple paginated customers
     */
    public function getSimplePaginatedCustomers(int $perPage = 15): Paginator
    {
        return $this->simplePaginate($perPage);
    }

    /**
     * Get cursor paginated customers
     */
    public function getCursorPaginatedCustomers(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->cursorPaginate($perPage, $cursor);
    }

    /**
     * Search customers
     */
    public function searchCustomers(string $query): Collection
    {
        return $this->searchDTO($query);
    }

    /**
     * Get customer DTO
     */
    public function getCustomerDTO(int $customerId): ?CustomerDTO
    {
        return $this->findDTO($customerId);
    }

    /**
     * Get customer preferences
     */
    public function getPreferences(int $customerId): array
    {
        return $this->getCustomerPreferences($customerId);
    }

    /**
     * Update preferences
     */
    public function updatePreferences(Customer $customer, array $preferences): bool
    {
        return $this->updateCustomerPreferences($customer, $preferences);
    }

    /**
     * Add customer note
     */
    public function addCustomerNote(Customer $customer, string $note, string $type = 'general'): bool
    {
        return $this->addCustomerNote($customer, $note, $type);
    }

    /**
     * Get customer notes
     */
    public function getCustomerNotes(Customer $customer): Collection
    {
        return $this->getCustomerNotes($customer);
    }

    // Override trait methods for Customer-specific logic

    /**
     * Override create method to handle customer-specific logic
     */
    public function create(array $data): Customer
    {
        try {
            DB::beginTransaction();
            $password = $data['password'] ?? Str::random(12);
            $userData = [
                'name' => $data['first_name'].' '.$data['last_name'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make($password),
            ];
            $user = User::firstOrCreate(['email' => $userData['email']], $userData);

            // Set default values
            $data['user_id'] = $user->id;
            $data['status'] = $data['status'] ?? CustomerStatus::PENDING;
            $data['customer_type'] = $data['customer_type'] ?? CustomerType::INDIVIDUAL;
            $data['loyalty_points'] = $data['loyalty_points'] ?? 0;
            $data['total_orders'] = $data['total_orders'] ?? 0;
            $data['total_spent'] = $data['total_spent'] ?? 0;
            $data['average_order_value'] = $data['average_order_value'] ?? 0;
            $data['marketing_consent'] = $data['marketing_consent'] ?? false;
            $data['newsletter_subscription'] = $data['newsletter_subscription'] ?? false;
            $data['address_count'] = $data['address_count'] ?? 0;
            $data['order_count'] = $data['order_count'] ?? 0;
            $data['review_count'] = $data['review_count'] ?? 0;
            $data['wishlist_count'] = $data['wishlist_count'] ?? 0;

            $this->validateData($data);
            $customer = $this->repository->create($data);

            DB::commit();

            return $customer;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Override createDTO method to handle customer-specific logic
     */
    public function createDTO(array $data): CustomerDTO
    {
        $customer = $this->create($data);

        return CustomerDTO::fromModel($customer);
    }

    /**
     * Override updateDTO method to handle customer-specific logic
     */
    public function updateDTO(Customer $customer, array $data): ?CustomerDTO
    {
        $updated = $this->update($customer, $data);

        return $updated ? CustomerDTO::fromModel($customer->fresh()) : null;
    }

    /**
     * Override delete method to handle customer-specific logic
     */
    public function delete(Customer $customer): bool
    {
        // Check if customer can be deleted
        if ($customer->total_orders > 0) {
            throw new \InvalidArgumentException('Cannot delete customer with existing orders.');
        }

        if ($customer->loyalty_points > 0) {
            throw new \InvalidArgumentException('Cannot delete customer with loyalty points.');
        }

        $result = $this->repository->delete($customer);

        if ($result) {
            // Fire customer deleted event
            event(new \Fereydooni\Shopping\app\Events\Customer\CustomerDeleted($customer));
        }

        return $result;
    }

    /**
     * Override getSearchableFields method for Customer-specific fields
     */
    protected function getSearchableFields(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'phone',
            'customer_number',
            'company_name',
        ];
    }

    /**
     * Override convertToDTO method for Customer-specific conversion
     */
    protected function convertToDTO(object $item): CustomerDTO
    {
        return CustomerDTO::fromModel($item);
    }

    /**
     * Override getDtoClass method for Customer-specific DTO
     */
    protected function getDtoClass(): string
    {
        return CustomerDTO::class;
    }
}
